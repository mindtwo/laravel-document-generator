<?php

namespace mindtwo\DocumentGenerator\Document;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Enums\ResolveContext;

abstract class Placeholder
{
    abstract public function resolve(Model $model, ResolveContext $context, ?Document $document): string;
}
