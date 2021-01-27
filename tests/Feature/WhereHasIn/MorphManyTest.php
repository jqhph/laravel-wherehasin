<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Post;
use Dcat\Laravel\Database\Tests\TestCase;

/**
 * @group morph-many
 */
class MorphManyTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_posts` where exists
         *   (
         *     select * from `comments`
         *     where `test_posts`.`id` = `comments`.`commentable_id` and `comments`.`commentable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         */
        $sql1 = Post::whereHasIn('comments')->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `comments`.`commentable_id` from `comments` where `test_posts`.`id` = `comments`.`commentable_id` and `comments`.`commentable_type` = Dcat\Laravel\Database\Tests\Models\Post)',
            $sql1
        );

        $sql2 = Post::whereHasIn('comments', function ($q) {
            $q->where('id', '>', 10);
        })->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `comments`.`commentable_id` from `comments` where `test_posts`.`id` = `comments`.`commentable_id` and `comments`.`commentable_type` = Dcat\Laravel\Database\Tests\Models\Post and `id` > 10)',
            $sql2
        );
    }
}
