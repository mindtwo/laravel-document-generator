<?php

namespace mindtwo\DocumentGenerator\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use mindtwo\DocumentGenerator\Builders\DocumentLayoutBuilder;
use mindtwo\DocumentGenerator\Enums\DocumentOrientation;
use mindtwo\DocumentGenerator\Enums\DocumentWidth;
use mindtwo\LaravelAutoCreateUuid\AutoCreateUuid;

/**
 * Model to access/define our document layout in database
 *
 * @param  int  $id
 * @param  string  $uuid
 * @param  string  $model_type
 * @param  int  $model_id
 * @param  bool  $show_border
 * @param  DocumentOrientation  $orientation
 * @param  DocumentWidth  $content_width
 * @param  Collection  $blocks
 */
class DocumentLayout extends Model
{
    use AutoCreateUuid;

    public $guarded = [
        'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'orientation' => DocumentOrientation::class,
        'content_width' => DocumentWidth::class,
        'placeholder' => 'array',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(DocumentBlock::class)->orderBy('position');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public static function query(): DocumentLayoutBuilder|Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): DocumentLayoutBuilder
    {
        return new DocumentLayoutBuilder($query);
    }
}
