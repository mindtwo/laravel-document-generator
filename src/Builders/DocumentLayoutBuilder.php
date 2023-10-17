<?php

namespace mindtwo\DocumentGenerator\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class DocumentLayoutBuilder extends Builder
{
    public function byIdentifier(string|int $layoutIdentifier): self
    {
        return $this->when(is_string($layoutIdentifier),
            fn($q) => $q->whereUuid($layoutIdentifier)
        )->when(is_int($layoutIdentifier),
            fn($q) => $q->where('id', $layoutIdentifier)
        );
    }

    public function forModel(Model $model): self
    {
        $type = array_flip(Relation::$morphMap)[$model::class];

        return $this->where('model_id', $model->id)->where('model_type', $type);
    }
}
