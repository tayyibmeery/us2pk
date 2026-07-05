<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'code',
        'acc_class',
        'nature',
        'ownership',
        'pandlcategory',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function voucherDetails()
    {
        return $this->hasMany(VoucherDetail::class);
    }

    public function getBalanceAttribute()
    {
        $debit = $this->voucherDetails()->sum('debit');
        $credit = $this->voucherDetails()->sum('credit');
        return $debit - $credit;
    }

    public function getDebitBalanceAttribute()
    {
        return $this->nature === 'Debit' ? max(0, $this->balance) : 0;
    }

    public function getCreditBalanceAttribute()
    {
        return $this->nature === 'Credit' ? max(0, -$this->balance) : 0;
    }
}
