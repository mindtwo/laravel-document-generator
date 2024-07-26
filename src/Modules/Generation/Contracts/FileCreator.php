<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Contracts;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\DocumentHolder;

interface FileCreator
{
    /**
     * Download GeneratedDocument
     *
     * @param DocumentHolder $documentHolder
     * @param string|null $downloadName
     * @param bool $inline
     * @return mixed
     */
    public function download(DocumentHolder $documentHolder, ?string $downloadName = null, bool $inline = false);

    /**
     * Save GeneratedDocument to disk
     *
     * @param DocumentHolder $generatedDocument
     * @param string $file_path
     * @param string $file_name
     * @return void
     */
    public function saveToDisk(DocumentHolder $documentHolder, string $file_path, string $file_name): void;
}
