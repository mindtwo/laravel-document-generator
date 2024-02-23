<?php

namespace Tests\Fake\Modules\Document;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileNameGenerator;

class TestFileNameGenerator implements FileNameGenerator
{
    public function generate(GeneratedDocument $document): string
    {
        return 'test_file_name_' . $document->id;
    }
}
