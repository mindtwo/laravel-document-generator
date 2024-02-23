<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Factory;

use mindtwo\DocumentGenerator\Modules\Document\Contracts\SavedBy;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;

class FileCreatorFactory
{

    public static function make(GeneratedDocument|Document $document): FileCreator
    {
        // Get the document instance
        $document = $document instanceof Document ? $document : $document->instance;

        // Get the file saver
        $fileCreator = app()->make($document instanceof SavedBy ? $document->savedBy() : config('documents.file_creator'));

        if (! $fileCreator instanceof FileCreator) {
            throw new \Exception('File saver class not found');
        }

        return $fileCreator;
    }

}
