<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Post\Repository\Symfony;

use App\Domain\Post\Constant\PostCategoryEnum;
use App\Domain\Post\Entity\PostCategory;
use App\Domain\Post\Exception\Post\PostCategory\PostCategoryNotFoundException;
use App\Domain\Post\Exception\Post\PostCategory\PostCategorySaveException;
use App\Domain\Post\Repository\PostCategoryRepositoryContract;
use Doctrine\DBAL\Connection;
use samdark\hydrator\Hydrator;

class SymfonyPostCategoryRepository implements PostCategoryRepositoryContract
{
    private const FIELD_LIST = [
        'id',
        'title',
        'description',
    ];

    private Hydrator $hydrator;
    private string $table = PostCategoryEnum::DB_TABLE;

    public function __construct(private Connection $db)
    {
        $this->hydrator = new Hydrator([
            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
        ]);
    }

    public function getById(string $id): PostCategory
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->eq('id', ':id'))
            ->setParameter('id', $id)
            ->fetchAssociative();

        if (!$dbResult) {
            throw new PostCategoryNotFoundException();
        }

        return $this->hydrateResult($dbResult);
    }

    public function hasByTitle(string $title): bool
    {
        $qb = $this->db->createQueryBuilder();

        return $qb
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->eq('title', ':title'))
            ->setParameter('title', $title)
            ->executeQuery()
            ->rowCount() > 0;
    }

    public function findById(string $id): ?PostCategory
    {
        $qb = $this->db->createQueryBuilder();
        $dbResult = $qb
            ->from($this->table)
            ->select(self::FIELD_LIST)
            ->where($qb->expr()->eq('id', ':id'))
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $dbResult ? $this->hydrateResult($dbResult) : null;
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

    public function save(PostCategory $postCategory): void
    {
        $prepareData = $this->extract($postCategory);
        if ($this->hasById($prepareData['id'])) {
            $prepareData['updated_at'] = new \DateTimeImmutable();
            $prepareData['deleted_at'] = null;
            try {
                if (0 === $this->db->update($this->table, $prepareData, ['id' => $prepareData['id']])) {
                    throw new PostCategorySaveException();
                }
            } catch (\Throwable $e) {
                throw new PostCategorySaveException(previous: $e);
            }
        } else {
            $createAndUpdateDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $prepareData['created_at'] = $createAndUpdateDate;
            $prepareData['updated_at'] = $createAndUpdateDate;
            try {
                if (!$this->db->insert($this->table, $prepareData)) {
                    throw new PostCategorySaveException();
                }
            } catch (\Throwable $e) {
                throw new PostCategorySaveException(previous: $e);
            }
        }
    }

    private function extract(PostCategory $postCategory): array
    {
        return $this->hydrator->extract($postCategory);
    }

    private function hydrateResult(array $dbResult): ?PostCategory
    {
        $result = null;
        if ($dbResult) {
            /** @var PostCategory $result */
            $result = $this->hydrator->hydrate($dbResult, PostCategory::class);
        }

        return $result;
    }
}
