<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Traits;

use mindtwo\DocumentGenerator\Modules\Content\Blocks\Block;
use mindtwo\DocumentGenerator\Modules\Content\Layouts\Layout;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

trait ResolvesDocumentContent
{
    /**
     * Get all blocks for document
     *
     * @return array<Block>
     */
    abstract protected function getBlocks(): array;

    /**
     * Get the layout for document
     */
    abstract protected function getLayout(): ?Layout;

    /**
     * Get the document for content
     */
    abstract protected function getDocument(): ?Document;

    /**
     * Get the document class for content
     */
    abstract protected function getDocumentClass(): ?string;

    public function html(): array
    {
        // get all blocks for document
        $blocks = $this->getBlocks();
        $blockPlaceholder = $this->collectPlaceholder($blocks);

        // get all placeholder values for document
        $resolved = $this->resolvePlaceholder($blockPlaceholder);

        $content = '';
        // render all blocks
        foreach ($blocks as $block) {
            $content .= $block->render($resolved, $this->getExtra());
        }

        $layout = $this->getLayout();

        if ($layout !== null) {
            return [$resolved, $layout->render($this->getDocument(), $this->model, $content)];
        }

        return [$resolved, $content];
    }

    /**
     * Resolve all placeholder
     */
    protected function resolvePlaceholder(array $placeholder): array
    {
        // fake placeholder
        if ($this->isFaking()) {
            return $this->getPlaceholderResolver()->resolveAllWithFakes($placeholder, $this->model, $this->getExtra());
        }

        $finalizedPlaceholder = $this->collectFinalizedPlaceholder();

        if (empty($finalizedPlaceholder)) {
            return $this->getPlaceholderResolver()->resolveAll($placeholder, $this->model, $this->getExtra());
        }

        // get all remaining placeholder
        $remainingPlaceholder = array_values(array_diff($placeholder, array_keys($finalizedPlaceholder)));
        $remainingResolved = $this->getPlaceholderResolver()->resolveAll($remainingPlaceholder, $this->model, $this->getExtra());

        return array_merge($finalizedPlaceholder, $remainingResolved);
    }

    /**
     * Collect all finalized placeholder values
     */
    protected function collectFinalizedPlaceholder(): array
    {
        $document = $this->getDocument();

        $generatedDocument = $document->getGeneratedDocument();

        if (empty($generatedDocument->resolved_placeholder) || empty($document->getFinalizedPlaceholder())) {
            return [];
        }
        $finalizedPlaceholderNames = $document->getFinalizedPlaceholder();

        return array_intersect_key($generatedDocument->resolved_placeholder, array_flip($finalizedPlaceholderNames));
    }

    /**
     * Collect all placeholder from blocks
     *
     * @param  array<Block>  $blocks
     */
    protected function collectPlaceholder(array $blocks): array
    {
        $placeholders = [];

        foreach ($blocks as $block) {
            $placeholders = array_merge($placeholders, $block->placeholder());
        }

        return $placeholders;
    }

    protected function getExtra(): array
    {
        if (! property_exists($this, 'extra')) {
            return [];
        }

        return $this->extra;
    }

    protected function getPlaceholderResolver(): PlaceholderResolver
    {
        if (! property_exists($this, 'placeholderResolver')) {
            $this->placeholderResolver = app(PlaceholderResolver::class);
        }

        return $this->placeholderResolver;
    }

    public function fake(bool $fake = true): static
    {
        if (! property_exists($this, 'fake')) {
            return $this;
        }

        $this->fake = $fake;

        return $this;
    }

    public function isFaking(): bool
    {
        if (! property_exists($this, 'fake')) {
            return false;
        }

        return $this->fake;
    }
}
