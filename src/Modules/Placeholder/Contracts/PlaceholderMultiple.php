<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Contracts;

use Illuminate\Database\Eloquent\Model;

interface PlaceholderMultiple
{
    /**
     * Resolve the placeholder values.
     */
    public function resolve(Model $model, array $extra = []): array;

    /**
     * Get all keys that are resolved by this placeholder.
     */
    public function resolvedKeys(): array;
}
