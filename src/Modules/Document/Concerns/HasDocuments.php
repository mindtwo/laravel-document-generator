<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use mindtwo\DocumentGenerator\Modules\Document\Contracts\HasDocuments as ContractsHasDocuments;
use mindtwo\DocumentGenerator\Modules\Document\Models\GeneratedDocument;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasDocuments
{

    public static function bootHasDocument()
    {
        static::deleting(function (ContractsHasDocuments $model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (! $model->forceDeleting) {
                    return;
                }
            }

            $model->generatedDocuments()->delete();
        });
    }

    /**
     * Get the document
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function generatedDocument(): MorphMany
    {
        return $this->morphMany(GeneratedDocument::class, 'documentable');
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
