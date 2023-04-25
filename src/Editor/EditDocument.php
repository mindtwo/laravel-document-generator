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
        public int|string $layout_identifier,
        public Collection $blocks,
        public ?bool $show_border,
        public ?DocumentWidth $content_width,
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
            layout_identifier: $validated['layout_identifier'],
            blocks: $validated['blocks'],
            show_border: $validated['show_border'],
            content_width: $validated['content_width'],
            orientation: $validated['orientation'],
            placeholder: null,
        );
    }
}
