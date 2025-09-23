<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Commands;

use Illuminate\Console\Command;
use mindtwo\DocumentGenerator\Modules\Console\Actions\OptimizeMissingDocumentsAction;
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

        app(OptimizeMissingDocumentsAction::class)($chunkSize, $queue);

        // Output a message to indicate the command has completed
        $this->info('HTML content minification started successfully.');

        return ConsoleCommand::SUCCESS;
    }
}
