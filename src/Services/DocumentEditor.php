<?php

namespace mindtwo\DocumentGenerator\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use mindtwo\DocumentGenerator\Document\Document;
use mindtwo\DocumentGenerator\Editor\EditDocument;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Models\DocumentLayout;

class DocumentEditor
{
    public function __construct(
        protected BlockRenderer $blockRenderer,
        protected PlaceholderResolver $placeholderResolver,
    ) {
    }

    /**
     * Generate document
     *
     * @param  string|int  $layoutIdentifier
     * @return Document
     */
    public function loadLayout(string|int $layoutIdentifier): EditDocument
    {
        Config::set('documents.context', ResolveContext::Preview);

        $layout = DocumentLayout::query()->byIdentifier($layoutIdentifier)->first();

        $editBlocks = collect([]);
        foreach ($layout->blocks as $documentBlock) {
            $editBlock = $this->blockRenderer->editBlock($documentBlock, $layout);

            $editBlocks->push($editBlock);
        }

        $placeholder = $this->placeholderResolver->resolveAll($layout->placeholder, $layout->model);

        Config::set('documents.context', null);

        return new EditDocument($layoutIdentifier, $editBlocks->mapWithKeys(fn ($b) => [$b->name => $b]), $layout->show_border, $layout->content_width, $layout->orientation, $placeholder);
    }
}
