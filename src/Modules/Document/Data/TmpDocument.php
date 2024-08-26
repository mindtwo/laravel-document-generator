<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Data;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Modules\Content\Layouts\Layout;
use mindtwo\DocumentGenerator\Modules\Content\Services\TmpDocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\DocumentHolder;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Generation\Factory\FileCreatorFactory;
use Spatie\LaravelData\Data;

class TmpDocument extends Data implements DocumentHolder
{
    private string $content;

    private array $resolvedPlaceholder = [];

    /**
     * @var class-string<Document>
     */
    public function __construct(
        private Model $model,
        private string $documentClass,
        private ?Document $instance = null,
        private array $extra = [],
        protected array $blocks = [],
        protected ?Layout $layout = null,
        private bool $fake = true,
    ) {
        $documentContent = new TmpDocumentContent(
            $model,
            $instance,
            $extra,
            $blocks,
            $documentClass,
            $layout,
        );

        $documentContent->fake($this->fake);

        [$this->resolvedPlaceholder, $this->content] = $documentContent->html();
    }

    public function getDocumentable(): Model
    {
        return $this->model;
    }

    public function documentClass(): string
    {
        return $this->documentClass;
    }

    public function documentInstance(): Document
    {
        if ($this->instance === null) {
            $this->instance = new $this->documentClass($this->model);
        }

        return $this->instance;
    }

    public function getFileName(): ?string
    {
        $classBase = class_basename($this->documentClass);

        return Str::of($classBase)
            ->prepend('tmp-')
            ->append(Str::random(10))
            ->lower()
            ->append('.pdf')
            ->snake()
            ->__toString();
    }

    public function diskInstance(): ?Filesystem
    {
        return null;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Download the document.
     */
    public function download()
    {
        $fileCreator = FileCreatorFactory::make($this);

        return $fileCreator->download($this, $this->getFileName(), true);
    }
}
