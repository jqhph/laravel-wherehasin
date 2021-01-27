<?php

namespace Dcat\Laravel\Database\Tests\Feature\WhereHasIn;

use Dcat\Laravel\Database\Tests\Models\Profile;
use Dcat\Laravel\Database\Tests\TestCase;

class BelongsToTest extends TestCase
{
    public function testSQL()
    {
        /**
         * whereHas sql.
         *
         * select * from `test_user_profiles` where exists
         *   (
         *     select * from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id`
         *   )
         * and `post_code` = ?
         */
        $sql1 = Profile::whereHasIn('user')->where('post_code', 1)->toSql();

        $this->assertEquals(
            'select * from `test_user_profiles` where `test_user_profiles`.`user_id` in (select `test_users`.`id` from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id`) and `post_code` = ?',
            $sql1
        );

        /**
         * whereHas sql.
         *
         * select * from `test_user_profiles` where exists
         *   (
         *     select * from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id` and `username` like ?
         *   )
         * and `post_code` = ?
         */
        $sql2 = Profile::whereHasIn('user', function ($q) {
            $q->where('username', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_user_profiles` where `test_user_profiles`.`user_id` in (select `test_users`.`id` from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id` and `username` like ?)',
            $sql2
        );
    }

    public function testOrWhereSQL()
    {
        $sql1 = Profile::where('post_code', 'like', '%51')->orWhereHasIn('user')->toSql();

        $this->assertEquals(
            'select * from `test_user_profiles` where `post_code` like ? or (`test_user_profiles`.`user_id` in (select `test_users`.`id` from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id`))',
            $sql1
        );

        $sql2 = Profile::where('post_code', 'like', '%51')->orWhereHasIn('user', function ($q) {
            $q->where('username', 'like', '%laravel%');
        })->toSql();

        $this->assertEquals(
            'select * from `test_user_profiles` where `post_code` like ? or (`test_user_profiles`.`user_id` in (select `test_users`.`id` from `test_users` where `test_user_profiles`.`user_id` = `test_users`.`id` and `username` like ?))',
            $sql2
        );
    }

    public function testData()
    {
        $data1 = Profile::whereHasIn('user')->orderBy('id')->get()->toArray();

        $data2 = Profile::whereHas('user')->orderBy('id')->get()->toArray();

        $this->assertEquals($data1, $data2);
    }
}
