<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Post\Proxy\Symfony;

use Doctrine\Common\Collections\Collection;

class PostCategoryProxy
{
    public function __construct(
        private string $id,
        private Collection $postList,
        private string $title,
        private ?string $description,
        private ?\DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
        private ?\DateTimeImmutable $deletedAt,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPostList(): Collection
    {
        return $this->postList;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
