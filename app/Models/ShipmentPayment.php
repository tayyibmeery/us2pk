<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
