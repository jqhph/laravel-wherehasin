<?php

namespace Dcat\Laravel\Database\Builder;

use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent;

class WhereHasIn
{
    /**
     * @var Eloquent\Builder
     */
    protected $builder;

    protected $relation;

    /**
     * @var \Closure
     */
    protected $callback;

    public function __construct(Eloquent\Builder $builder, $relation, $callback)
    {
        $this->builder = $builder;

        $this->relation = $relation;

        $this->callback = $callback;
    }

    public function execute()
    {
        if (! $this->relation) {
            return $this;
        }

        $nextRelation = null;

        if (is_object($this->relation)) {
            $relation = $this->relation;
        } else {
            $relationNames = explode('.', $this->relation);
            $nextRelation = implode('.', array_slice($relationNames, 1));

            $method = $relationNames[0];

            /** @var Relations\BelongsTo|Relations\HasOne $relation */
            $relation = Relations\Relation::noConstraints(function () use ($method) {
                return $this->builder->getModel()->$method();
            });
        }

        $relationQuery = $relation->getQuery();

        if ($nextRelation) {
            $relationQuery->whereHasIn($nextRelation, $this->callback);
        } elseif ($this->callback) {
            $relationQuery->where($this->callback);
        }

        return $this->whereIn($relation, $relationQuery);
    }

    protected function whereIn($relation, $relationQuery)
    {
        if (
            $relation instanceof Relations\MorphTo
            || $relation instanceof Relations\MorphToMany
            || $relation instanceof Relations\MorphMany
            || $relation instanceof Relations\MorphOne
            || $relation instanceof Relations\MorphOneOrMany
        ) {
            throw new \Exception('Please use whereHasMorphIn() for MorphTo relationships.');
        }

        // BelongsTo
        if ($relation instanceof Relations\BelongsTo) {
            return $this->builder->whereIn($relation->getQualifiedForeignKeyName(), $relationQuery->select($relation->getQualifiedOwnerKeyName()));
        }

        $keyName = $this->builder->getModel()->getQualifiedKeyName();

        if (
            $relation instanceof Relations\HasOne
            || $relation instanceof Relations\HasMany
        ) {
            return $this->builder->whereIn($keyName, $relationQuery->select($relation->getQualifiedForeignKeyName()));
        }

        // BelongsToMany
        if ($relation instanceof Relations\BelongsToMany) {
            return $this->builder->whereIn($keyName, $relationQuery->select($relation->getQualifiedForeignPivotKeyName()));
        }

        if ($relation instanceof Relations\HasManyThrough) {
            return $this->builder->whereIn($keyName, $relationQuery->select($relation->getQualifiedFirstKeyName()));
        }

        if ($relation instanceof Relations\HasOneThrough) {
            return $this->builder->whereIn($keyName, $relationQuery->select($relation->getQualifiedFirstKeyName()));
        }

        throw new \Exception(sprintf('%s not support "whereHasIn".', get_class($relation)));
    }
}
