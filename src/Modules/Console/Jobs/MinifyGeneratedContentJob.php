<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use mindtwo\DocumentGenerator\Modules\Document\Actions\MinifyHtmlContent;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

class MinifyGeneratedContentJob implements ShouldQueue
{
    use Batchable,
        Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    public function __construct(
        public int $startId,
        public int $endId,
        public int $chunkSize,
    ) {}

    /**
     * Execute the job to minify HTML content in the specified range of generated documents.
     */
    public function handle(): void
    {
        // Fetch all generated documents with non-null content
        GeneratedDocument::query()
            ->select('id', 'content')
            ->whereNotNull('content')
            ->whereBetween('id', [$this->startId, $this->endId])
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($documents) {
                foreach ($documents as $doc) {
                    // Minify the content
                    $minifiedContent = (new MinifyHtmlContent)($doc->content);

                    if ($doc->content === $minifiedContent) {
                        // If content is unchanged, skip updating
                        return;
                    }

                    // Update the document with the minified content
                    DB::table('generated_documents')
                        ->where('id', $doc->id)
                        ->update(['content' => $minifiedContent]);
                }
            });
    }
}
