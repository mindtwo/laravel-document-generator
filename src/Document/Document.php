<?php

namespace mindtwo\DocumentGenerator\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Enums\DocumentWidth;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Generator\NameGenerator;
use mindtwo\DocumentGenerator\Models\DocumentBlock;
use mindtwo\DocumentGenerator\Models\DocumentLayout;
use mindtwo\DocumentGenerator\Models\HasDocument;

abstract class Document
{
    /**
     * @var Model
     */
    protected $model = null;

    /**
     * Selectable placeholder in editor context.
     *
     * @var array
     */
    protected $placeholder = [];

    /**
     * Layout name.
     *
     * @var ?string
     */
    protected $layout_name = null;

    protected $show_border = false;

    /**
     * Documents orientation.
     *
     * @var DocumentOrientation
     */
    protected $document_orientation = DocumentOrientation::Landscape;

    /**
     * Documents orientation.
     *
     * @var DocumentWidth
     */
    protected $content_width = DocumentWidth::ThreeFourths;

    abstract public function blocks(): array;

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getModelClass(): string
    {
        if (isset($this->model)) {
            return $this->model::class;
        }

        return null;
    }

    public function getShowBorder(): bool
    {
        return $this->show_border;
    }

    /**
     * Get layout name.
     *
     * @return string
     */
    public function hashFields(): array
    {
        if (isset($this->hash_fields)) {
            return $this->hash_fields;
        }

        return [];
    }

    /**
     * Get layout name.
     *
     * @return string
     */
    public function getLayoutName(): string
    {
        if (isset($this->layout_name)) {
            return $this->layout_name;
        }

        return null;
    }

    /**
     * Get DocumentOrientation instance.
     *
     * @return DocumentOrientation
     */
    public function getDocumentOrientation(): DocumentOrientation
    {
        return $this->document_orientation;
    }

    /**
     * Get DocumentWidth instance.
     *
     * @return DocumentWidth
     */
    public function getContentWidth(): DocumentWidth
    {
        return $this->content_width;
    }

    /**
     * Selectable placeholder in editor context.
     *
     * @return array
     */
    public function getPlaceholder(): array
    {
        return $this->placeholder;
    }

    /**
     * Set model for current document.
     *
     * @param  Model  $model
     * @return Document
     */
    public function for(Model $model): self
    {
        if ($model instanceof HasDocument) {
            $this->model = $model;
        }

        return $this;
    }

    /**
     * Set document orientation.
     *
     * @param  DocumentOrientation  $documentOrientation
     * @return Document
     */
    public function setOrientation(DocumentOrientation $documentOrientation): self
    {
        $this->document_orientation = $documentOrientation;

        return $this;
    }

    /**
     * Set document content width.
     *
     * @param  DocumentWidth  $documentWidth
     * @return Document
     */
    public function setContentWidth(DocumentWidth $documentWidth): self
    {
        $this->content_width = $documentWidth;

        return $this;
    }

    /**
     * Set Layoutname.
     *
     * @param  string  $layoutName
     * @return self
     */
    public function setLayoutName(string $layoutName): self
    {
        $this->layout_name = $layoutName;

        return $this;
    }

    /**
     * Set showBorder.
     *
     * @param  bool  $showBorder
     * @return self
     */
    public function setShowBorder(bool $showBorder): self
    {
        $this->show_border = $showBorder;

        return $this;
    }

    public function loadLayout(): self
    {
        $layout = $this->getDocumentLayout();

        if (is_null($layout)) {
            return $this;
        }

        $this->setOrientation($layout->orientation);
        $this->setContentWidth($layout->content_width);
        $this->setShowBorder($layout->show_border);

        return $this;
    }

    public function documentBlocks(): Collection
    {
        $layout = $this->getDocumentLayout();

        return $layout->blocks()->get();
    }

    /**
     * Get document file name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (! is_null(config('documents.files.name_generator'))) {
            $generatorClass = config('documents.files.name_generator');

            if (is_subclass_of($generatorClass, NameGenerator::class)) {
                $resolveContext = Config::get('documents.context') ?? ResolveContext::Generate;

                return (new $generatorClass($this))->getName($resolveContext);
            }
        }

        $layoutName = $this->getLayoutName();
        $modelType = $this->getModelType();

        return Str::snake("$modelType $layoutName");
    }

    private function getDocumentLayout(): ?DocumentLayout
    {
        return $this->model->document;
    }

    /**
     * Get modelType of document which is the class name without namespace.
     *
     * @return string
     */
    private function getModelType(): string
    {
        return Str::of($this->model->document->model_type)->afterLast('\\')->toString();
    }

    /**
     * Save our layout in migration.
     *
     * @return void
     */
    public function migrate(): void
    {
        $documentLayout = new DocumentLayout;
        $documentLayout->orientation = $this->getDocumentOrientation()->value;
        $documentLayout->show_border = $this->getShowBorder();
        $documentLayout->placeholder = $this->getPlaceholder();

        $this->model->document()->save($documentLayout);

        foreach ($this->blocks() as $index => $block) {
            $newBlock = new DocumentBlock;

            $newBlock->fill([
                'name' => $block->name(),
                'position' => $block->position() ?? $index + 1,
                'template' => $block->template(),
                'show' => $block->show(),
                'block_type' => $block::class,
            ]);

            $documentLayout->blocks()->save($newBlock);
        }
    }
}
