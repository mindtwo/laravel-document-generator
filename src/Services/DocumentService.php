<?php

namespace mindtwo\DocumentGenerator\Services;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Services\DocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Factory\FileCreatorFactory;

class DocumentService
{

    // TODO implement fake
    private ?GeneratedDocument $generatedDocument = null;

    /**
     * Silent mode
     *
     * @var boolean
     */
    private bool $silent = false;

    public function getGeneratedDocument(): ?GeneratedDocument
    {
        return $this->generatedDocument;
    }

    /**
     * Generate document
     *
     * @param Model $model
     * @param string $documentClass
     * @return self
     */
    public function generate(Model $model, string $documentClass): self
    {
        if (! is_a($documentClass, Document::class, true)) {
            throw new \Exception('Document class not found');
        }

        $document = new $documentClass($model);

        if ($this->silent) {
            $this->generateSilently($document, $model);

            return $this;
        }

        $this->generatedDocument = $document->generate();

        return $this;
    }

    public function firstOrGenerate(Model $model, string $documentClass): self
    {
        if (! is_a($documentClass, Document::class, true)) {
            throw new \Exception('Document class not found');
        }

        $document = new $documentClass($model);

        $generatedDocument = $document->getGeneratedDocument();

        // if document already generated it has content
        if ($generatedDocument->has_content) {
            $this->generatedDocument = $generatedDocument;

            return $this;
        }

        $this->generatedDocument = $document->generate();
        return $this;
    }

    /**
     * Recreate a generated document
     *
     * @param GeneratedDocument $generatedDocument
     * @return self
     */
    public function recreateGeneratedDocument(GeneratedDocument $generatedDocument): self
    {
        $this->generatedDocument = $generatedDocument;

        return $this->recreateForModel($generatedDocument->model, $generatedDocument->document_class);
    }

    /**
     * Recreate document
     *
     * @param Model $model
     * @param string $documentClass
     * @return self
     */
    public function recreateForModel(Model $model, string $documentClass): self
    {
        if (! is_a($documentClass, Document::class, true)) {
            throw new \Exception('Document class not found');
        }

        $document = new $documentClass($model);

        // silently generate the document
        $this->generateSilently($document, $model);

        $this->saveToDisk(null, true);

        return $this;
    }

    /**
     * Recreate the last generated document
     *
     * @return self
     */
    public function recreate(): self
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        return $this->recreateForModel($this->generatedDocument->model, $this->generatedDocument->document_class);
    }

    /**
     * Download GeneratedDocument
     *
     * @param bool $inline
     * @return mixed
     */
    public function download(bool $inline = false)
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        return $this->generatedDocument->download($inline);
    }

    /**
     * Save GeneratedDocument to disk
     *
     * @return void
     */
    public function saveToDisk(?string $disk = null, bool $force = false): void
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        if ($this->silent) {
            $this->saveToDiskSilently($disk, $force);
            return;
        }

        $this->generatedDocument->saveToDisk($disk, $force);
    }

    /**
     * Set the service to silent mode
     */
    public function silent(): self
    {
        $this->silent = true;

        return $this;
    }

    /**
     * Update the generated document content
     *
     * @param Document $document
     * @param Model $model
     * @return void
     */
    private function generateSilently(Document $document, Model $model): void
    {
        // set if not already done
        if (! $this->generatedDocument) {
            $this->generatedDocument = $document->getGeneratedDocument();
        }

        // recreate document content
        $documentContent = new DocumentContent(
            $document,
            $model,
            $this->generatedDocument->extra ?? [],
        );

        list($resolved, $content) = $documentContent->html();

        $this->generatedDocument->update([
            'resolved_placeholder' => $resolved,
            'content' => $content,
        ]);
    }

    /**
     * Save GeneratedDocument to disk silently without event dispatching
     *
     * @return void
     */
    private function saveToDiskSilently(?string $disk = null, bool $force = false): void
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        // Check if the document is already saved to disk
        if ($this->generatedDocument->is_saved_to_disk && ! $force) {
            return;
        }

        // Set the disk
        $this->generatedDocument->disk = $disk ?? config('documents.files.default_disk');

        $document = $this->generatedDocument->instance;
        // Create the file creator
        $fileCreator = FileCreatorFactory::make($document);

        // update the generated document path and save it to db
        $this->updateGeneratedDocumentPath($document);

        // Create the file on disk
        $fileCreator->saveToDisk($this->generatedDocument, $this->generatedDocument->file_path, $this->generatedDocument->file_name);
    }

    /**
     * Update the generated document path.
     */
    private function updateGeneratedDocumentPath(Document $document): void
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        $file_name = $document->fileName();
        $file_path = $document->filePath();

        // Add the root path to the file path
        $file_path = rtrim(config('documents.files.root_path'), '/')."/$file_path";

        $this->generatedDocument->file_path = $file_path;
        $this->generatedDocument->file_name = $file_name;
        $this->generatedDocument->save();
    }
}
