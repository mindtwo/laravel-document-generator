<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

use Illuminate\Support\Collection;

interface Commandable
{
    public static function getEligibleModels(): Collection;
}
