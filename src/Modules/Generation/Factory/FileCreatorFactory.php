<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Factory;

use mindtwo\DocumentGenerator\Modules\Document\Contracts\SavedBy;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\DocumentHolder;

class FileCreatorFactory
{

    /**
     * Make a file creator instance
     *
     * @param null|DocumentHolder|GeneratedDocument|Document $document
     * @return FileCreator
     * @throws \Exception
     */
    public static function make($document = null): FileCreator
    {
        // Get the default file creator
        if (is_null($document)) {
            return app()->make(config('documents.file_creator'));
        }

        // Check if the document is an instance of DocumentHolder, GeneratedDocument or Document
        if (! $document instanceof DocumentHolder && ! $document instanceof GeneratedDocument && ! $document instanceof Document) {
            throw new \Exception('Document must be an instance of DocumentHolder, GeneratedDocument or Document');
        }

        // Get the document instance from the generated document
        if ($document instanceof GeneratedDocument) {
            $document = $document->instance;
        }

        // Get the document instance from the holder
        if ($document instanceof DocumentHolder) {
            $document = $document->documentInstance();
        }

        // Check if the document instance is a document
        if (! $document instanceof Document) {
            throw new \Exception('Document instance not found');
        }

        // Get the file saver
        $fileCreator = app()->make($document instanceof SavedBy ? $document->savedBy() : config('documents.file_creator'));

        if (! $fileCreator instanceof FileCreator) {
            throw new \Exception('File saver class not found');
        }

        return $fileCreator;
    }

}
