<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Display extends Model
{
    protected $fillable = [
        'name',
        'location',
        'device_token',
        'status',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}