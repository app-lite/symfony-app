<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Post\Repository\Symfony;

use App\Domain\Post\Constant\PostEnum;
use App\Domain\Post\Criteria\PostCriteria;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Exception\Post\Post\PostNotFoundException;
use App\Domain\Post\Exception\Post\Post\PostSaveException;
use App\Domain\Post\Repository\PostRepositoryContract;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use samdark\hydrator\Hydrator;

class SymfonyPostRepository implements PostRepositoryContract
{
    private const FIELD_LIST = [
        'id',
        'category_id',
        'title',
        'text',
        'created_at',
    ];

    private Hydrator $hydrator;
    private string $table = PostEnum::DB_TABLE;

    public function __construct(private Connection $db)
    {
        $this->hydrator = new Hydrator([
            'id' => 'id',
            'category_id' => 'categoryId',
            'title' => 'title',
            'text' => 'text',
            'created_at' => 'createdAt',
        ]);
    }

    public function getById(string $id): Post
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->eq('id', ':id'))
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$dbResult) {
            throw new PostNotFoundException();
        }

        return $this->hydrateResult($dbResult);
    }

    public function getListByCriteria(PostCriteria $criteria)
    {
        $qb = $this->db->createQueryBuilder();
        $query = $qb
            ->from($this->table)
            ->select(self::FIELD_LIST);

        $this->prepareQueryByCriteria($query, $criteria);

        return array_map(function (array $dbResult) {
            return $this->hydrateResult($dbResult);
        }, $query->fetchAllAssociative());
    }

    private function prepareQueryByCriteria(QueryBuilder $query, PostCriteria $criteria): QueryBuilder
    {
        if (!empty($criteria->getCategoryIdList())) {
            $query->expr()->in('category_id', ':categoryIdList');
            $query->setParameter('categoryIdList', $criteria->getCategoryIdList(), Connection::PARAM_STR_ARRAY);
        }

        if (!empty($criteria->getOrderList())) {
            foreach ($criteria->getOrderList() as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * @return Post[][]
     */
    private function getByLimitLateralGroupByCategoryId(int $limit): array
    {
        $qbSub = $this->db->createQueryBuilder();
        $lateralSub = $qbSub
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qbSub->expr()->eq('t1.category_id', 'post_posts.category_id'))
            ->orderBy('created_at', 'desc')
            ->setMaxResults($limit)->getSQL();

        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb->from("({$this->db->createQueryBuilder()->from($this->table)
            ->select('category_id')
            ->groupBy('category_id')})", 't1')
            ->join('t1', "LATERAL ({$lateralSub})", 't2', $qb->expr()->eq('t1.category_id', 't2.category_id'))
            ->select([
                't2.id',
                't2.category_id',
                't2.title',
                't2.text',
                't2.created_at',
            ])
            ->orderBy('created_at', 'desc')
            ->fetchAllAssociative();

        $result = [];
        foreach ($dbResult as $item) {
            $result[$item['category_id']][$item['id']] = $this->hydrateResult($item);
        }

        return $result;
    }

    /**
     * @return Post[][]
     */
    public function getByLimitWindowFunctionGroupByCategoryId(int $limit): array
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
            ->from("({$this->db->createQueryBuilder()
                ->from($this->table)
                ->select([
                    'id',
                    'category_id',
                    'title',
                    'text',
                    'created_at',
                    'row_number() OVER (
                    PARTITION BY category_id ORDER BY created_at DESC
                ) i',
                ])})", 'posts')
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->lte('i', ':limit'))
            ->setParameter('limit', $limit)
            ->orderBy('created_at', 'desc')
            ->fetchAllAssociative();

        $result = [];
        foreach ($dbResult as $item) {
            $result[$item['category_id']][$item['id']] = $this->hydrateResult($item);
        }

        return $result;
    }

    public function count(): int
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
                ->from($this->table)
                ->select('COUNT(id)');

        return $dbResult->fetchOne();
    }

    public function getListByIdList(array $idList): array
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->in('id', ':idList'))
            ->setParameter('idList', $idList, Connection::PARAM_STR_ARRAY)
            ->fetchAllAssociative();

        return array_map(function (array $item) {
            return $this->hydrateResult($item);
        }, array_column($dbResult, null, 'id'));
    }

    public function getList(): array
    {
        $dbResult = $this->db->createQueryBuilder()
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->fetchAllAssociative();

        return array_map(function (array $dbResult) {
            return $this->hydrateResult($dbResult);
        }, array_column($dbResult, null, 'id'));
    }

    /**
     * @return Post[][]
     */
    public function getByLimitGroupByCategoryId(int $limit): array
    {
        if ($this->db->getDriver()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $result = $this->getByLimitLateralGroupByCategoryId($limit);
        } else {
            $result = $this->getByLimitWindowFunctionGroupByCategoryId($limit);
        }

        return $result;
    }

    public function hasById(string $id): bool
    {
        $qb = $this->db->createQueryBuilder();

        return $qb
                ->from($this->table)
                ->select('COUNT(id)')
                ->where($qb->expr()->eq('id', ':id'))
                ->setParameter('id', $id)
                ->fetchOne() > 0;
    }

    public function save(Post $post): void
    {
        $prepareData = $this->extract($post);
        if ($this->hasById($prepareData['id'])) {
            $prepareData['updated_at'] = new \DateTimeImmutable();
            $prepareData['deleted_at'] = null;
            try {
                if (0 === $this->db->update($this->table, $prepareData, ['id' => $prepareData['id']])) {
                    throw new PostSaveException();
                }
            } catch (\Throwable $e) {
                throw new PostSaveException(previous: $e);
            }
        } else {
            $createAndUpdateDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $prepareData['created_at'] = $createAndUpdateDate;
            $prepareData['updated_at'] = $createAndUpdateDate;
            try {
                if (!$this->db->insert($this->table, $prepareData)) {
                    throw new PostSaveException();
                }
            } catch (\Throwable $e) {
                throw new PostSaveException(previous: $e);
            }
        }
    }

    /**
     * @return string[]
     */
    private function extract(Post $post): array
    {
        return $this->hydrator->extract($post);
    }

    /**
     * @param string[] $dbResult
     *
     * @throws \Exception
     */
    private function hydrateResult(array $dbResult): ?Post
    {
        $result = null;
        if ($dbResult) {
            $dbResult['created_at'] = new \DateTimeImmutable($dbResult['created_at']);
            /** @var Post $result */
            $result = $this->hydrator->hydrate($dbResult, Post::class);
        }

        return $result;
    }
}
