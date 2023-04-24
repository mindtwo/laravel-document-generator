<?php

namespace mindtwo\DocumentGenerator\Generator;

use mindtwo\DocumentGenerator\Enums\ResolveContext;

interface NameGenerator
{
    public function getName(ResolveContext $resolveContext): string;
}
