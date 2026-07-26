<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'address',
        'phone',
        'email',
        'whatsapp_number',
        'social_icons',
        'twitter',
        'facebook',
        'youtube',
        'linkedin',
        'copyright',
        'newsletter_text',
        'service_links',
        'quick_links',
        'company_links',
        'status'
    ];

    protected $casts = [
        'social_icons' => 'array',
        'service_links' => 'array',
        'quick_links' => 'array',
        'company_links' => 'array',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
