<?php

namespace mindtwo\DocumentGenerator\Services;

use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Block\Block;
use mindtwo\DocumentGenerator\Models\DocumentBlock;

class BlockTemplateResolver
{
    /**
     * Registered templates
     *
     * @var array
     */
    private $templateRoots = null;

    /**
     * Found templates
     *
     * @var array
     */
    private $templates = [];

    /**
     * Found templates
     *
     * @var array
     */
    private $layouts = [];

    public function __construct(
        protected array $config,
    ) {
        if (isset($config['templates'])) {
            $this->templateRoots = $config['templates']['roots'] ?? null;
        }

        $this->loadTemplates();
    }

    /**
     * Resolve a value for a blade by its name for the model
     * attached to document
     *
     * @param  string  $blockName
     * @return Block
     */
    public function resolve(DocumentBlock $documentBlock): Block
    {
        return $documentBlock->blockType::from($documentBlock);
    }

    /**
     * Add template root manually
     *
     * @param  string  $path
     * @return void
     */
    public function addTemplateRoot(string $path): void
    {
        $this->templateRoots[] = $path;

        $this->loadTemplates();
    }

    /**
     * Collect all templates from template roots
     *
     * @param  string  $path
     * @return void
     */
    public function loadTemplates(): bool
    {
        if (is_null($this->templateRoots)) {
            return true;
        }

        foreach ($this->templateRoots as $templateRoot) {
            if (! is_dir($templateRoot)) {
                continue;
            }

            // get all placeholders in autoload directory
            foreach (rsearch("$templateRoot", '/.*\.blade.php/') as $file) {
                $name = basename($file, '.blade.php');

                // cut base path here
                if ($name === 'layout') {
                    $this->addLayout($file);

                    continue;
                }

                $this->addTemplate($file);
            }
        }

        return true;
    }

    /**
     * Get template from our registered templates
     *
     * @param  string  $template
     * @return string
     */
    public function getTemplate(string $template): string
    {
        if (isset($this->templates[$template])) {
            return $this->templates[$template];
        }

        return $this->templateExists($template) ? $template : '';
    }

    /**
     * Get template from our registered templates
     *
     * @param  string  $template
     * @return string
     */
    public function getTemplatePath(string $template): string
    {
        return base_path($this->getTemplate($template));
    }

    /**
     * Get layout template from our registered templates
     *
     * @param  string  $template
     * @return string
     */
    public function getLayout(string $template): string
    {
        if (isset($this->layouts[$template])) {
            return $this->layouts[$template];
        }

        return $this->templateExists($template) ? $template : '';
    }

    /**
     * Get layout template from our registered templates
     *
     * @param  string  $layout
     * @return string
     */
    public function getLayoutPath(string $layout): string
    {
        return base_path($this->getLayout($layout));
    }

    /**
     * Check if a tempalte exists by path relative to src or by name
     *
     * @param  string  $template
     * @return bool
     */
    public function templateExists(string $template): bool
    {
        if (array_key_exists($template, $this->templates)) {
            return true;
        }

        return in_array($template, array_values($this->templates));
    }

    /**
     * Add layout template to our layout files
     *
     * @param  string  $filePath
     * @return void
     */
    private function addLayout(string $filePath): void
    {
        // cut base path here
        $name = Str::of($filePath)->before('/layout')->afterLast('/')->toString();

        $this->layouts[$name] = $this->cutBasePath($filePath);
    }

    /**
     * Add template to our template array
     *
     * @param  string  $filePath
     * @return void
     */
    private function addTemplate(string $filePath): void
    {
        $name = basename($filePath, '.blade.php');

        $this->templates[$name] = $this->cutBasePath($filePath);
    }

    /**
     * Remove laravel basePath from our filePath
     *
     * @param  string  $filePath
     * @return string
     */
    private function cutBasePath(string $filePath): string
    {
        return Str::of($filePath)->after(base_path().'/')->toString();
    }
}
