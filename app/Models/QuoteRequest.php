<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'service',
        'note',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
