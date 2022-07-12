<?php

declare(strict_types=1);

namespace App\Infrastructure\Application\Validation\Post\Symfony;

use App\Application\Query\Post\PostCategoryFetcher;
use App\Application\Validation\Post\PostCategoryValidation;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SymfonyPostCategoryValidation implements PostCategoryValidation
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
                    'title' => [
                        new NotBlank(),
                        new Type([
                            'type' => 'string',
                        ]),
                        new Length([
                            'min' => 3,
                            'max' => 255,
                        ]),
                        new Callback([
                            'callback' => function ($object, ExecutionContextInterface $context) {
                                $checkPostCategory = $this->postCategoryFetcher->hasByTitle($object);
                                if ($checkPostCategory) {
                                    $context->addViolation('This value is already used.');
                                }
                            },
                        ]),
                    ],
                    'description' => [
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
