<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Layouts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

class BladeLayout
{

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected array $excludedPlaceholder = ['slot'];

    public function __construct(
        protected string $template,
        array $excludedPlaceholder = [],
    ) {
        if (!empty($excludedPlaceholder)) {
            $this->excludedPlaceholder = $excludedPlaceholder;
        }
    }

    public function render(Document $document, Model $model, string $content): string
    {
        return Blade::renderComponent($this->component($document, $model, $content));
    }

    /**
     * Get our layout as component class
     *
     * @param  string  $content
     * @return Component
     */
    protected function component(Document $document, Model $model, string $content): Component
    {
        $templateString = preg_replace($this->placeholderRegex('slot'), $content, $this->template());

        $attributes = $this->getAttributes($document, $model);

        return new class($templateString, $attributes) extends Component
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

    public function template(): string
    {
        try {
            $templatePath = str_replace('.', '/', str_replace('.blade.php', '', $this->template));

            return file_get_contents(resource_path('views/'.$templatePath.'.blade.php'));
        } catch (\Throwable $th) {
            return '';
        }
    }

    /**
     * Undocumented function.
     *
     * @return array
     */
    public function pageDimensions(Document $document): array
    {
        $orientation = $document->getDocumentOrientation();

        return $orientation->dimensions();
    }

    /**
     * Get attribute bag for our layout component
     *
     * @return array
     */
    protected function getAttributes(Document $document, Model $model)
    {
        $placeholder = $this->placeholder();

        $fields = app(PlaceholderResolver::class)->resolveAll($placeholder, $model);

        $data = collect($fields)
            ->except($this->excludedPlaceholder)
            ->mapWithKeys(fn ($field, $key) => optional($field)->toArray() ?? [$key => '']);

        $dims = $this->pageDimensions($document);

        $data->put('pageWidth', $dims['width']);
        $data->put('pageHeight', $dims['height']);
        $data->put('innerWidth', $document->contentWidth());

        return new ComponentAttributeBag($data->toArray());
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

    protected function placeholderRegex(?string $field = null): string
    {
        if ($field) {
            return '/{{[\s]*\$(?<placeholder>'.$field.')[\s]*}}/m';
        }

        return '/{{[\s]*\$(?<placeholder>[a-zA-Z\_]+)[\s]*}}/m';
    }
}
