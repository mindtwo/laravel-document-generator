<?php

namespace mindtwo\DocumentGenerator\Modules\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\Commandable;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class GenerateMissingDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:generate-missing {document : Alias or document class that indicates the type we want to recreate} {--force : Force recreation of all documents} {--dry-run : Do not recreate documents} {--exclude-id=* : Ids we want to exclude from generation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing documents.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $documentClass = $this->getDocumentClass($this->argument('document'));

        // validate documentClass class
        if (! $documentClass || ! is_a($documentClass, Commandable::class, true)) {
            return ConsoleCommand::FAILURE;
        }

        $this->info("Generating missing $documentClass for model", 'v');

        // get all models with missing documents
        $missingDocuments = $this->getModelsWithMissingDocument($documentClass);
        if ($missingDocuments->isEmpty()) {
            $this->info('No missing documents found.');

            return ConsoleCommand::SUCCESS;
        }

        $this->info(sprintf('Found %d models with missing documents for %s.', count($missingDocuments), $documentClass));
        $this->info(sprintf('Generating documents for model with ids: %s.', $missingDocuments->pluck('id')->join(', ')), 'vv');

        if ($this->option('dry-run')) {
            $this->info('Dry run, no documents will be generated.');

            return ConsoleCommand::SUCCESS;
        }

        // generate documents
        $missingDocuments->each(function (Model $model) use ($documentClass) {
            document($model, $documentClass)->saveToDisk();
        });

        return ConsoleCommand::SUCCESS;
    }

    /**
     * Get all models with missing documents.
     */
    private function getModelsWithMissingDocument(string $documentClass): Collection
    {
        $eligibleModels = $documentClass::getEligibleModels($documentClass);

        if ($eligibleModels->isEmpty() || $this->option('force')) {
            return $eligibleModels;
        }

        $morphClass = $eligibleModels->first()->getMorphClass();

        $existingDocuments = GeneratedDocument::where('document_class', $documentClass)
            ->where('documentable_type', $morphClass)
            ->whereIn('documentable_id', $eligibleModels->pluck('id'))
            ->get()
            ->pluck('documentable_id');

        $excludedIds = $this->option('exclude-id') ?? [];

        return $eligibleModels->filter(function (Model $model) use ($existingDocuments, $excludedIds) {
            return ! $existingDocuments->contains($model->id) && ! in_array($model->id, $excludedIds);
        });
    }

    private function getDocumentClass(string $document): ?string
    {
        if (is_a($document, 'mindtwo\DocumentGenerator\Modules\Document\Contracts\Document', true)) {
            return $document;
        }

        return config('documents.aliases.' . $document);
    }
}