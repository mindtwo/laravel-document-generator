<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface HasDocument
{
    /**
     * Get the document relation.
     */
    public function generatedDocument(): MorphOne;

    /**
     * Get the document class.
     *
     * @return class-string<\mindtwo\DocumentGenerator\Modules\Document\Document>
     */
    public function getDocumentClass(): string;

}
