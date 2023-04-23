<?php

declare(strict_types=1);

namespace Tests\unit\Domain\Post\Entity;

use App\Application\Command\Post\Post\PostCommand;
use App\Application\Command\Post\PostCategory\PostCategoryCommand;
use App\Domain\Post\Entity\Post;
use App\Domain\Post\Entity\PostCategory;
use Codeception\Test\Unit;

class PostTest extends Unit
{
    public function testNewPost(): void
    {
        $dataDir = codecept_data_dir();
        $dataPostCategory1 = (require "{$dataDir}Post/post_category_list.php")['post_category_1'];
        $postCategory1 = PostCategory::createFromCommand(PostCategoryCommand::createFromData($dataPostCategory1));
        $dataPost1 = (require "{$dataDir}Post/post_list.php")['post_1'];
        $post1 = Post::createFromCommand(PostCommand::createFromData($dataPost1));

        $this->assertNotEmpty($post1);
        $this->assertEquals($post1->getId(), $dataPost1['id']);
        $this->assertEquals($post1->getTitle(), $dataPost1['title']);
        $this->assertEquals($post1->getText(), $dataPost1['text']);
    }
}
