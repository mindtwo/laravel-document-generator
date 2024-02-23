<?php

namespace mindtwo\DocumentGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakePlaceholderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:placeholder {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a placholder for documents';

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

        $config = config('documents');

        $placeholderPath = 'Documents/Placeholders';
        if (isset($config['placeholder']) && isset($config['placeholder']['auto_discover'])) {
            $placeholderPath = $config['placeholder']['auto_discover'][0];
        }

        // repalce value for {Path} in stub
        $namespace = Str::of($placeholderPath)->replace('/', '\\')->toString();

        // replace for {Name} in stub
        $placeholderName = Str::of($name)->title()->append('Placeholder')->toString();

        file_put_contents(
            $this->getFilePath($placeholderPath, $placeholderName),
            Str::of($this->getStub())->replace('{Path}', $namespace)->replace('{Name}', $placeholderName)->toString()
        );
    }

    protected function getFilePath($path, $name)
    {
        return app_path()."/$path/$name.php";
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__.'/../../stubs/Placeholder.php.stub');
    }
}
