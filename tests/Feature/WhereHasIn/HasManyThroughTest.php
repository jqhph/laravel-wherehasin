<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Country;
use Dcat\Laravel\Database\Tests\TestCase;

class HasManyThroughTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_countries` where exists
         *   (
         *     select * from `test_posts` inner join `test_users`
         *     on `test_users`.`id` = `test_posts`.`user_id` where `test_countries`.`id` = `test_users`.`country_id`
         *   )
         * and `name` = ?"
         */
        $sql1 = Country::whereHasIn('posts')->where('name', 'China')->toSql();

        $this->assertEquals(
            'select * from `test_countries` where `test_countries`.`id` in (select `test_users`.`country_id` from `test_posts` inner join `test_users` on `test_users`.`id` = `test_posts`.`user_id` where `test_countries`.`id` = `test_users`.`country_id`) and `name` = ?',
            $sql1
        );

        /**
         * whereHas sql.
         *
         * select * from `test_countries` where exists
         *   (
         *     select * from `test_posts` inner join `test_users`
         *     on `test_users`.`id` = `test_posts`.`user_id`
         *     where `test_countries`.`id` = `test_users`.`country_id` and `title` like ?
         *   )
         * and `name` = ?
         */
        $sql2 = Country::whereHasIn('posts', function ($q) {
            $q->where('title', 'like', '%laravel%');
        })->where('name', 'China')->toSql();

        $this->assertEquals(
            'select * from `test_countries` where `test_countries`.`id` in (select `test_users`.`country_id` from `test_posts` inner join `test_users` on `test_users`.`id` = `test_posts`.`user_id` where `test_countries`.`id` = `test_users`.`country_id` and `title` like ?) and `name` = ?',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = Country::whereHasIn('posts')->orderBy('id')->get()->toArray();
        $data2 = Country::whereHas('posts')->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
