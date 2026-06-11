<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $fillable = [
        'invoice_no',
        'date',
        'user_id',
        'pcode',
        'revenue',
        'output_tax',
        'paid_by',
        'vendor_payment'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
