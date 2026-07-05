<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherDetail extends Model
{
    protected $fillable = [
        'voucher_id',
        'account_id',
        'debit',
        'credit',
        'description'
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
