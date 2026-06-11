<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $fillable = [
        'invoice_no',
        'user_id',
        'amount_due',
        'cod',
        'cod_date',
        'balance'
    ];

    protected $casts = [
        'cod_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
