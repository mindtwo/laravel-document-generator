<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Blocks;

abstract class Block
{
    protected ?string $template = null;
    protected ?string $name = null;

    abstract protected function placeholderRegex(?string $field = null): string;

    /**
     * Render the block.
     *
     * @param array $fields Array of resolved placeholders.
     */
    abstract public function render(array $resolvedPlaceholder): string;

    public function template(): string
    {
        return $this->template;
    }

    public function name(): string
    {
        return $this->name;
    }

    protected function fields(array $resolvedPlaceholder): array
    {
        $blockPlaceholder = $this->placeholder();

        return array_intersect_key($resolvedPlaceholder, array_flip($blockPlaceholder));
    }

    /**
     * Get array of all placeholder we need to resolve.
     */
    public function placeholder(): array
    {
        $placeholder = [];
        preg_match_all($this->placeholderRegex(), $this->template(), $placeholder);

        return $placeholder['placeholder'] ?? [];
    }
}
