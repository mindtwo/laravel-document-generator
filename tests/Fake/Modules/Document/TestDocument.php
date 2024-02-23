<?php

namespace Tests\Fake\Modules\Document;

use Illuminate\Database\Eloquent\Model;

class TestDocument extends \mindtwo\DocumentGenerator\Modules\Document\Document
{

    protected bool $useDefaults = false;

    protected ?string $filePathGenerator = \Tests\Fake\Modules\Document\TestFilePathGenerator::class;

    protected ?string $fileNameGenerator = \Tests\Fake\Modules\Document\TestFileNameGenerator::class;


    /**
     * Get the file path generator class.
     *
     * @return class-string<FilePathGenerator>|null
     */
    public function filePathGenerator(): ?string
    {
        if ($this->useDefaults) {
            return null;
        }

        return $this->filePathGenerator;
    }

    /**
     * Get the file path generator class.
     *
     * @return class-string<FilePathGenerator>|null
     */
    public function fileNameGenerator(): ?string
    {
        if ($this->useDefaults) {
            return null;
        }

        return $this->fileNameGenerator;
    }

    public static function makeWithDefaults(Model $model): static
    {
        $document = new static($model);

        $document->useDefaults = true;

        return $document;
    }

    public function blocks(): array
    {
        return [];
    }
}
