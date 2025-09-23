<?php

namespace mindtwo\DocumentGenerator\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use mindtwo\DocumentGenerator\Modules\Content\Listeners\DocumentGeneratedListener;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentGeneratedEvent;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent;
use mindtwo\DocumentGenerator\Modules\Generation\Listeners\DocumentShouldSaveListener;
use mindtwo\DocumentGenerator\Modules\Placeholder\Services\PlaceholderResolver;
use mindtwo\DocumentGenerator\Services\DocumentService;

class DocumentGeneratorProvider extends ServiceProvider
{
    protected $placeholder = [];

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/documents.php' => config_path('documents.php'),
            ], 'documents');

            $this->publishes([
                __DIR__.'/../../database/migrations-v2/create_generated_documents_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_generated_documents_table.php'),
            ], ['migrations', 'documents']);

            $this->commands([
                \mindtwo\DocumentGenerator\Modules\Console\Commands\GenerateMissingDocumentsCommand::class,
                \mindtwo\DocumentGenerator\Modules\Console\Commands\MinfyHtmlGeneratedContentColumn::class,
            ]);
        }

        // Event::listen(DocumentShouldGenerateEvent::class, DocumentShouldGenerateListener::class);
        Event::listen(DocumentGeneratedEvent::class, DocumentGeneratedListener::class);

        Event::listen(DocumentShouldSaveToDiskEvent::class, DocumentShouldSaveListener::class);
        // Event::listen(DocumentShouldSaveToDiskEvent::class, DocumentShouldSaveListener::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/documents.php', 'documents');

        $this->app->singleton(PlaceholderResolver::class, function (Application $app) {
            return new PlaceholderResolver(config('documents'), $this->placeholder);
        });

        $this->app->bind('document', function (Application $app) {
            return new DocumentService;
        });
    }
}
