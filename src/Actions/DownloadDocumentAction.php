<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadDocumentAction
{
    /**
     * Download document by fileName. A filePath and diskName may be passed.
     *
     * @param  string  $fileName - name of file
     * @param  string  $filePath - path in filesystem
     * @param  string|null  $disk - name of filesystem
     * @return StreamedResponse
     */
    public function execute(string $fileName, string $filePath = '', ?string $disk = null): StreamedResponse
    {
        $disk = $this->getDiskInstance($disk);

        return $disk->download("{$filePath}/{$fileName}");
    }

    /**
     * Download document file from GeneratedDocument instance.
     *
     * @param  GeneratedDocument  $generatedDocument
     * @return StreamedResponse
     */
    public function executeWithDocument(GeneratedDocument $generatedDocument): StreamedResponse
    {
        if (! $generatedDocument->saved_to_disk) {
            throw new \Exception('Document was not saved to disk', 1);
        }

        return $this->execute($generatedDocument->fileName, $generatedDocument->filePath, $generatedDocument->disk);
    }

    /**
     * Get disk instance for the file system.
     *
     * @return Filesystem
     */
    private function getDiskInstance(?string $diskName): Filesystem
    {
        if (is_null($diskName)) {
            $diskName = config('documents.files.disk') ?? 'local';
        }

        return Storage::disk($diskName);
    }
}
