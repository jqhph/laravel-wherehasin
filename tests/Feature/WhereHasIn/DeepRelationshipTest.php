<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\User;
use Dcat\Laravel\Database\Tests\TestCase;

class DeepRelationshipTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_users` where exists
         *   (
         *     select * from `test_painters` inner join `test_user_painters`
         *     on `test_painters`.`id` = `test_user_painters`.`painter_id`
         *     where `test_users`.`id` = `test_user_painters`.`user_id` and exists
         *       (
         *         select * from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`
         *       )
         *   )
         *
         *
         * whereHasIn sql
         *
         * select * from `test_users` where `test_users`.`id` in
         *   (
         *     select `test_user_painters`.`user_id` from `test_painters` inner join `test_user_painters`
         *     on `test_painters`.`id` = `test_user_painters`.`painter_id`
         *     where `test_users`.`id` = `test_user_painters`.`user_id` and `test_painters`.`id` in
         *       (
         *         select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`
         *       )
         *   )
         */
        $sql1 = User::whereHasIn('painters.paintings')->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_painters`.`user_id` from `test_painters` inner join `test_user_painters` on `test_painters`.`id` = `test_user_painters`.`painter_id` where `test_users`.`id` = `test_user_painters`.`user_id` and `test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`))',
            $sql1
        );

        /**
         * whereHas sql.
         *
         * select * from `test_users` where exists
         *   (
         *     select * from `test_painters` inner join `test_user_painters`
         *     on `test_painters`.`id` = `test_user_painters`.`painter_id`
         *     where `test_users`.`id` = `test_user_painters`.`user_id` and exists
         *       (
         *         select * from `test_paintings`
         *         where `test_painters`.`id` = `test_paintings`.`painter_id` and `id` < ?
         *       )
         *   )
         *
         *
         * whereHasIn sql
         *
         * select * from `test_users` where `test_users`.`id` in
         *   (
         *     select `test_user_painters`.`user_id` from `test_painters` inner join `test_user_painters`
         *     on `test_painters`.`id` = `test_user_painters`.`painter_id`
         *     where `test_users`.`id` = `test_user_painters`.`user_id` and `test_painters`.`id` in
         *       (
         *         select `test_paintings`.`painter_id` from `test_paintings`
         *         where `test_painters`.`id` = `test_paintings`.`painter_id` and (`id` < ?)
         *       )
         *   )
         */
        $sql2 = User::whereHasIn('painters.paintings', function ($q) {
            $q->where('id', '<', 100);
        })->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_painters`.`user_id` from `test_painters` inner join `test_user_painters` on `test_painters`.`id` = `test_user_painters`.`painter_id` where `test_users`.`id` = `test_user_painters`.`user_id` and `test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id` and `id` < ?))',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = User::whereHasIn('painters.paintings', function ($q) {
            $q->whereIn('id', [100, 101]);
        })->orderBy('id')->get()->toArray();

        $data2 = User::whereHas('painters.paintings', function ($q) {
            $q->whereIn('id', [100, 101]);
        })->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
