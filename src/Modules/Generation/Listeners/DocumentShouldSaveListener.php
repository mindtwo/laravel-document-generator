<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Listeners;

use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\SavedBy;

class DocumentShouldSaveListener
{
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(DocumentShouldSaveToDiskEvent $event): void
    {
        $generatedDocument = $event->document;
        $document = $generatedDocument->instance;

        // Get the file saver
        $fileCreator = app()->make($document instanceof SavedBy ? $document->savedBy() : config('documents.file_creator'));
        if (! $fileCreator instanceof FileCreator) {
            throw new \Exception('File saver class not found');
        }

        $this->updateGeneratedDocumentPath($generatedDocument, $document);

        // Create the file on disk
        $fileCreator->saveToDisk($generatedDocument, $generatedDocument->file_path, $generatedDocument->file_name);
    }

    /**
     * Update the generated document path.
     */
    private function updateGeneratedDocumentPath(GeneratedDocument &$generatedDocument, Document $document): void
    {
        $file_name = $document->fileName();
        $file_path = $document->filePath();

        // Add the root path to the file path
        $file_path = rtrim(config('documents.files.root_path'), '/') . "/$file_path";

        $generatedDocument->file_path = $file_path;
        $generatedDocument->file_name = $file_name;
        $generatedDocument->save();
    }
}
