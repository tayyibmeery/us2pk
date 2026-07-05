<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionTypeAccount extends Model
{
    protected $fillable = [
        'transaction_type',
        'debit_account_id',
        'credit_account_id'
    ];

    public function debitAccount()
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }
}
