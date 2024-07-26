<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use mindtwo\DocumentGenerator\Modules\Document\Document;

interface DocumentHolder
{
    public function getDocumentable(): Model;

    /**
     * Get the document instance
     *
     * @return ?Document
     */
    public function documentInstance(): ?Document;

    /**
     * Get the content of the document
     */
    public function getContent(): string;

    /**
     * Get the file name of the document
     *
     * @return ?string
     */
    public function getFileName(): ?string;

    /**
     * Get the disk instance
     *
     * @return ?Filesystem
     */
    public function diskInstance(): ?Filesystem;
}
