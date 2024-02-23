<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

interface SavedBy
{
    /**
     * Get the class-string of the file saver used to save the document to disk.
     *
     * @return class-string<FilePathGenerator>|null
     */
    public function savedBy(): string;
}
