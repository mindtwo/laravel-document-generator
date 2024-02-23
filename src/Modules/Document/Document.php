<?php

namespace mindtwo\DocumentGenerator\Modules\Document;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Blocks\Block;
use mindtwo\DocumentGenerator\Modules\Content\Layouts\BladeLayout;
use mindtwo\DocumentGenerator\Modules\Document\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Modules\Document\Enums\DocumentWidth;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldGenerateEvent;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileNameGenerator;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FilePathGenerator;

abstract class Document
{
    /**
     * @var class-string<FilePathGenerator>
     */
    protected ?string $filePathGenerator = null;

     /**
     * @var class-string<FileNameGenerator>
     */
    protected ?string $fileNameGenerator = null;

    /**
     * The finalized placeholder.
     * Add placeholder names that should not be changed,
     * if the document and the blocks are regenerated.
     *
     * @var array
     */
    protected array $finalizedPlaceholder = [];

    /**
     * Documents orientation.
     *
     * @var DocumentOrientation
     */
    protected $documentOrientation = DocumentOrientation::Landscape;

    /**
     * Documents orientation.
     *
     * @var DocumentWidth
     */
    protected $contentWidth = DocumentWidth::ThreeFourths;

    public function __construct(
        private Model $model,
    ) {

    }

    /**
     * Get the blocks in order to render in the document.
     *
     * @return array<Block>
     */
    abstract public function blocks(): array;

    abstract public function layout(): BladeLayout;

    /**
     * Get the file path generator class.
     *
     * @return class-string<FilePathGenerator>|null
     */
    protected function filePathGenerator(): ?string
    {
        return isset($this->filePathGenerator) ? $this->filePathGenerator : null;
    }

    /**
     * Get the file path generator class.
     *
     * @return class-string<FilePathGenerator>|null
     */
    protected function fileNameGenerator(): ?string
    {
        return isset($this->fileNameGenerator) ? $this->fileNameGenerator : null;
    }

    /**
     * Get the file path as string.
     */
    public function filePath(): string
    {
        $filePathGenerator = $this->filePathGenerator() ?? config('documents.files.file_path_generator');
        return rtrim(app($filePathGenerator)->generate($this->getGeneratedDocument()), '/');
    }

    /**
     * Get the file name as string.
     */
    public function fileName(): string
    {
        $fileNameGenerator = $this->fileNameGenerator() ?? config('documents.files.file_name_generator');

        return app($fileNameGenerator)->generate($this->getGeneratedDocument());
    }

    /**
     * Get the content width as string.
     */
    public function contentWidth(): string
    {
        return $this->contentWidth->value;
    }

    /**
     * Get the document orientation as string.
     */
    public function documentOrientation(): string
    {
        return $this->documentOrientation->value;
    }

    public function getDocumentOrientation(): DocumentOrientation
    {
        return $this->documentOrientation;
    }

    /**
     * Get the finalized placeholder.
     */
    public function getFinalizedPlaceholder(): array
    {
        return $this->finalizedPlaceholder;
    }

    /**
     * Get the generated document model.
     */
    public function generate(): GeneratedDocument
    {
        $generatedDocument = $this->getGeneratedDocument();

        DocumentShouldGenerateEvent::dispatch($generatedDocument);

        return $this->getGeneratedDocument();
    }

    /**
     * Get the generated document model.
     */
    public function getGeneratedDocument(): GeneratedDocument
    {
        return GeneratedDocument::firstOrCreate([
            'documentable_id' => $this->model->id,
            'documentable_type' => $this->model->getMorphClass(),
            'document_class' => static::class,
        ]);
    }

    public static function make(Model $model): static
    {
        return new static($model);
    }

}
