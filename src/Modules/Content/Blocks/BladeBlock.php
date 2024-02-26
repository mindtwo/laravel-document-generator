<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Blocks;

use Illuminate\Support\Facades\Blade;

class BladeBlock extends Block
{

    public function __construct(
        protected ?string $name,
        protected ?string $template,
    ) {
    }

    public function template(): string
    {
        $templatePath = str_replace('.', '/', str_replace('.blade.php', '', $this->template));

        return file_get_contents(resource_path('views/'.$templatePath.'.blade.php'));
    }

    protected function placeholderRegex(?string $field = null): string
    {
        if ($field) {
            return '/{{[\s]*\$(?<placeholder>'.$field.')[\s]*}}/m';
        }

        return '/{{[\s]*\$(?<placeholder>[a-zA-Z\_]+)[\s]*}}/m';
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $resolvedPlaceholder): string
    {
        $fields = $this->fields($resolvedPlaceholder);

        return Blade::render($this->template, $fields);
    }
}
