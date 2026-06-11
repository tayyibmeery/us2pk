<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'psi',
        'user_id',
        'consolidation_id',
        'description',
        'weight',
        'weight_unit',
        'weight_kgs',
        'seller_tracker_id',
        'purchase_date',
        'comments',
        'status',
        'arrival_date',
        'expected_delivery_date',
        'date_delivered',
        'item_value_pkr',
        'company_charges',
        'paid_by',
        'payment_method',
        'receivable_cod',
        'local_delivery_by',
        'blueex_charges'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'expected_delivery_date' => 'date',
        'date_delivered' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consolidation()
    {
        return $this->belongsTo(Consolidation::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    protected static function booted()
    {
        static::creating(function ($shipment) {
            // Auto-generate PSI if not set
            if (!$shipment->psi) {
                $last = self::orderBy('id', 'desc')->first();
                $nextId = $last ? intval(substr($last->psi, 4)) + 1 : 1;
                $shipment->psi = 'PSI-' . $nextId;
            }
            // Auto-calculate total and amount due
            $shipment->total = $shipment->item_value_pkr + $shipment->company_charges;
            $shipment->amount_due = $shipment->paid_by === 'By Customer' ? $shipment->total : 0;
        });
    }
}
