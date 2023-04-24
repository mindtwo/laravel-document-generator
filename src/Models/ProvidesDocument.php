<?php

namespace mindtwo\DocumentGenerator\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait ProvidesDocument
{
    public function document(): MorphOne
    {
        return $this->morphOne(DocumentLayout::class, 'model');
    }
}
