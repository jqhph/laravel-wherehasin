<?php

namespace Illuminate\Database\Eloquent
{
    /**
     * @method $this whereHasIn(string $relation, ?\Closure $callable = null)
     * @method $this orWhereHasIn(string $relation, ?\Closure $callable = null)
     * @method $this whereHasNotIn(string $relation, ?\Closure $callable = null)
     * @method $this orWhereHasNotIn(string $relation, ?\Closure $callable = null)
     * @method $this whereHasMorphIn(string $relation, $types, ?\Closure $callable = null)
     * @method $this orWhereHasMorphIn(string $relation, $types, ?\Closure $callable = null)
     */
    class Builder extends \Illuminate\Database\Query\Builder
    {
    }
}
