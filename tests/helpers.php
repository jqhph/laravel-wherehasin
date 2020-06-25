<?php

function get_cost_second($callback)
{
    $start = microtime(true);

    $callback();

    return microtime(true) - $start;
}
