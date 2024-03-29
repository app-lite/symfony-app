<?php

declare(strict_types=1);

namespace App\Application\Validation\Post;

interface PostValidation
{
    /**
     * @param string[] $data
     */
    public function validate(array $data): mixed;
}
