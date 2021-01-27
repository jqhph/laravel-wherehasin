<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\User;
use Dcat\Laravel\Database\Tests\TestCase;

class BelongsToManyTest extends TestCase
{
    public function testSQL()
    {
        /**
         * 查询含有标签的用户.
         *
         * whereHas sql
         *
         * select * from `test_users` where exists
         *   (
         *     select * from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id`
         *     where `test_users`.`id` = `test_user_tags`.`user_id`
         * )
         * and `email` like ?
         */
        $sql1 = User::whereHasIn('tags')->where('email', 'like', '.com%')->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_tags`.`user_id` from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id` where `test_users`.`id` = `test_user_tags`.`user_id`) and `email` like ?',
            $sql1
        );

        /**
         * whereHas sql.
         *
         * select * from `test_users` where exists
         *   (
         *     select * from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id`
         *     where `test_users`.`id` = `test_user_tags`.`user_id` and `id` > ? and `name` like ?
         *   )
         */
        $sql2 = User::whereHasIn('tags', function ($q) {
            $q->where('id', '>', 1);
            $q->where('name', 'like', '%la%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_tags`.`user_id` from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id` where `test_users`.`id` = `test_user_tags`.`user_id` and `id` > ? and `name` like ?)',
            $sql2
        );
    }

    public function testOrWhereSQL()
    {
        /**
         * orWhereHas sql.
         *
         * select * from `test_users` where `id` > ? or exists
         *   (
         *     select * from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id`
         *     where `test_users`.`id` = `test_user_tags`.`user_id`
         * )
         */
        $sql1 = User::where('id', '>', 10)->orWhereHasIn('tags')->toSql();

        $this->assertEquals(
            'select * from `test_users` where `id` > ? or (`test_users`.`id` in (select `test_user_tags`.`user_id` from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id` where `test_users`.`id` = `test_user_tags`.`user_id`))',
            $sql1
        );

        /**
         * orWhereHas sql.
         *
         * select * from `test_users` where `id` > ? or exists
         *   (
         *     select * from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id`
         *     where `test_users`.`id` = `test_user_tags`.`user_id` and `id` > ? and `username` like ?
         * )
         */
        $sql2 = User::where('id', '>', 10)->orWhereHasIn('tags', function ($q) {
            $q->where('id', '>', 1);
            $q->where('username', 'like', '%la%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_users` where `id` > ? or (`test_users`.`id` in (select `test_user_tags`.`user_id` from `test_tags` inner join `test_user_tags` on `test_tags`.`id` = `test_user_tags`.`tag_id` where `test_users`.`id` = `test_user_tags`.`user_id` and `id` > ? and `username` like ?))',
            $sql2
        );
    }
}
