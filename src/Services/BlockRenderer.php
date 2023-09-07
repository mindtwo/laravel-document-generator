<?php

namespace mindtwo\DocumentGenerator\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use mindtwo\DocumentGenerator\Block\Block;
use mindtwo\DocumentGenerator\Document\Field;
use mindtwo\DocumentGenerator\Document\RenderedBlock;
use mindtwo\DocumentGenerator\Editor\EditBlock;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Models\DocumentBlock;
use mindtwo\DocumentGenerator\Models\DocumentLayout;

class BlockRenderer
{
    public function __construct(
        protected BlockTemplateResolver $blockTemplateResolver,
        protected PlaceholderResolver $placeholderResolver,
    ) {
    }

    /**
     * Render Block for document
     *
     * @param  Block  $block
     * @return RenderedBlock
     */
    public function renderBlock(Block $block, Model $model, DocumentOrientation $orientation): RenderedBlock
    {
        if (! $block->show()) {
            return '';
        }

        // get all placeholders for document
        $placeholders = $block->placeholder();

        /** @var Field[] $fields */
        $fields = $this->placeholderResolver->resolveAll($placeholders, $model);

        return new RenderedBlock($block->render($fields, $orientation), $fields);
    }

    /**
     * Get Editor Block
     *
     * @param  DocumentBlock  $documentBlock
     * @param  Model  $model
     * @return EditBlock
     */
    public function editBlock(DocumentBlock $documentBlock, DocumentLayout $documentLayout): EditBlock
    {
        $model = $documentLayout->model;

        /** @var array $editPlaceholders */
        $editPlaceholders = $documentLayout->placeholder;

        $block = $this->blockTemplateResolver->resolve($documentBlock);

        $hasEditor = $block->hasEditor();

        /** @var Field[] $resolved */
        $resolved = [];
        foreach ($block->placeholder() as $placeholder) {
            if (is_null($model)) {
                Log::error('DocumentGenerator: Model is null', [
                    'placeholder' => $placeholder,
                    'block' => $block->template(),
                    'layout' => $documentLayout,
                ]);
            }

            $field = new Field($placeholder, null);

            if (!is_null($model)) {
                $field = $this->placeholderResolver->resolve($placeholder, $model);
            }

            if ($hasEditor && in_array($placeholder, $editPlaceholders)) {
                $field->value = "{{$placeholder}}";
            }

            $resolved[] = $field;
        }

        $output = $block->render($resolved, $documentLayout->orientation);

        return new EditBlock(
            name: $documentBlock->name,
            content: $output,
            show: $block->show(),
            hasEditor: $hasEditor,
            fields: $resolved,
            fieldNames: $block->placeholder(),
            rawContent: $block->unrender(),
        );
    }
}
