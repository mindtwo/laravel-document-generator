<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Contracts;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

interface FilePathGenerator
{
    /**
     * Generate file path for document
     *
     * @param  GeneratedDocument  $document
     * @return string
     */
    public function generate(GeneratedDocument $document): string;
}
