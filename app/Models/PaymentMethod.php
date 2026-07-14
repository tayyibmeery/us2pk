<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
