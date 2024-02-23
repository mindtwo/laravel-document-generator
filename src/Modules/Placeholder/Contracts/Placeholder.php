<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use Illuminate\Support\Stringable;

interface Placeholder
{
    public function resolve(): null|string|Stringable;
}
