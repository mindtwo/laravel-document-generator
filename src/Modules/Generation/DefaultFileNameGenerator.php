<?php

namespace mindtwo\DocumentGenerator\Modules\Generation;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileNameGenerator;

class DefaultFileNameGenerator implements FileNameGenerator
{

    /**
     * Generate a file name.
     *
     * @param GeneratedDocument $document
     * @return string
     */
    public function generate(GeneratedDocument $document): string
    {
        $documentClassName = class_basename($document->document_class);
        $documentClassName = str_replace('Document', '', $documentClassName);
        $documentClassName = strtolower($documentClassName);

        $documentId = str_replace('-', '', $document->uuid);

        return "{$documentId}_{$documentClassName}.pdf";
    }
}
