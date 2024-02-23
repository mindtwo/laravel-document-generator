<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use Stringable;

interface HasFake
{
    public function fake(): null|string|Stringable;
}
