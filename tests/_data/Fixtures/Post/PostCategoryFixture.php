<?php

declare(strict_types=1);

namespace Tests\_data\Fixtures\Post;

use App\Infrastructure\Domain\Post\Proxy\Symfony\PostCategoryProxy;
use App\Infrastructure\Domain\Post\Proxy\Symfony\PostProxy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostCategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i <= 4; $i++) {
            $postCategoryId = $faker->uuid();
            $dateTimePostCategory = new \DateTimeImmutable($faker->dateTime()->format('Y-m-d H:i:s'));
            $postCategory = new PostCategoryProxy(
                $postCategoryId,
                new ArrayCollection(),
                $faker->text(64),
                $faker->text(),
                $dateTimePostCategory,
                $dateTimePostCategory,
                null,
            );
            $manager->persist($postCategory);
            for ($k = 0; $k <= rand(1000, 5000); $k++) {
                $dateTimePost = new \DateTimeImmutable($faker->dateTime()->format('Y-m-d H:i:s'));
                $post = new PostProxy(
                    $faker->uuid(),
                    $postCategory,
                    $faker->text(),
                    $faker->realText(),
                    $dateTimePost,
                    $dateTimePost,
                    null,
                );
                $postCategory->getPostList()->add($post);
                $manager->persist($post);
            }
        }
        $manager->flush();
    }
}