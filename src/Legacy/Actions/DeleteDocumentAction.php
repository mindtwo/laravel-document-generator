<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class DeleteDocumentAction
{
    public function execute(string $fileName, string $filePath = '', ?string $disk = null)
    {
        $disk = $this->getDiskInstance($disk);

        return $disk->delete("{$filePath}/{$fileName}");
    }

    /**
     * Get disk instance for the file system
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
