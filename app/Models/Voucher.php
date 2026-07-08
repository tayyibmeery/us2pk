<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'voucher_no',
        'date',
        'description',
        'is_active',
        'approved',
        'is_deleted',
        'source',
        'reference_type',
        'reference_id',
        'created_by',
        'approved_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'approved' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(VoucherDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getTotalDebitAttribute()
    {
        return $this->details->sum('debit');
    }

    public function getTotalCreditAttribute()
    {
        return $this->details->sum('credit');
    }

    public function isBalanced()
    {
        return $this->total_debit == $this->total_credit;
    }

    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }
}
