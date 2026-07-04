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
        'direct_costs',
        'roi_percent',
        // 'gross_income' is NOT in fillable because it's generated
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
        'direct_cost' => 'float',      // stored column (read‑only)
        'direct_costs' => 'float',
        'gross_income' => 'float',     // stored column (read‑only)
        'roi_percent' => 'float',
        'customs_duty' => 'float',
        'sales_tax' => 'float',
        'income_tax' => 'float',
        'caa_charges' => 'float',
        'output_sales_tax' => 'float',
        'total_us2pk_charges' => 'float',
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

    /**
     * Recalculate all totals from associated shipments.
     */
    public function recalculateTotals()
    {
        $shipments = $this->shipments;

        // Sum child shipment fields
        $this->total_weight_kg = $shipments->sum('weight_kgs');
        $this->courier_charges = $shipments->sum('delivery_charges');
        $this->output_sales_tax = $shipments->sum('output_tax');

        // ✅ Receivable from Courier = Total COD - Local Delivery Charges (net amount)
        $this->receivable_from_courier = $shipments->sum('receivable_cod') - $shipments->sum('delivery_charges');

        // Total US2PK Charges = sum of company_charges (revenue)
        $this->total_us2pk_charges = $shipments->sum('company_charges');

        // Import taxes from manual inputs
        $this->import_taxes = $this->customs_duty + $this->sales_tax + $this->income_tax + $this->caa_charges;

        // Net ST Payable = output - input (sales tax liability)
        $this->net_st_payable = $this->output_sales_tax - $this->sales_tax;

        // Direct Costs = warehouse + import taxes + local courier charges
        $this->direct_costs = $this->ware_house_charges
            + $this->import_taxes
            + $this->courier_charges;

        // ROI = (Gross Income / Direct Costs) * 100
        $this->roi_percent = $this->direct_costs > 0
            ? (($this->total_us2pk_charges - $this->direct_costs) / $this->direct_costs) * 100
            : 0;

        $this->saveQuietly();
        return $this;
    }
}
