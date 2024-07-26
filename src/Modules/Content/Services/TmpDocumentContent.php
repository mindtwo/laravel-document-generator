<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Services;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Layouts\Layout;
use mindtwo\DocumentGenerator\Modules\Content\Traits\ResolvesDocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Document;

class TmpDocumentContent
{
    use ResolvesDocumentContent;

    protected bool $fake = true;

    /**
     * @param  class-string<Document>|null  $documentClass
     */
    public function __construct(
        protected Model $model,
        protected ?Document $document = null,
        protected array $extra = [],
        protected array $blocks = [],
        protected ?string $documentClass = null,
        protected ?Layout $layout = null,
    ) {
    }

    protected function getLayout(): ?Layout
    {
        if ($this->layout !== null) {
            return $this->layout;
        }

        if ($this->document !== null) {
            return $this->document->layout();
        }

        $document = $this->getDocument();

        return $document ? $document->layout() : null;
    }

    protected function getBlocks(): array
    {
        if ($this->document !== null) {
            return $this->document->blocks();
        }

        return $this->blocks;
    }

    protected function getDocument(): ?Document
    {
        if ($this->document !== null) {
            return $this->document;
        }

        $documentClass = $this->getDocumentClass();

        return $documentClass ? new $documentClass($this->model) : null;
    }

    protected function getDocumentClass(): ?string
    {
        if ($this->documentClass !== null) {
            return $this->documentClass;
        }

        if ($this->document !== null) {
            return get_class($this->document);
        }

        return null;
    }
}
