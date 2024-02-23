<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use Illuminate\Support\Stringable;

interface HasFake
{
    public function fake(): null|string|Stringable;
}
