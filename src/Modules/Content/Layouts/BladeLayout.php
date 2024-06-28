<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Layouts;

class BladeLayout extends Layout
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

    public function template(): string
    {
        try {
            $templatePath = str_replace('.', '/', str_replace('.blade.php', '', $this->template));

            return file_get_contents(resource_path('views/'.$templatePath.'.blade.php'));
        } catch (\Throwable $th) {
            return '';
        }
    }

    protected function placeholderRegex(?string $field = null): string
    {
        if ($field) {
            return '/{{[\s]*\$(?<placeholder>'.$field.')[\s]*}}/m';
        }

        return '/{{[\s]*\$(?<placeholder>[a-zA-Z\_]+)[\s]*}}/m';
    }
}
