<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;

class DomPdfFileCreator implements FileCreator
{
    public function __construct()
    {
    }

    public function download(GeneratedDocument $generatedDocument, ?string $downloadName = null, bool $inline = false)
    {
        $document = $generatedDocument->instance;
        $options = $this->getDomPdfOptions();

        $dompdf = $this->getDompdf($options, $generatedDocument, $document);

        // get the download name
        $downloadName = $downloadName ?? $document->fileName() ?? 'document.pdf';
        if (! str_ends_with($downloadName, '.pdf')) {
            $downloadName .= '.pdf';
        }

        $dompdf->stream($downloadName ?? 'document.pdf', [
            'Content-Disposition' => ($inline ? 'inline' : 'attachment')."; filename={$generatedDocument->file_name}",
        ]);
        exit(0);
    }

    /**
     * Save GeneratedDocument to disk
     *
     * @param GeneratedDocument $generatedDocument
     * @param string $file_path
     * @param string $file_name
     * @return void
     */
    public function saveToDisk(GeneratedDocument $generatedDocument, string $file_path, string $file_name): void
    {
        $document = $generatedDocument->instance;
        $options = $this->getDomPdfOptions();

        $dompdf = $this->getDompdf($options, $generatedDocument, $document);
        $file = $dompdf->output();

        if (! str_ends_with($file_name, '.pdf')) {
            $file_name = "$file_name.pdf";

            $generatedDocument->update([
                'file_name' => $file_name,
            ]);
        }

        $generatedDocument->diskInstance()->put("$file_path/$file_name", $file);
    }

    protected function getDompdf(Options $options, GeneratedDocument $generatedDocument, Document $document): Dompdf
    {
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($generatedDocument->content);
        $dompdf->setPaper('A4', $document->documentOrientation());

        $dompdf->render();

        return $dompdf;
    }

    protected function getDompdfOptions(): Options
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        // only allow remote files from our env url
        $options->setAllowedProtocols([
            'http://' => false,
            'https://' => true, // TODO define rules in config
            'file://' => false,
        ]);
        $options->setTempDir(config('documents.files.tmp') ?? '/tmp/documents');

        return $options;
    }
}
