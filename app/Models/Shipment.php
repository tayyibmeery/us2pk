<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\VoucherService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shipment extends Model
{
    use SoftDeletes;

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
        'bought_by',
        'receivable_cod',
        'delivery_charges',
        'amount_due',
        'output_tax',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'expected_delivery_date' => 'date',
        'date_delivered' => 'date',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function consolidation()
    {
        return $this->belongsTo(Consolidation::class);
    }

    public function images()
    {
        return $this->hasMany(ShipmentImage::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    // ✅ Alias for frontend (snake_case)
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class, 'shipment_status_id');
    }

    // ✅ Alias for eager loading (fixes your 500 error)
    public function shipment_status()
    {
        return $this->belongsTo(ShipmentStatus::class, 'shipment_status_id');
    }

    public function localCourier()
    {
        return $this->belongsTo(LocalCourier::class, 'local_courier_id');
    }

    // ✅ Alias for frontend (snake_case)
    public function local_courier()
    {
        return $this->belongsTo(LocalCourier::class, 'local_courier_id');
    }

    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    public function voucher()
    {
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id');
    }

    public function debtor()
    {
        return $this->hasOne(Debtor::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_shipments')
            ->withPivot(['amount', 'cod'])
            ->withTimestamps();
    }

    public function isDamaged(): bool
    {
        $statusName = $this->shipmentStatus?->name;
        return $statusName && strcasecmp($statusName, 'Damaged - Returned') === 0;
    }

    protected static function booted()
    {
        static::creating(function ($shipment) {
            $shipment->shipment_code = 'TEMP-' . uniqid();
            $total = $shipment->item_value_pkr + $shipment->company_charges;
            $received = $shipment->received_amount ?? 0;
            $shipment->receivable_cod = max(0, $total - $received);
            $shipment->amount_due = $shipment->bought_by === 'By Customer' ? $total : 0;
        });

        static::created(function ($shipment) {
            $shipment->shipment_code = 'SH-' . $shipment->id;
            $shipment->saveQuietly();
            $shipment->syncDebtor();
            (new VoucherService())->syncShipmentVouchers($shipment);
        });

        static::updating(function ($shipment) {
            if ($shipment->isDirty(['item_value_pkr', 'company_charges', 'received_amount', 'bought_by'])) {
                $total = $shipment->item_value_pkr + $shipment->company_charges;
                $received = $shipment->received_amount ?? 0;
                $shipment->receivable_cod = max(0, $total - $received);
                $shipment->amount_due = $shipment->bought_by === 'By Customer' ? $total : 0;
            }
        });

        static::updated(function ($shipment) {
            $shipment->syncDebtor();
        });
    }

    public function recalculateReceivable(): void
    {
        if ($this->isDamaged()) {
            $this->receivable_cod = 0;
            $this->amount_due = 0;
            $this->saveQuietly();
            Debtor::where('shipment_id', $this->id)->delete();
            return;
        }

        $total = ($this->item_value_pkr ?? 0) + ($this->company_charges ?? 0);
        $advance = $this->payments()->where('payment_method', '!=', 'Cash on Delivery')->sum('amount') ?? 0;
        $codRemitted = $this->payments()->where('payment_method', 'Cash on Delivery')->sum('amount') ?? 0;
        $courierFee = $this->delivery_charges ?? 0;
        $grossCod = max(0, $total - $advance);
        $this->receivable_cod = max(0, $grossCod - $courierFee - $codRemitted);
        $this->amount_due = $this->bought_by === 'By Customer' ? $this->receivable_cod : 0;
        $this->saveQuietly();
        $this->syncDebtor();
    }

    public function syncDebtor(): void
    {
        if ($this->isDamaged()) {
            Debtor::where('shipment_id', $this->id)->delete();
            return;
        }

        $totalPayable = ($this->item_value_pkr ?? 0) + ($this->company_charges ?? 0);
        $advance = $this->payments()->where('payment_method', '!=', 'Cash on Delivery')->sum('amount') ?? 0;
        $courierCharges = $this->delivery_charges ?? 0;
        $receivableCod = $this->receivable_cod ?? 0;
        $grossCod = max(0, $totalPayable - $advance);
        $netReceivable = max(0, $grossCod - $courierCharges);

        if ($this->bought_by === 'By Customer' && $netReceivable > 0) {
            $invoiceNo = 'INV-' . $this->shipment_code;
            $debtor = Debtor::where('shipment_id', $this->id)->first();
            $data = [
                'invoice_no' => $invoiceNo,
                'user_id' => $this->user_id,
                'total_payable' => $totalPayable,
                'cod' => $grossCod,
                'receivable_cod' => $netReceivable,
                'amount_due' => $netReceivable,
                'amount_received' => $advance,
                'courier_deduction' => $courierCharges,
                'net_receivable' => $netReceivable,
                'balance' => $netReceivable,
                'last_payment_date' => now(),
            ];
            if ($debtor) {
                $debtor->update($data);
            } else {
                $data['shipment_id'] = $this->id;
                Debtor::create($data);
            }
        } else {
            Debtor::where('shipment_id', $this->id)->delete();
        }
    }

    public function recalcReceivedAmount(): void
    {
        $this->received_amount = $this->payments()
            ->where('payment_method', '!=', 'Cash on Delivery')
            ->sum('amount') ?? 0;
        $this->saveQuietly();
        $this->recalculateReceivable();
        if ($this->consolidation_id) {
            $this->consolidation->recalculateTotals();
        }
    }
}
