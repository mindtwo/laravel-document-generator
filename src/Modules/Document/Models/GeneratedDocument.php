<?php

namespace mindtwo\DocumentGenerator\Modules\Document\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use mindtwo\DocumentGenerator\Modules\Document\Document;
use mindtwo\DocumentGenerator\Modules\Document\Events\DocumentShouldSaveToDiskEvent;
use mindtwo\DocumentGenerator\Modules\Generation\Factory\FileCreatorFactory;
use mindtwo\LaravelAutoCreateUuid\AutoCreateUuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property int $id
 * @property string $uuid
 * @property string $model_type
 * @property string $model_id
 * @property string $document_class
 * @property ?string $content
 * @property ?string $disk
 * @property ?string $file_name
 * @property ?string $file_path
 * @property ?array $resolved_placeholder
 * @property ?array $extra
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property-read bool $hasContent
 * @property-read bool $isSavedToDisk
 * @property-read ?string $full_path
 * @property-read Document $instance
 */
class GeneratedDocument extends Model
{

    use AutoCreateUuid;

    public static function boot()
    {
        parent::boot();

        static::deleted(function (GeneratedDocument $document) {
            if ($document->is_saved_to_disk) {
                $document->diskInstance()->delete($document->full_path);
            }
        });
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'resolved_placeholder' => 'array',
        'extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function fullPath(): Attribute
    {
        return Attribute::make(function () {
            if (! $this->file_path || ! $this->file_name) {
                return null;
            }

            return $this->file_path.'/'.$this->file_name;
        });
    }

    public function hasContent(): Attribute
    {
        return Attribute::make(function () {
            return ! empty($this->content);
        });
    }

    /**
     * Check if the document is saved to disk.
     *
     * @return Attribute<bool>
     */
    public function isSavedToDisk(): Attribute
    {
        return Attribute::make(function () {
            return ! empty($this->disk) && ! empty($this->full_path);
        });
    }

    /**
     * Get the document instance.
     *
     * @return Attribute<Document>
     */
    public function instance(): Attribute
    {
        return Attribute::make(function () {
            if (! $this->document_class || ! class_exists($this->document_class) || ! $this->model) {
                return null;
            }

            return new $this->document_class($this->model);
        });
    }

    /**
     * Save the document to disk.
     */
    public function saveToDisk(?string $disk = null): void
    {
        if ($this->is_saved_to_disk) {
            return;
        }

        $this->disk = $disk ?? config('documents.files.default_disk');

        DocumentShouldSaveToDiskEvent::dispatch($this);
    }

    /**
     * Download the document.
     */
    public function download(bool $inline = false): StreamedResponse|Response
    {
        if (! $this->has_content) {
            return response()->noContent(404);
        }

        if ($this->is_saved_to_disk) {
            return $this->diskInstance()->download($this->full_path, $this->file_name, [
                'Content-Disposition' => ($inline ? 'inline' : 'attachment')."; filename={$this->file_name}",
            ]);
        }

        $fileCreator = FileCreatorFactory::make($this);
        return $fileCreator->download($this, null, $inline);
    }

    /**
     * Get disk instance where file is stored.
     */
    public function diskInstance(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    /**
     * Get the owning model.
     */
    public function model(): MorphTo
    {
        return $this->morphTo('documentable');
    }
}
