<?php

namespace mindtwo\DocumentGenerator\Actions;

use mindtwo\DocumentGenerator\Http\Requests\EditLayoutRequest;
use mindtwo\DocumentGenerator\Models\DocumentLayout;

class UpdateLayoutAction
{
    public function __construct(
        protected UpdateBlockAction $updateBlockAction,
    ) {
    }

    public function execute(DocumentLayout $documentLayout, EditLayoutRequest $editLayoutRequest)
    {
        $data = $editLayoutRequest->validated();

        $documentLayout->update(
            collect($data)->except('blocks', 'model_id', 'model_type')->toArray()
        );

        $editLayoutRequest->blocks()->each(fn ($block) => $this->updateBlockAction->execute($documentLayout, $block));
    }
}
