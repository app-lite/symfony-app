<?php

declare(strict_types=1);

namespace App\Application\Validation\Post;

interface PostCategoryValidation
{
    /**
     * @param string[] $data
     * @return mixed
     */
    public function validate(array $data): mixed;
}
