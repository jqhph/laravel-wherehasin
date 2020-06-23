<?php

namespace Dcat\Laravel\Database;

use Dcat\Laravel\Database\Builder\WhereHasIn;
use Dcat\Laravel\Database\Builder\WhereHasMorphIn;
use Illuminate\Database\Eloquent;
use Illuminate\Support\ServiceProvider;

class WhereHasInServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Eloquent\Builder::macro('whereHasIn', function ($relationName, ?\Closure $callable = null) {
            return (new WhereHasIn($this, $relationName, $callable))->execute();
        });
        Eloquent\Builder::macro('orWhereHasIn', function ($relationName, ?\Closure $callable = null) {
            return $this->orWhere(function ($query) use ($relationName, $callable) {
                return $query->whereHasIn($relationName, $callable);
            });
        });

        Eloquent\Builder::macro('whereHasMorphIn', WhereHasMorphIn::make());
        Eloquent\Builder::macro('orWhereHasMorphIn', function ($relation, $types, ?\Closure $callback = null) {
            return $this->whereHasMorphIn($relation, $types, $callback, 'or');
        });
    }
}
