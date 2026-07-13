<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Log;

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

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function shipmentStatus()
    {
        return $this->belongsTo(ShipmentStatus::class);
    }

    public function localCourier()
    {
        return $this->belongsTo(LocalCourier::class);
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

            // Auto-create debtor for customer shipments
            $shipment->syncDebtor();

            // Auto-create vouchers for shipment
            $voucherService = new VoucherService();
            $vouchers = $voucherService->syncShipmentVouchers($shipment);

            Log::info('Shipment created - Vouchers generated', [
                'shipment_id' => $shipment->id,
                'voucher_count' => count($vouchers),
                'received_amount' => $shipment->received_amount
            ]);
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
            // Update debtor when shipment is updated
            $shipment->syncDebtor();

            // Update vouchers when shipment is updated
            $voucherService = new VoucherService();
            $voucherService->syncShipmentVouchers($shipment);
        });
    }

    /**
     * Sync the debtor record for this shipment
     */
    public function syncDebtor(): void
    {
        $totalPayable = ($this->item_value_pkr ?? 0) + ($this->company_charges ?? 0);
        $received = $this->received_amount ?? 0;
        $courierCharges = $this->delivery_charges ?? 0;

        // Calculate gross COD (amount customer owes)
        $grossCod = max(0, $totalPayable - $received);

        // Calculate net receivable (after courier deduction)
        // This is what the company actually gets from the courier
        $netReceivable = max(0, $grossCod - $courierCharges);

        // Only create debtor if bought_by is 'By Customer' and grossCod > 0
        if ($this->bought_by === 'By Customer' && $grossCod > 0) {
            $invoiceNo = 'INV-' . $this->shipment_code;

            // Check if debtor already exists
            $debtor = Debtor::where('shipment_id', $this->id)->first();

            if ($debtor) {
                // Update existing debtor with NET amount
                $debtor->update([
                    'invoice_no' => $invoiceNo,
                    'user_id' => $this->user_id,
                    'total_payable' => $totalPayable,
                    'cod' => $grossCod,
                    'receivable_cod' => $netReceivable, // ← NET amount after courier
                    'amount_due' => $netReceivable,
                    'amount_received' => $received,
                    'courier_deduction' => $courierCharges,
                    'net_receivable' => $netReceivable,
                    'balance' => $netReceivable, // ← Should show 4800 not 5000
                    'last_payment_date' => now(),
                ]);
            } else {
                // Create new debtor with NET amount
                Debtor::create([
                    'invoice_no' => $invoiceNo,
                    'shipment_id' => $this->id,
                    'user_id' => $this->user_id,
                    'total_payable' => $totalPayable,
                    'cod' => $grossCod,
                    'receivable_cod' => $netReceivable, // ← NET amount after courier
                    'amount_due' => $netReceivable,
                    'amount_received' => $received,
                    'courier_deduction' => $courierCharges,
                    'net_receivable' => $netReceivable,
                    'balance' => $netReceivable, // ← Should show 4800 not 5000
                    'last_payment_date' => now(),
                ]);
            }

            Log::info('Debtor synced', [
                'shipment' => $this->shipment_code,
                'gross_cod' => $grossCod,
                'courier_charges' => $courierCharges,
                'net_receivable' => $netReceivable,
                'balance' => $netReceivable
            ]);
        } else {
            // If shipment is fully paid or paid by company, delete the debtor
            Debtor::where('shipment_id', $this->id)->delete();
        }
    }

    public function recalcReceivedAmount(): void
    {
        $this->received_amount = $this->payments()->sum('amount') ?? 0;
        $this->saveQuietly();
        if ($this->consolidation_id) {
            $this->consolidation->recalculateTotals();
        }
        $this->syncDebtor();
    }
}
