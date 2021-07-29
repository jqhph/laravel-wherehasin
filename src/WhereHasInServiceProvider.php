<?php

namespace Dcat\Laravel\Database;

use Dcat\Laravel\Database\Builder\WhereHasIn;
use Dcat\Laravel\Database\Builder\WhereHasMorphIn;
use Dcat\Laravel\Database\Builder\WhereHasNotIn;
use Illuminate\Database\Eloquent;
use Illuminate\Support\ServiceProvider;

class WhereHasInServiceProvider extends ServiceProvider
{
    public function register()
    {
        Eloquent\Builder::macro('whereHasIn', function ($relationName, $callable = null) {
            return (new WhereHasIn($this, $relationName, function ($nextRelation, $builder) use ($callable) {
                if ($nextRelation) {
                    return $builder->whereHasIn($nextRelation, $callable);
                }

                if ($callable) {
                    return $builder->callScope($callable);
                }

                return $builder;
            }))->execute();
        });
        Eloquent\Builder::macro('orWhereHasIn', function ($relationName, $callable = null) {
            return $this->orWhere(function ($query) use ($relationName, $callable) {
                return $query->whereHasIn($relationName, $callable);
            });
        });

        Eloquent\Builder::macro('whereHasNotIn', function ($relationName, $callable = null) {
            return (new WhereHasNotIn($this, $relationName, function ($nextRelation, $builder) use ($callable) {
                if ($nextRelation) {
                    return $builder->whereHasNotIn($nextRelation, $callable);
                }

                if ($callable) {
                    return $builder->callScope($callable);
                }

                return $builder;
            }))->execute();
        });
        Eloquent\Builder::macro('orWhereHasNotIn', function ($relationName, $callable = null) {
            return $this->orWhere(function ($query) use ($relationName, $callable) {
                return $query->whereHasNotIn($relationName, $callable);
            });
        });

        Eloquent\Builder::macro('whereHasMorphIn', WhereHasMorphIn::make());
        Eloquent\Builder::macro('orWhereHasMorphIn', function ($relation, $types, $callback = null) {
            return $this->whereHasMorphIn($relation, $types, $callback, 'or');
        });
    }
}
