<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use mindtwo\DocumentGenerator\Modules\Content\Layouts\Layout;
use Stringable;

interface LayoutPlaceholder
{
    public function resolve(Layout $layout): null|string|Stringable;
}
