<?php

declare(strict_types=1);

namespace App\Application\Command\Post\PostCategory;

use Ramsey\Uuid\Uuid;

class PostCategoryCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
    ) {
    }

    /**
     * @param string[] $data
     */
    public static function createFromData(array $data): self
    {
        return new self(
            $data['id'] ?? Uuid::uuid4()->toString(),
            $data['title'],
            $data['description'] ?? null,
        );
    }
}
