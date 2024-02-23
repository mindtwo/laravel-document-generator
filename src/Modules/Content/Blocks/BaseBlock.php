<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Blocks;


class BaseBlock extends Block
{

    public function __construct(
        protected ?string $name,
        protected ?string $template,
    ) {
    }

    protected function placeholderRegex(?string $field = null): string
    {
        if ($field) {
            return '/{[\s]*(' . $field . ')[\s]*}/m';
        }

        return '/{(?<placeholder>[a-z\_]*)}/m';
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $resolvedPlaceholder): string
    {
        $rendered = $this->template();

        $fields = $this->fields($resolvedPlaceholder);

        foreach ($fields as $name => $value) {
            $rendered = preg_replace($this->placeholderRegex($name), $value, $rendered);
        }

        return $rendered;
    }
}
