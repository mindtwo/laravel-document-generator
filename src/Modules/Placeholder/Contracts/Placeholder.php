<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use Illuminate\Database\Eloquent\Model;
use Stringable;

interface Placeholder
{
    public function resolve(Model $model): null|string|Stringable;
}
