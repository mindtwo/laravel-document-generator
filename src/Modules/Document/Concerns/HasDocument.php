<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Concerns;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

trait HasDocument
{

    /**
     * Get the document
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function generatedDocument()
    {
        return $this->morphOne(GeneratedDocument::class, 'documentable');
    }

    /**
     * Get the document class.
     *
     * @return class-string<\mindtwo\DocumentGenerator\Modules\Document\Document>
     */
    public function getDocumentClass(): string
    {
        if (! isset($this->documentClass)) {
            throw new \Exception('Error generating document. Please add a property or mutator named "documentClass" with value class-string of default document', 1);

        }

        return $this->documentClass;
    }
}
