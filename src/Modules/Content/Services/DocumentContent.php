<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Services;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Blocks\Block;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

class DocumentContent
{
    protected PlaceholderResolver $placeholderResolver;

    public function __construct(
        protected Document $document,
        protected Model $model,
    ) {
        $this->placeholderResolver = app(PlaceholderResolver::class);
    }

    public function html(): array
    {
        // get all blocks for document
        $blocks = $this->document->blocks();
        $blockPlaceholder = $this->collectPlaceholder($blocks);

        // get all placeholder values for document
        $resolved = $this->resolvePlaceholder($blockPlaceholder);

        $content = '';
        // render all blocks
        foreach ($blocks as $block) {
            $content .= $block->render($resolved);
        }

        return [$resolved, $this->document->layout()->render($this->document, $this->model, $content)];
    }

    /**
     * Resolve all placeholder
     *
     * @param array $placeholder
     * @return array
     */
    protected function resolvePlaceholder(array $placeholder): array
    {
        $finalizedPlaceholder = $this->collectFinalizedPlaceholder();

        if (empty($finalizedPlaceholder)) {
            return $this->placeholderResolver->resolveAll($placeholder, $this->model);
        }

        // get all remaining placeholder
        $remainingPlaceholder = array_values(array_diff($placeholder, array_keys($finalizedPlaceholder)));
        $remainingResolved = $this->placeholderResolver->resolveAll($remainingPlaceholder, $this->model);

        return array_merge($finalizedPlaceholder, $remainingResolved);
    }

    /**
     * Collect all finalized placeholder values
     *
     * @return array
     */
    private function collectFinalizedPlaceholder(): array
    {
        $generatedDocument = $this->document->getGeneratedDocument();

        if (empty($generatedDocument->resolved_placeholder) || empty($this->document->getFinalizedPlaceholder())) {
            return [];
        }
        $finalizedPlaceholderNames = $this->document->getFinalizedPlaceholder();

        return array_intersect_key($generatedDocument->resolved_placeholder, array_flip($finalizedPlaceholderNames));
    }

    /**
     * Collect all placeholder from blocks
     *
     * @param array<Block> $blocks
     * @return array
     */
    private function collectPlaceholder(array $blocks): array
    {
        $placeholders = [];

        foreach ($blocks as $block) {
            $placeholders = array_merge($placeholders, $block->placeholder());
        }

        return $placeholders;
    }
}
