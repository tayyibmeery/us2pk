<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consolidation extends Model
{
    protected $fillable = [
        'consol_id',
        'awb_number',
        'warehouse_id',
        'international_courier_id',
        'date_departed',
        'date_reached',
        'receivable_from_courier',
        'courier_charges',
        'ware_house_charges',
        'import_taxes',
        'net_st_payable',
        'customs_duty',
        'sales_tax',
        'income_tax',
        'caa_charges',
        'output_sales_tax',
        'total_weight_kg',
        'total_us2pk_charges',
        'total_cod_collected',
        'roi_percent',
    ];

    protected $casts = [
        'date_departed'   => 'date',
        'date_reached'    => 'date',
        'total_weight_kg' => 'float',
        'receivable_from_courier' => 'float',
        'courier_charges' => 'float',
        'ware_house_charges' => 'float',
        'import_taxes' => 'float',
        'net_st_payable' => 'float',
        'direct_cost' => 'float',
        'gross_income' => 'float',
        'roi_percent' => 'float',
        'customs_duty' => 'float',
        'sales_tax' => 'float',
        'income_tax' => 'float',
        'caa_charges' => 'float',
        'output_sales_tax' => 'float',
        'total_us2pk_charges' => 'float',
        'total_cod_collected' => 'float',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function internationalCourier()
    {
        return $this->belongsTo(InternationalCourier::class);
    }

    public function voucher()
    {
        // ✅ FIX: withTrashed() so we can find (and restore) soft-deleted vouchers
        // when updating a consolidation. Without this, a soft-deleted voucher is
        // invisible and the service falls back to "create new" — causing a
        // duplicate voucher_no unique-constraint violation.
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id')
            ->withTrashed();
    }

    public function recalculateTotals()
    {
        $shipments = $this->shipments;

        $this->total_weight_kg = $shipments->sum('weight_kgs');
        $this->courier_charges = $shipments->sum('delivery_charges');
        $this->output_sales_tax = $shipments->sum('output_tax');

        $this->total_cod_collected = $shipments->sum(function ($s) {
            $total = ($s->item_value_pkr ?? 0) + ($s->company_charges ?? 0);
            $advance = $s->payments()->where('payment_method', '!=', 'Cash on Delivery')->sum('amount') ?? 0;
            return max(0, $total - $advance);
        });

        $this->receivable_from_courier = max(0, $this->total_cod_collected - $this->courier_charges);
        $this->total_us2pk_charges = $shipments->sum('company_charges');

        $this->import_taxes = ($this->customs_duty ?? 0)
            + ($this->sales_tax ?? 0)
            + ($this->income_tax ?? 0)
            + ($this->caa_charges ?? 0);

        $this->net_st_payable = ($this->output_sales_tax ?? 0) - ($this->sales_tax ?? 0);

        $this->saveQuietly();
        $this->refresh();

        $directCost = $this->direct_cost ?? 0;
        $grossIncome = $this->gross_income ?? 0;

        $this->roi_percent = $directCost > 0 ? ($grossIncome / $directCost) * 100 : 0;

        $this->saveQuietly();

        return $this;
    }
}
