<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consolidation extends Model
{
    protected $fillable = [
        'consol_id',
        'awb_number',
        'warehouse_id',
        'date_dispatched',
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
        // Allow these to be set via mass assignment (though we usually set them directly)
        'total_us2pk_charges',
        'direct_costs',
        'roi_percent',
    ];

    protected $casts = [
        'date_dispatched' => 'date',
        'date_departed'   => 'date',
        'date_reached'    => 'date',
        'total_weight_kg' => 'float',
        'receivable_from_courier' => 'float',
        'courier_charges' => 'float',
        'ware_house_charges' => 'float',
        'import_taxes' => 'float',
        'net_st_payable' => 'float',
        'direct_cost' => 'float',      // stored column, read-only
        'direct_costs' => 'float',     // regular column
        'gross_income' => 'float',     // stored column, read-only
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

        $this->total_weight_kg = $shipments->sum('weight_kgs');
        $this->courier_charges = $shipments->sum('delivery_charges');
        $this->receivable_from_courier = $shipments->sum('receivable_cod');
        $this->output_sales_tax = $shipments->sum('output_tax');

        $this->import_taxes = $this->customs_duty + $this->sales_tax + $this->income_tax + $this->caa_charges;
        $this->net_st_payable = $this->output_sales_tax - $this->sales_tax;

        // Set total_us2pk_charges (e.g., sum of courier charges)
        $this->total_us2pk_charges = $this->courier_charges;

        // ✅ Compute direct_costs (regular column) – this is NOT stored, so we must set it
        $this->direct_costs = $this->ware_house_charges + $this->import_taxes + $this->courier_charges + $this->net_st_payable;

        // ROI % uses the stored direct_cost column (computed by DB)
        $this->roi_percent = $this->direct_cost > 0
            ? (($this->receivable_from_courier - $this->direct_cost) / $this->direct_cost) * 100
            : 0;

        $this->saveQuietly();
        return $this;
    }
}
