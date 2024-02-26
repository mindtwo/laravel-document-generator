<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

use mindtwo\DocumentGenerator\Modules\Generation\Contracts\FileCreator;

interface SavedBy
{
    /**
     * Get the class-string of the file saver used to save the document to disk.
     *
     * @return class-string<FileCreator>|null
     */
    public function savedBy(): string;
}
