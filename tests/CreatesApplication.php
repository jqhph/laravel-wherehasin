<?php

namespace Dcat\Laravel\Database\Tests;

use Dcat\Laravel\Database\WhereHasInServiceProvider;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->register(WhereHasInServiceProvider::class);

        return $app;
    }
}
