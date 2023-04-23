<?php

declare(strict_types=1);

namespace Tests\unit\Domain\Post\Entity;

use App\Application\Command\Post\PostCategory\PostCategoryCommand;
use App\Domain\Post\Entity\PostCategory;
use Codeception\Test\Unit;

class PostCategoryTest extends Unit
{
    public function testNewPostCategory(): void
    {
        $dataDir = codecept_data_dir();
        $dataPostCategory1 = (require "{$dataDir}Post/post_category_list.php")['post_category_1'];
        $postCategory1 = PostCategory::createFromCommand(PostCategoryCommand::createFromData($dataPostCategory1));

        $this->assertNotEmpty($postCategory1);
        $this->assertEquals($postCategory1->getId(), $dataPostCategory1['id']);
        $this->assertEquals($postCategory1->getTitle(), $dataPostCategory1['title']);
        $this->assertEquals($postCategory1->getDescription(), $dataPostCategory1['description']);
    }
}
