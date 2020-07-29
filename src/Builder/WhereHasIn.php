<?php

namespace Dcat\Laravel\Database\Builder;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Relations;

class WhereHasIn
{
    /**
     * @var Eloquent\Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $relation;

    /**
     * @var string
     */
    protected $nextRelation;

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

    /**
     * @return Eloquent\Builder
     *
     * @throws \Exception
     */
    public function execute()
    {
        if (! $this->relation) {
            return $this->builder;
        }

        return $this->whereIn(
            $this->formatRelation()
        );
    }

    /**
     * @param Relations\Relation $relation
     *
     * @return Eloquent\Builder
     *
     * @throws \Exception
     */
    protected function whereIn($relation)
    {
        if ($relation instanceof Relations\MorphTo) {
            throw new \Exception('Please use whereHasMorphIn() for MorphTo relationships.');
        }

        $relationQuery = $relation->getQuery();
        $keyName = $this->builder->getModel()->getQualifiedKeyName();

        if (
            $relation instanceof Relations\MorphOne
            || $relation instanceof Relations\MorphMany
        ) {
            return $this->builder->whereIn(
                $keyName,
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedForeignKeyName())
                        ->whereColumn($relation->getQualifiedParentKeyName(), $relation->getQualifiedForeignKeyName())
                        ->where($relation->getQualifiedMorphType(), $relation->getMorphClass())
                )
            );
        }

        if ($relation instanceof Relations\MorphToMany) {
            return $this->builder->whereIn(
                $keyName,
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedForeignPivotKeyName())
                        ->whereColumn($relation->getQualifiedParentKeyName(), $relation->getQualifiedForeignPivotKeyName())
                        ->where($relation->getTable().'.'.$relation->getMorphType(), $relation->getMorphClass())
                )
            );
        }

        // BelongsTo
        if ($relation instanceof Relations\BelongsTo) {
            return $this->builder->whereIn(
                $this->getRelationQualifiedForeignKeyName($relation),
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedOwnerKeyName())
                        ->whereColumn($this->getRelationQualifiedForeignKeyName($relation), $relation->getQualifiedOwnerKeyName())
                )
            );
        }

        if (
            $relation instanceof Relations\HasOne
            || $relation instanceof Relations\HasMany
        ) {
            return $this->builder->whereIn(
                $keyName,
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedForeignKeyName())
                        ->whereColumn($keyName, $relation->getQualifiedForeignKeyName())
                )
            );
        }

        // BelongsToMany
        if ($relation instanceof Relations\BelongsToMany) {
            return $this->builder->whereIn(
                $keyName,
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedForeignPivotKeyName())
                        ->whereColumn($relation->getQualifiedParentKeyName(), $relation->getQualifiedForeignPivotKeyName())
                )
            );
        }

        if (
            $relation instanceof Relations\HasOneThrough
            || $relation instanceof Relations\HasManyThrough
        ) {
            return $this->builder->whereIn(
                $keyName,
                $this->withRelationQueryCallback(
                    $relationQuery
                        ->select($relation->getQualifiedFirstKeyName())
                        ->whereColumn($relation->getQualifiedParentKeyName(), $relation->getQualifiedFirstKeyName())
                )
            );
        }

        throw new \Exception(sprintf('%s does not support "whereHasIn".', get_class($relation)));
    }

    protected function getRelationQualifiedForeignKeyName($relation)
    {
        if (method_exists($relation, 'getQualifiedForeignKeyName')) {
            return $relation->getQualifiedForeignKeyName();
        }

        return $relation->getQualifiedForeignKey();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function formatRelation()
    {
        if (is_object($this->relation)) {
            $relation = $this->relation;
        } else {
            $relationNames = explode('.', $this->relation);
            $this->nextRelation = implode('.', array_slice($relationNames, 1));

            $method = $relationNames[0];

            $relation = Relations\Relation::noConstraints(function () use ($method) {
                return $this->builder->getModel()->$method();
            });
        }

        return $relation;
    }

    /**
     * @param Eloquent\Builder $relation
     *
     * @return Eloquent\Builder
     */
    protected function withRelationQueryCallback($relationQuery)
    {
        if ($this->nextRelation) {
            $relationQuery->whereHasIn($this->nextRelation, $this->callback);
        } elseif ($this->callback) {
            $relationQuery->where($this->callback);
        }

        return $relationQuery;
    }
}
