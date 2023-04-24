<?php

namespace mindtwo\DocumentGenerator\Editor;

use Illuminate\Support\Collection;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Enums\DocumentWidth;
use mindtwo\DocumentGenerator\Http\Requests\EditLayoutRequest;
use mindtwo\DocumentGenerator\Http\Resources\EditLayoutResource;

class EditDocument
{
    public function __construct(
        public int|string $layoutIdentifier,
        public Collection $blocks,
        public ?bool $showBorder,
        public ?DocumentWidth $contentWidth,
        public ?DocumentOrientation $orientation,
        public ?array $placeholder,
    ) {
    }

    public function toResponse(): EditLayoutResource
    {
        return new EditLayoutResource($this);
    }

    public function toJson(): array
    {
        return json_decode($this->toResponse()->content(), true);
    }

    public function fromRequest(EditLayoutRequest $editLayoutRequest): self
    {
        $validated = $editLayoutRequest->validated();

        return new self(
            layoutIdentifier: $validated['layoutIdentifier'],
            blocks: $validated['blocks'],
            showBorder: $validated['showBorder'],
            contentWidth: $validated['contentWidth'],
            orientation: $validated['orientation'],
            placeholder: null,
        );
    }
}
