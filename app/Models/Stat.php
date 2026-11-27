<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'happy_clients',
        'complete_shipments',
        'customer_reviews',
        'active_services',
        'section_title',
        'section_content',
        'phone',
        'status'
    ];

    protected $casts = [
        'happy_clients' => 'integer',
        'complete_shipments' => 'integer',
        'customer_reviews' => 'integer',
        'active_services' => 'integer',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
