<?php

namespace mindtwo\DocumentGenerator\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasDocument
{
    public function document(): MorphOne;
}
