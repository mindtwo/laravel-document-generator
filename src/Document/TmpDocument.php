<?php

namespace mindtwo\DocumentGenerator\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use mindtwo\DocumentGenerator\Block\BladeBlock;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Enums\DocumentWidth;
use mindtwo\DocumentGenerator\Models\DocumentBlock;
use mindtwo\DocumentGenerator\Models\DocumentLayout;

class TmpDocument extends Document
{
    /**
     * Document settings
     *
     * @var array
     */
    private $documentSettings;

    public function __construct(
        array $documentSettings,
        Model $model,
    ) {
        $this->documentSettings = $documentSettings;
        $this->model = $model;
    }

    public function blocks(): array
    {
        if ($this->documentSettings['blocks']) {
            return $this->documentSettings['blocks'];
        }

        return [];
    }

    public function documentBlocks(): Collection
    {
        $layout = DocumentLayout::query()->forModel($this->model)->first();

        return collect($this->blocks())->map(function ($block) use ($layout) {
            $documentBlock = $layout->blocks()->where('name', $block['name'])->first();
            $isBladeBlock = $documentBlock->blockType === BladeBlock::class;

            return (new DocumentBlock)->fill([
                'show' => $block['show'],
                'name' => $documentBlock->name,
                'blockType' => $documentBlock->blockType,
                'template' => $isBladeBlock ? $documentBlock->template : $block['content'],
                'position' => 1,
            ]);
        });
    }

    public function getLayoutName(): string
    {
        return $this->documentSettings['layoutName'];
    }

    public function getDocumentOrientation(): DocumentOrientation
    {
        $documentOrientation = DocumentOrientation::tryFrom($this->documentSettings['orientation']);

        return $documentOrientation;
    }

    public function getContentWidth(): DocumentWidth
    {
        $contentWidth = DocumentWidth::tryFrom($this->documentSettings['contentWidth']);

        return $contentWidth;
    }

    public function getShowBorder(): bool
    {
        return $this->documentSettings['showBorder'] ?? false;
    }
}
