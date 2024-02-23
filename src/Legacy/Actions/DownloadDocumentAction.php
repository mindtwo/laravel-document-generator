<?php

namespace mindtwo\DocumentGenerator\Actions;

use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Models\GeneratedDocument;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadDocumentAction
{

    /**
     * Download given document.
     *
     * @param GeneratedDocument $document
     * @param boolean $stream
     * @param boolean $inline
     * @return StreamedResponse
     */
    public function __invoke(GeneratedDocument $document, bool $stream = false, bool $inline = false): StreamedResponse
    {
        if (!$document->saved_to_disk) {
            abort(404);
        }

        $disk = $document->diskInstance();

        return $disk->download($document->full_path, $document->file_name, [
            "Content-Disposition" => ($inline ? 'inline' : 'attachment') . "; filename={$document->file_name}",
        ]);
    }

    /**
     * Download document by fileName. A filePath and diskName may be passed.
     *
     * @param  string  $fileName - name of file
     * @param  string  $filePath - path in filesystem
     * @param  string|null  $disk - name of filesystem
     * @return StreamedResponse
     */
    public function execute(string $fileName, string $filePath = '', ?string $disk = null, bool $inline = false): StreamedResponse
    {
        if (is_null($disk)) {
            $disk = config('documents.files.disk') ?? 'local';
        }

        $disk = Storage::disk($disk);
        return $disk->download("$filePath/$fileName", $fileName, [
            "Content-Disposition" => ($inline ? 'inline' : 'attachment') . "; filename={$fileName}",
        ]);
    }
}
