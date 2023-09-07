<?php

namespace mindtwo\DocumentGenerator\Block;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Services\BlockTemplateResolver;

class BladeBlock implements Block
{
    use BaseBlock;

    public $updateable = [
        'show',
        'position',
    ];

    public function __construct(
        public string $name,
        public string $templateFile,
        public bool $show,
        public int $position,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function template(): string
    {
        return $this->templateFile;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $fields, DocumentOrientation $orientation, bool $force = false): string
    {
        if (! $this->show && ! $force) {
            return '';
        }

        $data = collect($fields)->mapWithKeys(fn ($field, $key) => optional($field)->toArray() ?? [$key => ''])->put('orientation', $orientation->value)->toArray();

        return Blade::render($this->templateContents(), $data);
    }

    private function templateContents(): string
    {
        $templateResolver = app()->make(BlockTemplateResolver::class);

        $templateFile = $templateResolver->getTemplatePath($this->templateFile);

        return ! empty($templateFile) ? File::get($templateFile) : '';
    }

    /**
     * {@inheritDoc}
     */
    public function hasTemplateFile(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function placeholder(): array
    {
        $placeholders = [];
        // regex to find and match all vars in blade file
        preg_match_all('/{{[\s]*\$(?<placeholder>[a-zA-Z\_]+)[\s]*}}/m', $this->templateContents(), $placeholders);

        $placeholders = $placeholders['placeholder'] ?? [];
        // remove orientation from placeholder
        $placeholders = \array_filter($placeholders, static function ($element) {
            return $element !== 'orientation';
        });

        return $placeholders;
    }

    /**
     * Get unrendered block template
     *
     * @return string
     */
    public function unrender(): string
    {
        return $this->templateContents();
    }
}
