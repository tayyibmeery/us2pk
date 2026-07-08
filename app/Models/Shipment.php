<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_code',
        'user_id',
        'consolidation_id',
        'description',
        'weight',
        'weight_unit',
        'weight_kgs',
        'seller_tracker_id',
        'purchase_date',
        'comments',
        'shipment_status_id',
        'payment_method_id',
        'local_courier_id',
        'site_id',
        'arrival_date',
        'expected_delivery_date',
        'date_delivered',
        'item_value_pkr',
        'company_charges',
        'received_amount',
        'paid_by',
        'receivable_cod',
        'delivery_charges',
        'amount_due',              // ✅ added – was missing
        'output_tax',   // add this
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'expected_delivery_date' => 'date',
        'date_delivered' => 'date',
        // Use float for all numeric fields to get actual numbers
        'item_value_pkr' => 'float',
        'company_charges' => 'float',
        'received_amount' => 'float',
        'delivery_charges' => 'float',
        'receivable_cod' => 'float',
        'total' => 'float',
        'weight' => 'float',
        'weight_kgs' => 'float',
        'amount_due' => 'float',
        'output_tax' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consolidation()
    {
        return $this->belongsTo(Consolidation::class);
    }

    public function images()
    {
        return $this->hasMany(ShipmentImage::class);
    }

    protected static function booted()
    {
        static::creating(function ($shipment) {
            $shipment->shipment_code = 'TEMP-' . uniqid();

            $total = $shipment->item_value_pkr + $shipment->company_charges;
            $shipment->receivable_cod = max(0, $total - ($shipment->received_amount ?? 0));
            $shipment->amount_due = $shipment->paid_by === 'By Customer' ? $total : 0;
        });

        static::created(function ($shipment) {
            $userPcode = $shipment->user?->pcode ?? 'USR';
            $shipment->shipment_code = 'SH-' . $userPcode . '-' . $shipment->id;
            $shipment->saveQuietly();
        });

        static::updating(function ($shipment) {
            if ($shipment->isDirty(['item_value_pkr', 'company_charges', 'received_amount', 'paid_by'])) {
                $total = $shipment->item_value_pkr + $shipment->company_charges;
                $shipment->receivable_cod = max(0, $total - ($shipment->received_amount ?? 0));
                $shipment->amount_due = $shipment->paid_by === 'By Customer' ? $total : 0;
            }
        });
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function deliveryService()
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class);
    }

    public function localCourier()
    {
        return $this->belongsTo(LocalCourier::class);
    }


    // Add relationship
    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    // Method to recalc received_amount from all payments
    public function recalcReceivedAmount(): void
    {
        $this->received_amount = $this->payments()->sum('amount') ?? 0;
        // receivable_cod will auto-calc via booted() if we save
        $this->saveQuietly(); // avoid event loops
    }

    // Override save to update receivable_cod and amount_due after payment changes
    // We'll handle via controller logic, not event to avoid complexity.

    public function voucher()
    {
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id');
    }
}
