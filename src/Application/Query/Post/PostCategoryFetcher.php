<?php

declare(strict_types=1);

namespace App\Application\Query\Post;

use App\Domain\Post\Criteria\PostCategoryCriteria;
use App\Domain\Post\Entity\PostCategory;
use App\Domain\Post\Repository\PostCategoryRepositoryContract;

class PostCategoryFetcher
{
    public function __construct(private PostCategoryRepositoryContract $postCategoryRepository)
    {
    }

    public function getById(string $id): PostCategory
    {
        return $this->postCategoryRepository->getById($id);
    }

    public function hasById(string $id): bool
    {
        return $this->postCategoryRepository->hasById($id);
    }

    public function hasByTitle(string $title): bool
    {
        return $this->postCategoryRepository->hasByTitle($title);
    }

    /**
     * @return PostCategory[]
     */
    public function getList(): array
    {
        return $this->postCategoryRepository->getList();
    }
}
