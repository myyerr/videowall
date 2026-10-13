<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentDetail extends Model
{
    protected $fillable = [
        'content_id',
        'content_text',
        'color',
        'positions',
        'sort_order',
    ];

    protected $casts = [
        'positions' => 'array',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}