<?php

namespace mindtwo\DocumentGenerator\Document;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use mindtwo\DocumentGenerator\Services\BlockTemplateResolver;
use mindtwo\DocumentGenerator\Services\PlaceholderResolver;

class Layout
{
    private BlockTemplateResolver $blockTemplateResolver;

    private PlaceholderResolver $placeholderResolver;

    protected Document $document;

    public function __construct(
        Document $document,
    ) {
        $this->document = $document;

        $this->placeholderResolver = app()->make(PlaceholderResolver::class);
        $this->blockTemplateResolver = app()->make(BlockTemplateResolver::class);
    }

    public function render(string $content): string
    {
        return Blade::renderComponent($this->component($content));
    }

    /**
     * Get our layout as component class
     *
     * @param  string  $content
     * @return Component
     */
    protected function component(string $content): Component
    {
        $string = $this->getLayoutFileContents();
        $string = str_replace('{{ $slot }}', $content, $string);

        $attributes = $this->getAttributes();

        return new class($string, $attributes) extends Component
        {
            protected $template;

            public function __construct($template, $attributes)
            {
                $this->template = $template;
                $this->attributes = $attributes;
            }

            public function render()
            {
                return $this->template;
            }
        };
    }

    /**
     * Get placeholders for this layout.
     *
     * @return array
     */
    public function placeholder(): array
    {
        $placeholders = [];
        // regex to find and match all vars in blade file
        preg_match_all('/{{[\s]*\$(?<placeholder>[a-zA-Z\_]+)[\s]*}}/m', $this->getLayoutFileContents(), $placeholders);

        return $placeholders['placeholder'] ?? [];
    }

    /**
     * Undocumented function.
     *
     * @return array
     */
    public function pageDimensions(): array
    {
        $orientation = $this->document->getDocumentOrientation();

        return $orientation->dimensions();
    }

    /**
     * Get attribute bag for our layout component
     *
     * @return void
     */
    protected function getAttributes()
    {
        $document = $this->document;

        $fields = $this->placeholderResolver->resolveLayout($this, $document);

        $data = collect($fields)
            ->except('slot')
            ->mapWithKeys(fn ($field, $key) => optional($field)->toArray() ?? [$key => '']);

        $dims = $this->pageDimensions();

        $data->put('pageWidth', $dims['width']);
        $data->put('pageHeight', $dims['height']);
        $data->put('innerWidth', $document->getContentWidth()->value);

        return new ComponentAttributeBag($data->toArray());
    }

    /**
     * Get layout file contents
     *
     * @return string
     */
    private function getLayoutFileContents(): string
    {
        $templateFile = $this->getLayoutFilePath();

        return ! empty($templateFile) ? File::get($templateFile) : '';
    }

    /**
     * Get layout template filePath
     *
     * @return string
     */
    private function getLayoutFilePath(): string
    {
        $layoutName = $this->document->getLayoutName();

        return $this->blockTemplateResolver->getLayoutPath($layoutName);
    }
}
