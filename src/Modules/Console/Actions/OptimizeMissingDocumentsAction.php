<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Actions;

use Illuminate\Support\Facades\Bus;
use mindtwo\DocumentGenerator\Modules\Console\Jobs\MinifyGeneratedContentJob;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;

class OptimizeMissingDocumentsAction
{
    /**
     * Execute the action to optimize missing documents.
     */
    public function __invoke(int $chunkSize = 100, string $queue = 'default'): void
    {
        // Get the minimum and maximum IDs of the generated documents
        $minMaxIds = GeneratedDocument::query()
            ->selectRaw('MIN(id) as min_id, MAX(id) as max_id')
            ->first(['min_id', 'max_id'])
            ->toArray();

        $minId = $minMaxIds['min_id'] ?? 0;
        $maxId = $minMaxIds['max_id'] ?? 0;

        // Start the job batch
        $jobs = [];

        for ($start = $minId; $start <= $maxId; $start += $chunkSize) {
            $jobs[] = new MinifyGeneratedContentJob($start, min($start + $chunkSize - 1, $maxId), $chunkSize);
        }

        // Dispatch the jobs in a batch
        Bus::batch($jobs)
            ->name('Minify content')
            ->allowFailures()
            ->onQueue($queue)
            ->dispatch();

    }
}
