<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Document\TmpDocument;
use mindtwo\DocumentGenerator\Enums\ResolveContext;
use mindtwo\DocumentGenerator\Services\DocumentGenerator;

class GenerateTmpDocumentAction
{
    public function __construct(
        protected DocumentGenerator $documentGenerator,
    ) {
    }

    public function execute(array $documentSettings, Model $model)
    {
        $this->documentGenerator->setResolveContext(ResolveContext::Preview);

        $document = new TmpDocument($documentSettings, $model);

        $generatedDocument = $this->documentGenerator->generateDocument($document, $model);
        $filePath = $this->documentGenerator->saveToFile($generatedDocument, $document, true);

        if (is_null($filePath)) {
            $this->documentGenerator->resetResolveContext();
            abort(400);
        }

        $fileName = basename($filePath);

        $this->documentGenerator->resetResolveContext();

        return route('documents.tmp', ['fileName' => $fileName]);
    }
}
