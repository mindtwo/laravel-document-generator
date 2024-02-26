<?php

namespace mindtwo\DocumentGenerator\Modules\Placeholder\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Modules\Placeholder\Contracts\Placeholder;

class PlaceholderResolver
{
    /**
     * Autodiscover directories
     *
     * @var ?array
     */
    private $directories = null;

    /**
     * Discovered/Registered placeholders
     *
     * @var array<string, Placeholder>
     */
    private $placeholders = [];

    public function __construct(
        protected array $config,
        array $placeholders = []
    ) {
        if (isset($config['placeholder'])) {
            $this->directories = $config['placeholder']['auto_discover'] ?? null;
        }

        $this->loadPlaceholders($placeholders);
    }

    /**
     * Resolve values for all placeholders in array
     * for given model attached to document
     *
     * @param  array  $placeholders
     * @param  Model  $model
     * @return array
     */
    public function resolveAll(array $placeholders, Model $model): array
    {
        $resolved = [];

        foreach ($placeholders as $placeholder) {
            $resolved[$placeholder] = $this->resolve($placeholder, $model);
        }

        return $resolved;
    }

    /**
     * Resolve a value for a placeholder by its name for the model
     * attached to document
     *
     * @param  string  $placeholderName
     * @param  Model  $model
     * @return ?string
     */
    public function resolve(string $placeholderName, Model $model): ?string
    {
        if (isset($this->placeholders[$placeholderName])) {
            $value = $this->placeholders[$placeholderName]->resolve($model);

            return $value;
        }

        $expl = explode('.', $placeholderName);

        if (count($expl) > 1) {
            $class = Str::title($expl[0]);

            // check if the placeholder starts with model::class with lowercase first letter
            if ($class !== $model::class) {
                throw new \Exception("$class does not match the model the document is generated for", 1);
            }

            array_shift($expl);

            // try to retrieve the value from model
            $value = $model;
            foreach ($expl as $part) {
                if (isset($value->{$part})) {
                    $value = $value->{$part};
                }
            }

            if (isset($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Register a placeholder for a given name
     *
     * @param  class-string<Placeholder>|Placeholder  $placeholder
     * @param  ?string  $name
     * @param  bool  $override
     */
    public function registerPlaceholder(string|Placeholder $placeholder, ?string $name = null, $override = false): void
    {
        $name = $name ?? Str::of(class_basename($placeholder))->replace('Placeholder', '')->snake()->toString();

        if (isset($this->placeholders[$name]) && ! $override) {
            throw new \Exception("There is already a placeholder registered for the name $name", 1);
        }

        $this->placeholders[$name] = is_string($placeholder) ? app()->make($placeholder) : $placeholder;
    }

    /**
     * Load all placeholders from autoload directory
     * and the registered ones
     */
    protected function loadPlaceholders(?array $placeholder = []): void
    {
        // autoload from classes
        $this->loadFromClasses($placeholder);

        // autoload from directories
        $this->autoloadFromDirectories();
    }

    /**
     * Load all placeholders from classes
     */
    private function loadFromClasses(?array $placeholder = []): void
    {
        if (is_null($placeholder)) {
            return;
        }

        foreach ($placeholder as $placeholder) {
            $this->registerPlaceholder($placeholder);
        }
    }

    /**
     * Autoload placeholders from directories
     */
    private function autoloadFromDirectories(): void
    {
        if (is_null($this->directories)) {
            return;
        }

        foreach ($this->directories as $dir) {
            $searchPath = base_path("app/$dir");

            if (! is_dir($searchPath)) {
                continue;
            }

            // get all placeholders in autoload directory
            foreach (rsearch($searchPath, '/.*\.php/') as $file) {
                // get className
                $class = basename($file, '.php');

                // get namespace
                $namespace = extract_namespace($file);

                $fullClass = "$namespace\\$class";

                $this->registerPlaceholder($fullClass);
            }
        }
    }
}
