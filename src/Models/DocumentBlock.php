<?php

namespace mindtwo\DocumentGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model to access/define our document layout in database
 *
 * @param  int  $id
 * @param  int  $position
 * @param  bool  $show
 * @param  string  $template
 * @param  string  $block_type
 * @param  DocumentLayout  $layout
 */
class DocumentBlock extends Model
{
    public $guarded = [
        'id',
        'document_layout_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'show' => 'boolean',
    ];

    public function layout(): BelongsTo
    {
        return $this->belongsTo(DocumentLayout::class);
    }
}
