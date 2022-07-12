<?php

declare(strict_types=1);

namespace App\Infrastructure\Application\Validation\Post\Symfony;

use App\Application\Query\Post\PostCategoryFetcher;
use App\Application\Validation\Post\PostValidation;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SymfonyPostValidation implements PostValidation
{
    public function __construct(
        private ValidatorInterface $validator,
        private PostCategoryFetcher $postCategoryFetcher,
    ) {
    }

    public function validate(array $data): mixed
    {
        return $this->validator->validate(
            $data,
            new Collection(
                [
                    'id' => new Optional(
                        [
                            new Uuid(),
                        ]
                    ),
                    'category_id' => [
                        new Uuid(),
                        new NotBlank(),
                        new Callback([
                            'callback' => function ($object, ExecutionContextInterface $context) {
                                if (!$this->postCategoryFetcher->hasById($object)) {
                                    $context->addViolation('The selected category id is invalid.');
                                }
                            },
                        ]),
                    ],
                    'title' => [
                        new NotBlank(),
                        new Type([
                            'type' => 'string',
                        ]),
                        new Length([
                            'max' => 255,
                        ]),
                    ],
                    'text' => [
                        new NotBlank(),
                        new Type([
                            'type' => 'string',
                        ]),
                    ],
                ],
                allowExtraFields: true,
            )
        );
    }
}
