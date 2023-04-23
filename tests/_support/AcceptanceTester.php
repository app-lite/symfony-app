<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Post\Constant\PostCategoryEnum;
use App\Domain\Post\Constant\PostEnum;
use App\Tests\_generated\AcceptanceTesterActions;
use Codeception\Actor;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends Actor
{
    use AcceptanceTesterActions;

    public function havePostCategoryAndPostInDatabase(): void
    {
        $dateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $postCategoryData1 = [
            'id' => '00000000-0000-0000-0000-000000000001',
            'title' => 'Category 1',
            'description' => 'Description 1',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        $this->haveInDatabase(PostCategoryEnum::DB_TABLE, $postCategoryData1);

        $post = [
            'id' => '00000000-0000-0000-0000-000000000001',
            'category_id' => $postCategoryData1['id'],
            'title' => 'Post 1',
            'text' => 'Message 1',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        $this->haveInDatabase(PostEnum::DB_TABLE, $post);
    }

    public function havePostCategoryInDatabase(): void
    {
        $dateTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $postCategoryData1 = [
            'id' => '00000000-0000-0000-0000-000000000001',
            'title' => 'Category 1',
            'description' => 'Description 1',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        $this->haveInDatabase(PostCategoryEnum::DB_TABLE, $postCategoryData1);
    }
}
