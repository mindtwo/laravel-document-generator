<?php

namespace mindtwo\DocumentGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeLayoutMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:layout {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a layout migration';

    public function __construct(
        protected Filesystem $filesystem
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $model = $this->option('model');

        $config = config('documents');
        $migrationPath = $config['migrations_path'] ?? database_path('documents');

        // replace for {Name} in stub
        $migrationName = Str::of($name)->lower()->prepend('create_')->append('_layout')->toString();

        file_put_contents(
            $this->getFilePath($migrationPath, $migrationName),
            Str::of($this->getStub())->replace('{Model}', $model)->replace('{Name}', $name)->toString()
        );

        $this->createViewVendorPath();

        file_put_contents(
            resource_path("views/vendor/documents/components/layouts/$name.blade.php"),
            '',
        );
    }

    protected function getFilePath($path, $name)
    {
        return "$path/$name.php";
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__.'/../../stubs/LayoutMigration.php.stub');
    }

    protected function createViewVendorPath()
    {
        if (! is_dir(resource_path('views/vendor/documents'))) {
            mkdir(
                resource_path('views/vendor/documents')
            );
        }

        if (! is_dir(resource_path('views/vendor/documents/components'))) {
            mkdir(
                resource_path('views/vendor/documents/components')
            );
        }

        if (! is_dir(resource_path('views/vendor/documents/components/layouts'))) {
            mkdir(
                resource_path('views/vendor/documents/components/layouts')
            );
        }
    }
}
