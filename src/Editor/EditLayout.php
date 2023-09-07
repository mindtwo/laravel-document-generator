<?php

namespace mindtwo\DocumentGenerator\Editor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Enums\DocumentWidth;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Helper\ResolveWithContext;
use mindtwo\DocumentGenerator\Http\Resources\EditLayoutResource;
use mindtwo\DocumentGenerator\Models\DocumentLayout;
use mindtwo\DocumentGenerator\Services\BlockRenderer;
use mindtwo\DocumentGenerator\Services\PlaceholderResolver;

class EditLayout
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

    public static function make(DocumentLayout $documentLayout, ?Model $model = null): self
    {
        $model = $model ?? $documentLayout->model;

        if (is_null($model)) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
                'message' => 'Could not resolve a model to create a EditDocument instance for layout: '.$documentLayout->id,
            ], 404));
        }

        return ResolveWithContext::use(ResolveContext::Preview, function () use ($documentLayout, $model) {
            $blockRenderer = app()->make(BlockRenderer::class);
            $placeholderResolver = app()->make(PlaceholderResolver::class);

            $editBlocks = collect([]);
            foreach ($documentLayout->blocks as $documentBlock) {
                $editBlock = $blockRenderer->editBlock($documentBlock, $documentLayout);

                $editBlocks->push($editBlock);
            }

            $placeholder = [];
            if (null !== ($model = $documentLayout->model)) {
                $placeholder = $placeholderResolver->resolveAll($documentLayout->placeholder, $model);
            }

            return new self(
                layout_identifier: $documentLayout->uuid,
                blocks: $editBlocks->mapWithKeys(fn ($b) => [$b->name => $b]),
                show_border: $documentLayout->show_border,
                content_width: $documentLayout->content_width,
                orientation: $documentLayout->orientation,
                placeholder: $placeholder,
            );
        });

        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'message' => 'Could not create a EditDocument instance for layout: '.$documentLayout->id,
        ], 400));
    }

    public function toResponse(): EditLayoutResource
    {
        return new EditLayoutResource($this);
    }

    public function toJson(): array
    {
        return json_decode($this->toResponse()->content(), true);
    }
}
