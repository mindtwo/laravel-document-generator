<?php

namespace mindtwo\DocumentGenerator\Services;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use mindtwo\DocumentGenerator\Document\Document;
use mindtwo\DocumentGenerator\Document\Layout;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Events\DocumentCreatedEvent;
use mindtwo\DocumentGenerator\Events\DocumentFileCreatedEvent;
use mindtwo\DocumentGenerator\Events\DocumentSavedEvent;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;

class DocumentGenerator
{
    public function __construct(
        public Filesystem $filesystem,
        public BlockRenderer $blockRenderer,
    ) {
    }

    /**
     * Generate Document.
     * Get pre-filled GeneratedDocument model
     *
     * @param  Document  $document
     * @param  Model  $model
     * @return GeneratedDocument
     */
    public function generateDocument(Document $document, Model $model): GeneratedDocument
    {
        $content = '';
        $fields = collect([]);

        foreach ($document->documentBlocks() as $docBlock) {
            $block = $docBlock->block_type::from($docBlock);

            $renderedBlock = $this->blockRenderer->renderBlock($block, $model, $document->getDocumentOrientation());

            $content .= $renderedBlock->content;

            $fields->push(...array_values($renderedBlock->fields));
        }

        $fields = $fields->unique(fn ($field) => $field->placeholder)->mapWithKeys(fn ($field) => [$field->placeholder => $field->value]);

        $contentHash = $this->generateContentHash($document, $fields, $content);

        if (GeneratedDocument::where('content_hash', $contentHash)->exists()) {
            return GeneratedDocument::where('content_hash', $contentHash)->first();
        }

        $document->loadLayout();
        $layout = new Layout($document);

        $output = $layout->render($content);

        $output = preg_replace('/>\s+</', '><', $output);

        $generatedDocument = new GeneratedDocument;

        $generatedDocument->content = $output;
        $generatedDocument->contentHash = $contentHash;
        $generatedDocument->fields = $fields;

        DocumentCreatedEvent::dispatch($generatedDocument);

        return $generatedDocument;
    }

    /**
     * Save GeneratedDocument to db
     *
     * @param  GeneratedDocument  $generatedDocument
     * @param  Model  $model
     * @return string|null
     */
    public function saveToDatabase(GeneratedDocument $generatedDocument, Model $model, $withoutEvent = false): bool
    {
        if (! $generatedDocument->saved_to_disk || $generatedDocument->saved_to_db) {
            return false;
        }

        $save = $generatedDocument->save();

        if (! $withoutEvent) {
            DocumentSavedEvent::dispatch($generatedDocument, $model);
        }

        return $save;
    }

    /**
     * Save GeneratedDocument to disk
     *
     * @param  GeneratedDocument  $generatedDocument
     * @param  Document  $document
     * @param  bool  $temporary
     * @return string|null
     */
    public function saveToFile(GeneratedDocument &$generatedDocument, Document $document, $temporary = false, $withoutEvent = false): ?string
    {
        $fullPath = $this->getDocumentPath($document, $temporary);
        $fileName = basename($fullPath);

        $filePath = str_replace("/$fileName", '', $fullPath);

        $generatedDocument->file_name = $fileName;
        $generatedDocument->file_path = $filePath;

        if (! $temporary) {
            $diskName = config('documents.files.disk') ?? 'local';

            $generatedDocument->disk = $diskName;
        }

        $savedPath = $this->generatePdf($generatedDocument, $document);

        if (is_null($savedPath)) {
            return null;
        }

        if (! $withoutEvent) {
            DocumentFileCreatedEvent::dispatch($generatedDocument);
        }

        return $savedPath;
    }

    /**
     * Set resolve context for placholders.
     *
     * @param  ResolveContext  $resolveContext
     * @return void
     */
    public function setResolveContext(ResolveContext $resolveContext): void
    {
        Config::set('documents.context', $resolveContext);
    }

    /**
     * Reset reolve context to default value.
     *
     * @return void
     */
    public function resetResolveContext(): void
    {
        Config::set('documents.context', null);
    }

    /**
     * Generate pdf from html string output
     * Use document as helper for orientation here.
     *
     * @param  GeneratedDocument  $generatedDocument
     * @param  Document  $document
     * @return string|null
     */
    private function generatePdf(GeneratedDocument $generatedDocument, Document $document): ?string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        // only allow remote files from our env url
        $options->setAllowedProtocols([
            'http://' => false,
            'https://' => true, // TODO define rules in config
            'file://' => false,
        ]);

        // $options->setFontDir(public_path('storage/assets/fonts'));
        $options->setTempDir(config('documents.files.tmp') ?? '/tmp/documents');

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($generatedDocument->content);

        $dompdf->setPaper('A4', $document->loadLayout()->getDocumentOrientation()->value);

        // Render the HTML as PDF
        $dompdf->render();

        $file = $dompdf->output();

        $filePath = $generatedDocument->full_path;

        if ($this->filesystem->put($filePath, $file)) {
            return $filePath;
        }

        return null;
    }

    /**
     * Get tmp path for document.
     *
     * @param  Document  $document
     * @param  bool  $temporary
     * @return string
     */
    private function getDocumentPath(Document $document, $temporary = false): string
    {
        $timestamp = Carbon::now()->timestamp;
        $docName = $document->getName();

        if ($temporary) {
            $tmpPath = config('documents.files.tmp') ?? '/tmp/documents';

            return "{$tmpPath}/{$timestamp}_{$docName}.pdf";
        }

        $filePath = config('documents.files.file') ?? 'documents';

        return "{$filePath}/{$timestamp}_{$docName}.pdf";
    }

    public function generateContentHash(Document $document, Collection $fields, string $content): string
    {
        if (empty($document->hashFields())) {
            return hash('sha256', $content);
        }

        $fieldStr = collect($document->hashFields())->map(fn ($field) => $fields[$field] ?? '')->join('');

        return hash('sha256', $fieldStr);
    }
}
