<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasDocuments
{
    /**
     * Get the document relation.
     */
    public function generatedDocuments(): MorphMany;

    /**
     * Get the document class.
     *
     * @return class-string<\mindtwo\DocumentGenerator\Modules\Document\Document>
     */
    public function getDocumentClass(): string;

}
