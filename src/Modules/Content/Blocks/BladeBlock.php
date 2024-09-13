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

    /**
     * Get array of all placeholder we need to resolve.
     */
    public function placeholder(): array
    {
        $placeholder = parent::placeholder();

        $propAttributes = $this->propAttributes();

        return array_merge($placeholder, array_keys($propAttributes));
    }

    /**
     * Get prop attributes from template.
     * Use the default @props directive from Laravel Blade.
     */
    protected function propAttributes(): array
    {
        $matches = [];

        preg_match_all('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( (?<propArray> [\S\s]*? ) \))?/x', $this->template(), $matches);

        if (empty($matches)) {
            return [];
        }

        // Get the prop array string from the matches.
        $propArrayStr = $matches['propArray'][0] ?? null;
        if (! $propArrayStr) {
            return [];
        }

        $propStrs = explode(',', str_replace(['[', ']'], ['', ''], $propArrayStr));

        // Trim characters to remove from prop strings
        $propTrim = " \n\r\t\v\0\'\"";

        // Reduce the prop strings to an array of key-value pairs
        return array_reduce($propStrs, function ($arr, $prop) use ($propTrim) {
            $prop = trim($prop, $propTrim);

            // If the prop is empty, return the array
            if (empty($prop)) {
                return $arr;
            }

            // If the prop does not contain ' => ', it is a simple string
            if (strpos($prop, '=>') === false) {
                $key = trim($prop, $propTrim);

                $arr[$key] = null;

                return $arr;
            }

            // Split by ' => ' to separate keys and values
            [$key, $value] = explode('=>', $prop);

            $key = trim($key, $propTrim);
            $value = trim($value, $propTrim);

            $arr[$key] = $value;

            return $arr;
        }, []);
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
    public function render(array $resolvedPlaceholder, array $extra = []): string
    {
        $fields = $this->fields($resolvedPlaceholder);

        return Blade::render($this->template, array_merge($fields, ['extra' => $extra]));
    }
}
