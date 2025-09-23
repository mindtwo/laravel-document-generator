<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use mindtwo\DocumentGenerator\Modules\Console\Jobs\MinifyGeneratedContentJob;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class MinfyHtmlGeneratedContentColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:minify-html-content { --chunk-size=100 : The number of documents to process in each chunk } { --queue=default : The queue to dispatch the jobs to }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Minify HTML content in the generated documents';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Output a message to indicate the command has started
        $this->info('Starting to minify HTML content in generated documents...');

        // Get the chunk size from the command option
        $chunkSize = (int) $this->option('chunk-size');
        // Get the queue name from the command option
        $queue = $this->option('queue');

        dump(GeneratedDocument::query()
            ->selectRaw('MIN(id) as min_id, MAX(id) as max_id')
            ->first(['min_id', 'max_id'])
            ->toArray());

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

        // Output a message to indicate the command has completed
        $this->info('HTML content minification started successfully.');

        return ConsoleCommand::SUCCESS;
    }
}
