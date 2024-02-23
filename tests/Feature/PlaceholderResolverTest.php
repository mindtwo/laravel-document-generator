<?php

use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

it('can make PlaceholderResolver class', function () {
    $this->assertInstanceOf(PlaceholderResolver::class, app()->make(PlaceholderResolver::class));
});
