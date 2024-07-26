<?php

namespace mindtwo\DocumentGenerator\Modules\Content\Services;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Layouts\Layout;
use mindtwo\DocumentGenerator\Modules\Content\Traits\ResolvesDocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;

class DocumentContent
{
    use ResolvesDocumentContent;

    protected PlaceholderResolver $placeholderResolver;

    public function __construct(
        protected Document $document,
        protected Model $model,
        protected array $extra = [],
        protected bool $fake = false,
    ) {
        $this->placeholderResolver = app(PlaceholderResolver::class);
    }

    protected function getLayout(): ?Layout
    {
        return $this->document->layout();
    }

    protected function getBlocks(): array
    {
        return $this->document->blocks();
    }

    protected function getDocument(): ?Document
    {
        return $this->document;
    }

    protected function getDocumentClass(): ?string
    {
        return get_class($this->document);
    }
}
