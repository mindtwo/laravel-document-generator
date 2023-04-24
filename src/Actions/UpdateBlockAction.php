<?php

namespace mindtwo\DocumentGenerator\Actions;

use mindtwo\DocumentGenerator\Models\DocumentBlock;
use mindtwo\DocumentGenerator\Models\DocumentLayout;

class UpdateBlockAction
{
    /**
     * Update document block
     *
     * @param  DocumentLayout  $documentLayout
     * @param  array  $block
     * @return void
     */
    public function execute(DocumentLayout $documentLayout, array $block): void
    {
        $data = collect($block);

        $documentBlock = DocumentBlock::where([
            'document_layout_id' => $documentLayout->id,
            'name' => $data->get('name'),
        ])->first();

        $block = $documentBlock->blockType::from($documentBlock);

        if ($block->hasEditor()) {
            $data->put('template', $block->prepareTemplate($data->get('content')));
        }

        $documentBlock->update(
            $data->only($block->updateable())->toArray(),
        );
    }
}
