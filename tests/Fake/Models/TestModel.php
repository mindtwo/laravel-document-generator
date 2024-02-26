<?php

namespace Tests\Fake\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 */
class TestModel extends Model
{
    protected $fillable = [
        'id',
        'title',
    ];
}
