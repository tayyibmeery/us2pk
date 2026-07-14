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


    public function voucher()
    {
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id')
            ->whereIn('reference_type', ['payment', 'shipment_advance']);
    }

    /**
     * Boot the model
     */
    protected static function booted()
    {
        // When a payment is deleted, also delete its voucher
        static::deleting(function ($payment) {
            // Find and delete the associated voucher
            $voucher = Voucher::where('reference_type', 'payment')
                ->where('reference_id', $payment->id)
                ->first();

            if (!$voucher) {
                // Try to find advance payment voucher for the shipment
                $voucher = Voucher::where('reference_type', 'shipment_advance')
                    ->where('reference_id', $payment->shipment_id)
                    ->first();
            }

            if ($voucher) {
                $voucher->details()->delete();
                $voucher->delete();
                \Illuminate\Support\Facades\Log::info('Payment voucher deleted automatically', [
                    'payment_id' => $payment->id,
                    'voucher_no' => $voucher->voucher_no
                ]);
            }
        });
    }
}
