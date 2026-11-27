<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhyUsSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'features',
        'status'
    ];

    protected $casts = [
        'features' => 'array',
        'status' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        if (filter_var($this->image, FILTER_VALIDATE_URL)) return $this->image;
        return asset('storage/' . $this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
