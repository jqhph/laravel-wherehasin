<?php

use Illuminate\Support\Facades\DB;

function get_cost_second($callback)
{
    $start = microtime(true);

    $callback();

    return microtime(true) - $start;
}

function dump_query_sql()
{
    DB::listen(function ($q) {
        dump($q->sql, $q->bindings);
    });
}
