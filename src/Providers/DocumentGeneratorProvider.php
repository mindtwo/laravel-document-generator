<?php

namespace mindtwo\DocumentGenerator\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use mindtwo\DocumentGenerator\Commands\MakeLayoutMigrationCommand;
use mindtwo\DocumentGenerator\Commands\MakePlaceholderCommand;
use mindtwo\DocumentGenerator\Security\DocumentPolicy;
use mindtwo\DocumentGenerator\Services\BlockRenderer;
use mindtwo\DocumentGenerator\Services\BlockTemplateResolver;
use mindtwo\DocumentGenerator\Services\DocumentGenerator;
use mindtwo\DocumentGenerator\Services\PlaceholderResolver;

class DocumentGeneratorProvider extends ServiceProvider
{
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

            if (! class_exists('CreateDocumentLayoutsTable')) {
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_document_layouts_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_document_layouts_table.php'),
                ], 'migrations');
            }

            if (! class_exists('CreateDocumentBlocksTable')) {
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_document_blocks_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_document_blocks_table.php'),
                ], 'migrations');
            }

            if (! class_exists('CreateGeneratedDocumentsTable')) {
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_generated_documents_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_generated_documents_table.php'),
                ], 'migrations');
            }

            $this->commands([
                MakePlaceholderCommand::class,
                MakeLayoutMigrationCommand::class,
            ]);
        }

        // register gate for our security policies
        $this->registerGate();

        $this->loadMigrationsFrom($this->getMigrationsPath());

        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
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
            return new PlaceholderResolver(config('documents'));
        });

        $this->app->singleton(BlockTemplateResolver::class, function (Application $app) {
            return new BlockTemplateResolver(config('documents'));
        });

        $this->app->bind(BlockRenderer::class, function (Application $app) {
            $placeholderResolver = $app->make(PlaceholderResolver::class);

            $blockTemplateResolver = $app->make(BlockTemplateResolver::class);

            return new BlockRenderer($blockTemplateResolver, $placeholderResolver);
        });

        $this->app->bind(DocumentGenerator::class, function (Application $app) {
            $disk = $this->getDiskInstance();

            $blockRenderer = $app->make(BlockRenderer::class);

            return new DocumentGenerator($disk, $blockRenderer);
        });
    }

    /**
     * Register our security gate using the security policy.
     *
     * @return void
     */
    private function registerGate()
    {
        $policyClass = config('documents.security.policy');

        if ($policyClass === null || ! is_subclass_of($policyClass, DocumentPolicy::class)) {
            $policyClass = \mindtwo\DocumentGenerator\Security\DefaultDocumentPolicy::class;
        }

        Gate::define('download-document', [$policyClass, 'download']);
        Gate::define('create-document', [$policyClass, 'create']);
        Gate::define('create-tmp-document', [$policyClass, 'createTmp']);
        Gate::define('update-document-layout', [$policyClass, 'editLayout']);
    }

    /**
     * Get our migration paths
     */
    private function getMigrationsPath(): array
    {
        return is_array(config('documents.migrations_path')) ?
            config('documents.migrations_path') : [config('documents.migrations_path')];
    }

    /**
     * Get disk instance for the file system
     */
    private function getDiskInstance(): Filesystem
    {
        $diskName = config('documents.files.disk') ?? 'local';

        return Storage::disk($diskName);
    }
}
