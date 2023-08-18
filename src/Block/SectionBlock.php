<?php

namespace mindtwo\DocumentGenerator\Block;

use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;

class SectionBlock implements Block
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
        $template = str_replace(['<section>', '</section>', '<p>'], '', $template);

        return Str::of($template)->replace('<\p>', "\n")->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function render(?array $fields, DocumentOrientation $orientation): string
    {
        if (! $this->show) {
            return '';
        }

        $rendered = collect(explode("\n", $this->baseRender($fields)))
            ->reduce(fn ($str, $value) => $str .= "<p>$value</p>", '');

        return "<section>$rendered</section>";
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
