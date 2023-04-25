<?php

namespace mindtwo\DocumentGenerator\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mindtwo\DocumentGenerator\Builders\GeneratedDocumentBuilder;
use mindtwo\LaravelAutoCreateUuid\AutoCreateUuid;

/**
 * Model to save our document in the database
 *
 * @property int  $id
 * @property string  $uuid
 * @property string  $documentId
 * @property array  $fields
 * @property string  $content
 * @property string  $disk
 * @property string  $fileName
 * @property string  $filePath
 * @property string  $fullpath
 * @property bool  $saved_to_disk
 * @property bool $saved_to_db
 */
class GeneratedDocument extends Model
{
    use AutoCreateUuid;

    protected $fillable = [
        'content',
        'fields',
    ];

    protected $casts = [
        'fields' => 'json',
    ];

    /**
     * Accessor for striping documentId from our document
     *
     * @return Attribute
     */
    public function documentId(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::replace('-', '', $this->uuid)
        );
    }

    public function savedToDisk(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->disk),
        );
    }

    public function savedToDb(): Attribute
    {
        return Attribute::make(
            get: fn () => isset($this->id),
        );
    }

    /**
     * Get disk instance where file is stored.
     *
     * @return Filesystem
     */
    public function diskInstance(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    public function fullpath(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->filePath}/{$this->file_name}"
        );
    }

    public static function query(): GeneratedDocumentBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): GeneratedDocumentBuilder
    {
        return new GeneratedDocumentBuilder($query);
    }
}
