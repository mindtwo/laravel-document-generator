<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\HasDocument as ContractsHasDocument;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasDocument
{

    public static function bootHasDocument()
    {
        static::deleting(function (ContractsHasDocument $model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (! $model->forceDeleting) {
                    return;
                }
            }

            $model->generatedDocument()->delete();
        });
    }

    /**
     * Get the document
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function generatedDocument(): MorphOne
    {
        return $this->morphOne(GeneratedDocument::class, 'documentable');
    }

    /**
     * Get the document class.
     *
     * @return class-string<\mindtwo\DocumentGenerator\Modules\Document\Document>
     */
    public function getDocumentClass(): string
    {
        if (! isset($this->documentClass)) {
            throw new \Exception('Error generating document. Please add a property or mutator named "documentClass" with value class-string of default document', 1);

        }

        return $this->documentClass;
    }
}
