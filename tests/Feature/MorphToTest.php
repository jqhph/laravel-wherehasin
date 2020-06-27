<?php

namespace Dcat\Laravel\Database\Tests\Feature;

use Dcat\Laravel\Database\Tests\Models\Image;
use Dcat\Laravel\Database\Tests\Models\Post;
use Dcat\Laravel\Database\Tests\TestCase;

/**
 * @group morph-to
 */
class MorphToTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_images` where
         *   (
         *     (
         *       `test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post and exists
         *       (
         *         select * from `test_posts` where `test_images`.`imageable_id` = `test_posts`.`id`
         *       )
         *     )
         *   )
         *
         * whereHasIn sql.
         *
         * select * from `test_images` where
         *   (
         *     (
         *       `test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post and `test_images`.`imageable_id` in
         *         (
         *           select `test_posts`.`id` from `test_posts` where `test_images`.`imageable_id` = `test_posts`.`id`
         *         )
         *     )
         *   )
         */
        $sql1 = Image::whereHasMorphIn('imageable', Post::class)->sql();

        $this->assertEquals(
            'select * from `test_images` where ((`test_images`.`imageable_type` = Dcat\Laravel\Database\Tests\Models\Post and `test_images`.`imageable_id` in (select `test_posts`.`id` from `test_posts` where `test_images`.`imageable_id` = `test_posts`.`id`)))',
            $sql1
        );
    }
}
