<?php

namespace Dcat\Laravel\Database\Builder;

class WhereHasNotIn extends WhereHasIn
{
    /**
     * @var string
     */
    protected $method = 'whereNotIn';
}
