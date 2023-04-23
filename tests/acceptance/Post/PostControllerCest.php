<?php

declare(strict_types=1);

namespace Tests\acceptance\Post;

use App\Domain\Post\Constant\PostCategoryEnum;
use App\Domain\Post\Constant\PostEnum;
use App\Tests\AcceptanceTester;
use Faker\Factory;

class PostControllerCest
{
    public function testFetchPost(AcceptanceTester $I): void
    {
        $I->wantTo('Test fetch post');
        $I->havePostCategoryInDatabase();

        $postCategoryTitle1 = 'Category 1';

        $id = $I->grabFromDatabase(PostCategoryEnum::DB_TABLE, 'id', [
            'title' => $postCategoryTitle1,
        ]);

        $dateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $postDataList = [];
        for ($i = 1; $i <= 3; ++$i) {
            $postId = "00000000-0000-0000-0000-00000000000{$i}";
            $postDataList[$i] = [
                'id' => $postId,
                'category_id' => $id,
                'title' => "Post {$i}",
                'text' => "Message {$i}",
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
            ];
            $I->haveInDatabase(PostEnum::DB_TABLE, $postDataList[$i]);
        }

        $I->sendGet('/post');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSee($postCategoryTitle1);
        for ($i = 1; $i <= 3; ++$i) {
            $postId = "00000000-0000-0000-0000-00000000000{$i}";
            $postTitle = "Post {$i}";
            $I->canSee($postTitle);
            $I->seeInDatabase(PostEnum::DB_TABLE, [
                'id' => $postId,
                'title' => $postTitle,
                'category_id' => $id,
            ]);
        }
    }

    public function testCreatePost(AcceptanceTester $I): void
    {
        $I->wantTo('Test created post');

        $faker = Factory::create();

        $dateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $postCategoryId1 = $faker->uuid();
        $postCategoryTitle1 = 'Category 1';
        $postCategoryData1 = [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
            'description' => 'Description 1',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        $I->haveInDatabase(PostCategoryEnum::DB_TABLE, $postCategoryData1);

        $postId1 = $faker->uuid();
        $postTitle1 = $faker->text(64);
        $postText1 = $faker->text();

        $I->sendPost('/post/store', [
            'id' => $postId1,
            'category_id' => $postCategoryData1['id'],
            'title' => $postTitle1,
            'text' => $postText1,
        ]);

        $I->canSeeResponseCodeIsSuccessful();
        $I->canSee($postCategoryTitle1);
        $I->canSee($postTitle1);
        $I->seeInDatabase(PostEnum::DB_TABLE, [
            'id' => $postId1,
            'title' => $postTitle1,
            'category_id' => $postCategoryId1,
        ]);
    }

    public function testCreatePostWithSelectedCategoryIdIsInvalid(AcceptanceTester $I): void
    {
        $faker = Factory::create();
        $postId1 = $faker->uuid();
        $postTitle1 = $faker->text(64);
        $postText1 = $faker->text();

        $I->sendPost('/post/store', [
            'id' => $postId1,
            'category_id' => '00000000-0000-0000-0000-000000000000',
            'title' => $postTitle1,
            'text' => $postText1,
        ]);

        $I->canSee('The selected category id is invalid.');
        $I->dontSeeInDatabase(PostEnum::DB_TABLE, [
            'id' => $postId1,
            'title' => $postTitle1,
        ]);
    }

    public function testCreatePostWithoutCategoryId(AcceptanceTester $I): void
    {
        $faker = Factory::create();
        $postId1 = $faker->uuid();
        $postTitle1 = $faker->text(64);
        $postText1 = $faker->text();

        $I->sendPost('/post/store', [
            'id' => $postId1,
            'title' => $postTitle1,
            'text' => $postText1,
        ]);

        $I->canSee('This field is missing.');
        $I->dontSeeInDatabase(PostEnum::DB_TABLE, [
            'id' => $postId1,
            'title' => $postTitle1,
        ]);
    }

    public function testCreatePostWithoutTitle(AcceptanceTester $I): void
    {
        $I->havePostCategoryInDatabase();

        $postCategoryTitle1 = 'Category 1';

        $id = $I->grabFromDatabase(PostCategoryEnum::DB_TABLE, 'id', [
            'title' => $postCategoryTitle1,
        ]);

        $faker = Factory::create();
        $postId1 = $faker->uuid();
        $postTitle1 = null;
        $postText1 = $faker->text();

        $I->sendPost('/post/store', [
            'id' => $postId1,
            'category_id' => $id,
            'title' => $postTitle1,
            'text' => $postText1,
        ]);

        $I->canSee('This value should not be blank.');

        $I->dontSeeInDatabase(PostEnum::DB_TABLE, [
            'title' => $postTitle1,
            'text' => $postText1,
        ]);
    }

    public function testCreatePostWithoutText(AcceptanceTester $I): void
    {
        $I->havePostCategoryInDatabase();

        $postCategoryTitle1 = 'Category 1';

        $id = $I->grabFromDatabase(PostCategoryEnum::DB_TABLE, 'id', [
            'title' => $postCategoryTitle1,
        ]);

        $faker = Factory::create();
        $postId1 = $faker->uuid();
        $postTitle1 = $faker->text(64);
        $postText1 = null;

        $I->sendPost('/post/store', [
            'id' => $postId1,
            'category_id' => $id,
            'title' => $postTitle1,
            'text' => $postText1,
        ]);

        $I->canSee('This value should not be blank.');

        $I->dontSeeInDatabase(PostEnum::DB_TABLE, [
            'title' => $postTitle1,
            'text' => $postText1,
        ]);
    }
}
