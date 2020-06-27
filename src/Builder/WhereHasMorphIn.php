<?php

namespace Dcat\Laravel\Database\Builder;

use Illuminate\Database\Eloquent\Relations\Relation;

class WhereHasMorphIn
{
    public static function make()
    {
        return function ($relation, $types, $callback = null, $boolean = 'and') {
            $relation = $this->getRelationWithoutConstraints($relation);

            $types = (array) $types;

            if ($types === ['*']) {
                $types = $this->model->newModelQuery()->distinct()->pluck($relation->getMorphType())->all();

                foreach ($types as &$type) {
                    $type = Relation::getMorphedModel($type) ?? $type;
                }
            }

            return $this->where(function ($query) use ($relation, $callback, $types) {
                foreach ($types as $type) {
                    $query->orWhere(function ($query) use ($relation, $callback, $type) {
                        $belongsTo = $this->getBelongsToRelation($relation, $type);

                        if ($callback) {
                            $callback = function ($query) use ($callback, $type) {
                                return $callback($query, $type);
                            };
                        }

                        $query->where($relation->getRelated()->getTable().'.'.$relation->getMorphType(), '=', (new $type)->getMorphClass())
                            ->whereHasIn($belongsTo, $callback);
                    });
                }
            }, null, null, $boolean);
        };
    }
}
