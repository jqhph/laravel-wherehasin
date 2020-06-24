<?php

namespace Dcat\Laravel\Database\Tests;

use CreateTestTables;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        include_once __DIR__.'/helpers.php';

        parent::setUp();

        $this->app['config']->set('database.default', 'mysql');
        $this->app['config']->set('database.connections.mysql.host', env('MYSQL_HOST', 'localhost'));
        $this->app['config']->set('database.connections.mysql.database', env('MYSQL_DATABASE', 'laravel_test'));
        $this->app['config']->set('database.connections.mysql.username', env('MYSQL_USER', 'root'));
        $this->app['config']->set('database.connections.mysql.password', env('MYSQL_PASSWORD', ''));
        $this->app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');

        Schema::defaultStringLength(191);

        //$this->migrateTestTables();

        include_once __DIR__.'/resources/seeds/factory.php';

        create_suppliers();
        create_histories();
        create_users();
        create_contries();
        create_posts();
    }

    protected function tearDown(): void
    {
        //(new CreateTestTables())->down();

        parent::tearDown();
    }

    /**
     * run package database migrations.
     *
     * @return void
     */
    public function migrateTestTables()
    {
        include_once __DIR__.'/resources/migrations/2020_06_23_224641_create_test_tables.php';

        (new CreateTestTables())->up();
    }
}
