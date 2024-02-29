<?php

namespace mindtwo\DocumentGenerator\Services;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Content\Services\DocumentContent;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

class DocumentService
{

    // TODO implement fake
    private ?GeneratedDocument $generatedDocument = null;

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

        // set if not already done
        if (! $this->generatedDocument) {
            $this->generatedDocument = $document->getGeneratedDocument();
        }

        // recreate document content
        $documentContent = new DocumentContent(
            $document,
            $model
        );

        list($resolved, $content) = $documentContent->html();

        $this->generatedDocument->update([
            'resolved_placeholder' => $resolved,
            'content' => $content,
        ]);

        $this->generatedDocument->saveToDisk(null, true);

        return $this;
    }

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
    public function saveToDisk(): void
    {
        if (! $this->generatedDocument) {
            throw new \Exception('Document not generated');
        }

        $this->generatedDocument->saveToDisk();
    }
}
