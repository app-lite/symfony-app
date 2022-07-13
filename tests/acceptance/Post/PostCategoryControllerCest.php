<?php

declare(strict_types=1);

namespace Tests\acceptance\Post;

use App\Domain\Post\Constant\PostCategoryEnum;
use App\Domain\Post\Constant\PostEnum;
use App\Tests\AcceptanceTester;
use Faker\Factory;

class PostCategoryControllerCest
{
    public function testFetch(AcceptanceTester $I)
    {
        $I->wantTo('Test fetch category and post');
        $I->havePostCategoryAndPostInDatabase();

        $I->seeInDatabase(PostCategoryEnum::DB_TABLE, ['id' => '00000000-0000-0000-0000-000000000001']);
        $I->seeInDatabase(PostEnum::DB_TABLE, ['id' => '00000000-0000-0000-0000-000000000001']);

        $I->sendGet( '/post');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSee('Home');
        $I->canSeeResponseContains('Category 1');
    }

//    public function testPostCategory()
//    {
//        $a = 0;
//        $postCategoryDataList = [];
//        for ($i = 0; $i <= 2; $i++) {
//            $numberCategory = ++$a;
//            $postCategoryId = Uuid::uuid4();
//            $postCategoryDataList[] = [
//                'id' => $postCategoryId,
//                'title' => "Category {$numberCategory}",
//                'description' => "Description {$numberCategory}",
//                'created_at' => new \DateTimeImmutable(),
//                'updated_at' => new \DateTimeImmutable(),
//            ];
//        }
//    }

    public function testCreatePostCategory(AcceptanceTester $I)
    {
        $I->wantTo('Test created post category');
        $faker = Factory::create();
        $postCategoryId1 = $faker->uuid();
        $postCategoryTitle1 = $faker->text(64);

        $I->sendPost( '/post/category/store', [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
            'description' => null,
        ]);

        $I->canSeeResponseCodeIsSuccessful();
        $I->canSee($postCategoryTitle1);
        $I->seeInDatabase(PostCategoryEnum::DB_TABLE, [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
        ]);
    }

    public function testCreatePostCategoryWithoutTitle(AcceptanceTester $I)
    {
        $faker = Factory::create();
        $postCategoryId1 = $faker->uuid();
        $postCategoryTitle1 = null;

        $I->sendPost( '/post/category/store', [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
        ]);

        $I->canSee('This value should not be blank.');
        $I->canSee('This value is too short. It should have 3 characters or more.');

        $I->dontSeeInDatabase(PostCategoryEnum::DB_TABLE, [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
        ]);
    }

    public function testCreatePostCategoryExistsTitle(AcceptanceTester $I)
    {
        $I->havePostCategoryInDatabase();

        $faker = Factory::create();
        $postCategoryId1 = $faker->uuid();
        $postCategoryTitle1 = 'Category 1';

        $I->sendPost( '/post/category/store', [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
        ]);

        $I->canSee('This value is already used.');

        $I->dontSeeInDatabase(PostCategoryEnum::DB_TABLE, [
            'id' => $postCategoryId1,
            'title' => $postCategoryTitle1,
        ]);
    }
}
