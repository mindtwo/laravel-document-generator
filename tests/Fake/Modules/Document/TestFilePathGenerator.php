<?php

namespace Tests\Fake\Modules\Document;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FilePathGenerator;

class TestFilePathGenerator implements FilePathGenerator
{
    public function generate(GeneratedDocument $document): string
    {
        return strtolower(substr($document->uuid, 0, 2)) . '/' . strtolower(substr($document->uuid, 2, 2));
    }
}
