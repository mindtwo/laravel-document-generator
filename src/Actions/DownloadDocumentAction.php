<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadDocumentAction
{

    /**
     * Download given document.
     *
     * @param GeneratedDocument $document
     * @param boolean $stream
     * @param boolean $inline
     * @return BinaryFileResponse
     */
    public function __invoke(GeneratedDocument $document, bool $stream = false, bool $inline = false): BinaryFileResponse
    {
        $disk = $this->getDiskInstance($document->disk);

        return response()->download($disk->path("{$document->file_path}/{$document->file_name}"), $document->file_name, [], $inline ? 'inline' : 'attachment');
    }

    /**
     * Download document by fileName. A filePath and diskName may be passed.
     *
     * @param  string  $fileName - name of file
     * @param  string  $filePath - path in filesystem
     * @param  string|null  $disk - name of filesystem
     * @return BinaryFileResponse
     */
    public function execute(string $fileName, string $filePath = '', ?string $disk = null): BinaryFileResponse
    {
        $disk = $this->getDiskInstance($disk);

        return response()->download($disk->path("{$filePath}/{$fileName}"), $fileName);
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
