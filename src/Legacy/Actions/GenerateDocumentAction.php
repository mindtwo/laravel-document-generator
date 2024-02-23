<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Document\Document;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Services\DocumentGenerator;

class GenerateDocumentAction
{
    public function __construct(
        protected DocumentGenerator $documentGenerator,
    ) {
    }

    public function execute(Document $document, Model $model)
    {
        $this->documentGenerator->setResolveContext(ResolveContext::Generate);

        $generatedDocument = $this->documentGenerator->generateDocument($document, $model);

        if ($generatedDocument->saved_to_db) {
            return ['success' => true, 'documentId' => $generatedDocument->uuid, 'url' => route('documents.download', ['documentId' => $generatedDocument->uuid])];
        }

        $filePath = $this->documentGenerator->saveToFile($generatedDocument, $document);

        if (is_null($filePath)) {
            $this->documentGenerator->resetResolveContext();
            abort(400);
        }

        $this->documentGenerator->resetResolveContext();

        $saved = $this->documentGenerator->saveToDatabase($generatedDocument, $model);

        return ['success' => $saved, 'documentId' => $generatedDocument->uuid, 'url' => route('documents.download', ['documentId' => $generatedDocument->uuid])];
    }
}
