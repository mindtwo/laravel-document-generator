<?php

namespace mindtwo\DocumentGenerator\Actions;

use mindtwo\DocumentGenerator\Document\Document;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Services\DocumentGenerator;

class RecreateDocumentFile
{
    public function __invoke(GeneratedDocument $generatedDocument, Document $document): bool
    {
        $documentGenerator = app(DocumentGenerator::class);

        $filePath = $documentGenerator->saveToFile($generatedDocument, $document, false, true);

        if (is_null($filePath)) {
            throw new \Exception('Error while trying to recreate the document', 1);
        }

        return $generatedDocument->save();
    }

}
