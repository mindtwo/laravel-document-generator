<?php

namespace mindtwo\DocumentGenerator\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldGenerateEvent;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent;
use mindtwo\DocumentGenerator\Modules\Content\Listeners\DocumentShouldGenerateListener;
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
        }

        Event::listen(DocumentShouldGenerateEvent::class, DocumentShouldGenerateListener::class);
        Event::listen(DocumentShouldSaveToDiskEvent::class, DocumentShouldSaveListener::class);

        $this->loadMigrationsFrom($this->getMigrationsPath());
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
            return new DocumentService();
        });
    }

    /**
     * Get our migration paths
     */
    private function getMigrationsPath(): array
    {
        return is_array(config('documents.migrations_path')) ?
            config('documents.migrations_path') : [config('documents.migrations_path')];
    }
}
