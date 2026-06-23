<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'pcode',
        'user_id',
        'consolidation_id',
        'description',
        'weight',
        'weight_unit',
        'weight_kgs',
        'seller_tracker_id',
        'site_name',
        'purchase_date',
        'comments',
        'status',
        'arrival_date',
        'expected_delivery_date',
        'date_delivered',
        'item_value_pkr',
        'company_charges',
        'received_amount',
        'paid_by',
        'payment_method',
        'receivable_cod',
        'local_delivery_by',
        'delivery_charges',
    ];


    protected $casts = [
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'expected_delivery_date' => 'date',
        'date_delivered' => 'date',
        'item_value_pkr' => 'decimal:2',
        'company_charges' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'delivery_charges' => 'decimal:2',
        'receivable_cod' => 'decimal:2',
        'total' => 'decimal:2',
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
            // Auto-generate pcode if not set
            if (!$shipment->pcode) {
                $city = $shipment->user?->city;
                if ($city) {
                    $cityCode = $city->city_code;
                    $last = self::where('pcode', 'LIKE', $cityCode . '-%')
                        ->orderBy('id', 'desc')
                        ->first();
                    $next = $last ? intval(substr($last->pcode, strpos($last->pcode, '-') + 1)) + 1 : 1;
                    $shipment->pcode = $cityCode . '-' . $next;
                } else {
                    $shipment->pcode = 'PK-' . rand(1000, 9999);
                }
            }
            // Calculate receivable_cod (total - received_amount)
            $total = $shipment->item_value_pkr + $shipment->company_charges;
            $shipment->receivable_cod = max(0, $total - ($shipment->received_amount ?? 0));
            // amount_due (if paid by customer, total; else 0)
            $shipment->amount_due = $shipment->paid_by === 'By Customer' ? $total : 0;
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
}
