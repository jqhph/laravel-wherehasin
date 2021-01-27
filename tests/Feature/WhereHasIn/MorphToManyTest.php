<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Post;
use Dcat\Laravel\Database\Tests\Models\Tag;
use Dcat\Laravel\Database\Tests\TestCase;

/**
 * @group morph-to-many
 */
class MorphToManyTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_posts` where exists
         *   (
         *     select * from `test_tags` inner join `taggables`
         *     on `test_tags`.`id` = `taggables`.`tag_id`
         *     where `test_posts`.`id` = `taggables`.`taggable_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         *
         * whereHasIn sql.
         *
         * select * from `test_posts` where `test_posts`.`id` in
         *   (
         *     select `taggables`.`taggable_id` from `test_tags` inner join `taggables`
         *     on `test_tags`.`id` = `taggables`.`tag_id`
         *     where `test_posts`.`id` = `taggables`.`taggable_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         */
        $sql1 = Post::whereHasIn('tags')->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `taggables`.`taggable_id` from `test_tags` inner join `taggables` on `test_tags`.`id` = `taggables`.`tag_id` where `test_posts`.`id` = `taggables`.`taggable_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post)',
            $sql1
        );

        $sql2 = Post::whereHasIn('tags', function ($q) {
            $q->where('id', '>', 10);
        })->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `taggables`.`taggable_id` from `test_tags` inner join `taggables` on `test_tags`.`id` = `taggables`.`tag_id` where `test_posts`.`id` = `taggables`.`taggable_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post and `id` > 10)',
            $sql2
        );
    }

    public function testMorphedByMany()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_tags` where exists
         *   (
         *     select * from `test_posts` inner join `taggables`
         *     on `test_posts`.`id` = `taggables`.`taggable_id`
         *     where `test_tags`.`id` = `taggables`.`tag_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         *
         * whereHasIn sql.
         *
         * select * from `test_tags` where `test_tags`.`id` in
         *   (
         *     select `taggables`.`tag_id` from `test_posts` inner join `taggables`
         *     on `test_posts`.`id` = `taggables`.`taggable_id`
         *     where `test_tags`.`id` = `taggables`.`tag_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         */
        $sql1 = Tag::whereHasIn('posts')->sql();

        $this->assertEquals(
            'select * from `test_tags` where `test_tags`.`id` in (select `taggables`.`tag_id` from `test_posts` inner join `taggables` on `test_posts`.`id` = `taggables`.`taggable_id` where `test_tags`.`id` = `taggables`.`tag_id` and `taggables`.`taggable_type` = Dcat\Laravel\Database\Tests\Models\Post)',
            $sql1
        );
    }
}
