<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Post\Proxy\Symfony;

class PostProxy
{
    public function __construct(
        private string $id,
        private PostCategoryProxy $postCategory,
        private string $title,
        private ?string $text,
        private ?\DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
        private ?\DateTimeImmutable $deletedAt,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPostCategory(): PostCategoryProxy
    {
        return $this->postCategory;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): ?string
    {
        return $this->text;
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
