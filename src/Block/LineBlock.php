<?php

namespace mindtwo\DocumentGenerator\Block;

use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;

class LineBlock implements Block
{
    use BaseBlock {
        render as protected baseRender;
    }

    public $updateable = [
        'show',
        'template',
        'position',
    ];

    public function __construct(
        public string $name,
        public string $template,
        public bool $show,
        public int $position,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function hasEditor(): bool
    {
        return true;
    }

    /**
     * Transformtemplate for saving in database
     *
     * @return string
     */
    public function prepareTemplate(string $template): string
    {
        return Str::of($template)->replace('<div>', '')->replace('</div>', '')->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function render(?array $fields, DocumentOrientation $orientation): string
    {
        if (! $this->show) {
            return '';
        }

        $rendered = $this->baseRender($fields);

        return "<div>$rendered</div>";
    }

    /**
     * Get unrendered block template
     *
     * @return string
     */
    public function unrender(): string
    {
        return $this->template;
    }
}
