<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Painter;
use Dcat\Laravel\Database\Tests\TestCase;

class HasManyTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_painters` where exists
         *   (
         *     select * from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`
         *   )
         * and `username` = ?
         */
        $sql1 = Painter::whereHasIn('paintings')->where('username', 'Monalisa')->toSql();

        $this->assertEquals(
            'select * from `test_painters` where `test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`) and `username` = ?',
            $sql1
        );

        /**
         * whereHas sql.
         *
         * select * from `test_painters` where exists
         *   (
         *     select * from `test_paintings`
         *     where `test_painters`.`id` = `test_paintings`.`painter_id` and `id` > ? and `body` like ?
         *   )
         */
        $sql2 = Painter::whereHasIn('paintings', function ($q) {
            $q->where('id', '>', 1);
            $q->where('body', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_painters` where `test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id` and `id` > ? and `body` like ?)',
            $sql2
        );
    }

    public function testOrWhereSQL()
    {
        $sql1 = Painter::where('id', '>', 10)->orWhereHasIn('paintings')->toSql();

        $this->assertEquals(
            'select * from `test_painters` where `id` > ? or (`test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id`))',
            $sql1
        );

        $sql2 = Painter::where('id', '>', 10)->orWhereHasIn('paintings', function ($q) {
            $q->where('id', '>', 1);
            $q->where('body', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_painters` where `id` > ? or (`test_painters`.`id` in (select `test_paintings`.`painter_id` from `test_paintings` where `test_painters`.`id` = `test_paintings`.`painter_id` and `id` > ? and `body` like ?))',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = Painter::whereHasIn('paintings', function ($q) {
            $q->where('id', '=', 100);
        })->orderBy('id')->get()->toArray();

        $data2 = Painter::whereHas('paintings', function ($q) {
            $q->where('id', '=', 100);
        })->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
