<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Post;
use Dcat\Laravel\Database\Tests\TestCase;

/**
 * @group morph-one
 */
class MorphOneTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_posts` where exists
         *   (
         *     select * from `test_images`
         *     where `test_posts`.`id` = `test_images`.`imageable_id` and `test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post
         *   )
         */
        $sql1 = Post::whereHasIn('image')->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `test_images`.`imageable_id` from `test_images` where `test_posts`.`id` = `test_images`.`imageable_id` and `test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post)',
            $sql1
        );

        $sql2 = Post::whereHasIn('image', function ($q) {
            $q->where('id', '>', 10);
        })->sql();

        $this->assertEquals(
            'select * from `test_posts` where `test_posts`.`id` in (select `test_images`.`imageable_id` from `test_images` where `test_posts`.`id` = `test_images`.`imageable_id` and `test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post and `id` > 10)',
            $sql2
        );
    }
}
