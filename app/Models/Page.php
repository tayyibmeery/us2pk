<?php
// app/Models/Page.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'status',
        'order',
        'image',
        'icon',
        'meta',
        'parent_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'meta' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }
        return asset('storage/' . $this->image);
    }

    public function isActive()
    {
        return $this->status === true;
    }

    public function getMetaValue($key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }
}
