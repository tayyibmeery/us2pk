<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentPayment extends Model
{
    use HasFactory;
    use SoftDeletes; // ✅ ADD

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


    public function voucher()
    {
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id')
            ->whereIn('reference_type', ['payment', 'shipment_advance']);
    }

    /**
     * Boot the model
     */

}
