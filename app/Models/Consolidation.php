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
        'courier',
        'date_departed',
        'date_reached',
        'receiveable_from_bluex',
        'pkship_charges',
        'bluex_charges',
        'ware_house_charges',
        'import_taxes',
        'net_st_payable',
        'customs_duty',
        'sales_tax',
        'income_tax',
        'caa_charges',
        'output_sales_tax',
        'total_us2pk_charges',
        'direct_costs',
        'total_weight_kg',
        'gross_income',
        'roi_percent'
    ];

    protected $casts = [
        'date_dispatched' => 'date',
        'date_departed'   => 'date',
        'date_reached'    => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    // Helper to recalculate totals from shipments
    public function recalculateTotals()
    {
        $shipments = $this->shipments;

        $this->total_weight_kg = $shipments->sum('weight_kgs');
        $this->pkship_charges = $shipments->sum('company_charges');
        $this->bluex_charges = $shipments->sum('blueex_charges');
        $this->receiveable_from_bluex = $shipments->sum('receivable_cod');
        $this->output_sales_tax = $shipments->sum('output_tax'); // if you have this field

        // Assume warehouse_charges and import_taxes are entered manually or calculated elsewhere
        // We'll leave them as is.

        $this->import_taxes = $this->customs_duty + $this->sales_tax + $this->income_tax + $this->caa_charges;
        $this->net_st_payable = $this->output_sales_tax - $this->sales_tax;
        $this->direct_cost = $this->ware_house_charges + $this->import_taxes + $this->bluex_charges + $this->net_st_payable;
        $this->gross_income = $this->pkship_charges - $this->direct_cost;
        $this->roi_percent = $this->direct_cost > 0 ? ($this->gross_income / $this->direct_cost) * 100 : 0;

        $this->save();

        return $this;
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
