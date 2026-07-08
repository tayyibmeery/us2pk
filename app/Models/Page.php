<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'status',
        'order',
        'image',
        'icon',
        'meta'
    ];

    protected $casts = [
        'status' => 'boolean',
        'meta' => 'array',
    ];
}
