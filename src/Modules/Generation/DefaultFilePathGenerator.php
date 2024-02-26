<?php

namespace mindtwo\DocumentGenerator\Modules\Generation;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileNameGenerator;

class DefaultFilePathGenerator implements FileNameGenerator
{

    /**
     * Generate a file path
     *
     * @param GeneratedDocument $document
     * @return string
     */
    public function generate(GeneratedDocument $document): string
    {
        return $document->id;
    }
}
