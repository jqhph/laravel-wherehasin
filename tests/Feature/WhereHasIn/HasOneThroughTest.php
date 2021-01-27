<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Supplier;
use Dcat\Laravel\Database\Tests\TestCase;

class HasOneThroughTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_suppliers` where exists
         *   (
         *     select * from `test_histories` inner join `test_users`
         *     on `test_users`.`id` = `test_histories`.`user_id`
         *     where `test_suppliers`.`id` = `test_users`.`supplier_id`
         * )
         * and `name` like ?
         */
        $sql1 = Supplier::whereHasIn('userHistory')->where('name', 'like', '%la%')->toSql();

        $this->assertEquals(
            'select * from `test_suppliers` where `test_suppliers`.`id` in (select `test_users`.`supplier_id` from `test_histories` inner join `test_users` on `test_users`.`id` = `test_histories`.`user_id` where `test_suppliers`.`id` = `test_users`.`supplier_id`) and `name` like ?',
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
        $sql2 = Supplier::whereHasIn('userHistory', function ($q) {
            $q->where('title', 'like', '%la%');
        })->where('name', 'China')->toSql();

        $this->assertEquals(
            'select * from `test_suppliers` where `test_suppliers`.`id` in (select `test_users`.`supplier_id` from `test_histories` inner join `test_users` on `test_users`.`id` = `test_histories`.`user_id` where `test_suppliers`.`id` = `test_users`.`supplier_id` and `title` like ?) and `name` = ?',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = Supplier::whereHasIn('userHistory', function ($q) {
            $q->where('test_histories.id', '<', 100);
        })->orderBy('id')->get()->toArray();

        $data2 = Supplier::whereHas('userHistory', function ($q) {
            $q->where('test_histories.id', '<', 100);
        })->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
