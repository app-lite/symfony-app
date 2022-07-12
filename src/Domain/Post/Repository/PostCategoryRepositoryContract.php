<?php

declare(strict_types=1);

namespace App\Domain\Post\Repository;

use App\Domain\Post\Entity\PostCategory;

interface PostCategoryRepositoryContract
{
    public function findById(string $id): ?PostCategory;

    public function getById(string $id): PostCategory;

    public function hasById(string $id): bool;

    public function hasByTitle(string $title): bool;

    public function getListByIdList(array $idList): array;

    public function getList(): array;

    public function save(PostCategory $postCategory): void;
}
