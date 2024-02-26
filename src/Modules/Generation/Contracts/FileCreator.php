<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Contracts;

use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

interface FileCreator
{
    /**
     * Download GeneratedDocument
     *
     * @param GeneratedDocument $generatedDocument
     * @param string|null $downloadName
     * @param bool $inline
     * @return mixed
     */
    public function download(GeneratedDocument $generatedDocument, ?string $downloadName = null, bool $inline = false);

    /**
     * Save GeneratedDocument to disk
     *
     * @param GeneratedDocument $generatedDocument
     * @param string $file_path
     * @param string $file_name
     * @return void
     */
    public function saveToDisk(GeneratedDocument $generatedDocument, string $file_path, string $file_name): void;
}
