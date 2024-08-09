<?php

namespace mindtwo\DocumentGenerator\Modules\Generation\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\DocumentHolder;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DomPdfFileCreator implements FileCreator
{
    public function __construct()
    {
    }

    /**
     * Download GeneratedDocument
     *
     * @return mixed
     */
    public function download(DocumentHolder $documentHolder, ?string $downloadName = null, bool $inline = false)
    {
        $document = $documentHolder->documentInstance();
        $options = $this->getDomPdfOptions();

        $dompdf = $this->getDompdf($options, $documentHolder, $document);

        // get the download name
        $downloadName = $downloadName ?? $document->fileName() ?? 'document.pdf';
        if (! str_ends_with($downloadName, '.pdf')) {
            $downloadName .= '.pdf';
        }

        $fileName = $documentHolder->getFileName() ?? 'document.pdf';
        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition($inline ? 'inline' : 'attachment', $downloadName, $fileName),
            'Content-Length' => strlen($output),
        ]);
    }

    /**
     * Save GeneratedDocument to disk
     *
     * @param  DocumentHolder  $generatedDocument
     */
    public function saveToDisk(DocumentHolder $documentHolder, string $file_path, string $file_name): void
    {
        $document = $documentHolder->documentInstance();
        $options = $this->getDomPdfOptions();

        $dompdf = $this->getDompdf($options, $documentHolder, $document);
        $file = $dompdf->output();

        $fileName = $documentHolder->getFileName();
        if (! str_ends_with($fileName, '.pdf')) {
            $file_name = "$fileName.pdf";

            if ($documentHolder instanceof Model) {
                $documentHolder->update([
                    'file_name' => $file_name,
                ]);
            }
        }

        $documentHolder->diskInstance()->put("$file_path/$file_name", $file);
    }

    protected function getDompdf(Options $options, DocumentHolder $documentHolder, Document $document): Dompdf
    {
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($documentHolder->getContent());
        $dompdf->setPaper('A4', $document->documentOrientation());

        $dompdf->render();

        return $dompdf;
    }

    protected function getDompdfOptions(): Options
    {
        $options = new Options();

        // TODO do this settings in config
        $options->set('isRemoteEnabled', config('documents.render.is_remote_enabled', true));
        $options->set('isHtml5ParserEnabled', config('documents.render.is_html5_parser_enabled', true));

        // configure font dir, font cache and chroot
        if ($value = config('documents.render.font_dir')) {
            $options->setFontDir($value);
        }
        if ($value = config('documents.render.font_cache')) {
            $options->setFontCache($value);
        }
        if ($value = config('documents.render.chroot')) {
            $options->setChroot($value);
        }

        // set dpi
        $options->setDpi(config('documents.render.dpi', 96));

        // only allow remote files from our env url
        $options->setAllowedProtocols([
            'http://' => false,
            'https://' => true, // TODO define rules in config
            'file://' => false,
        ]);
        $options->setTempDir(config('documents.files.tmp_path') ?? '/tmp/documents');

        return $options;
    }
}
