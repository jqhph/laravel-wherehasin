<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\User;
use Dcat\Laravel\Database\Tests\TestCase;

class HasOneTest extends TestCase
{
    public function testSQL()
    {
        $sql1 = User::whereHasIn('profile')->where('email', 'like', '.com%')->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_profiles`.`user_id` from `test_user_profiles` where `test_users`.`id` = `test_user_profiles`.`user_id`) and `email` like ?',
            $sql1
        );

        $sql2 = User::whereHasIn('profile', function ($q) {
            $q->where('id', '>', 1);
            $q->where('username', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_users` where `test_users`.`id` in (select `test_user_profiles`.`user_id` from `test_user_profiles` where `test_users`.`id` = `test_user_profiles`.`user_id` and `id` > ? and `username` like ?)',
            $sql2
        );
    }

    public function testOrWhereSQL()
    {
        $sql1 = User::where('id', '>', 10)->orWhereHasIn('profile')->toSql();

        $this->assertEquals(
            'select * from `test_users` where `id` > ? or (`test_users`.`id` in (select `test_user_profiles`.`user_id` from `test_user_profiles` where `test_users`.`id` = `test_user_profiles`.`user_id`))',
            $sql1
        );

        $sql2 = User::where('id', '>', 10)->orWhereHasIn('profile', function ($q) {
            $q->where('id', '>', 1);
            $q->where('username', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_users` where `id` > ? or (`test_users`.`id` in (select `test_user_profiles`.`user_id` from `test_user_profiles` where `test_users`.`id` = `test_user_profiles`.`user_id` and `id` > ? and `username` like ?))',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = User::whereHasIn('profile', function ($q) {
            $q->where('id', '<', 100);
        })->orderBy('id')->get()->toArray();

        $data2 = User::whereHas('profile', function ($q) {
            $q->where('id', '<', 100);
        })->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
