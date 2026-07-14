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
        return $this->morphOne(Voucher::class, 'reference', 'reference_type', 'reference_id');
    }

    /**
     * Recalculate all totals from associated shipments.
     * ✅ Direct Cost = Warehouse Charges + Import Taxes
     * ✅ Import Taxes = Customs Duty + Sales Tax + Income Tax + CAA Charges
     */
    public function recalculateTotals()
    {
        $shipments = $this->shipments;

        // Sum child shipment fields
        $this->total_weight_kg = $shipments->sum('weight_kgs');
        $this->courier_charges = $shipments->sum('delivery_charges');
        $this->output_sales_tax = $shipments->sum('output_tax');

        // Total COD Collected = sum of receivable_cod from shipments
        $this->total_cod_collected = $shipments->sum('receivable_cod');

        // Receivable from Courier = Total COD - Local Delivery Charges
        $totalCod = $shipments->sum('receivable_cod');
        $totalDeliveryCharges = $shipments->sum('delivery_charges');
        $this->receivable_from_courier = max(0, $totalCod - $totalDeliveryCharges);

        // Total US2PK Charges = sum of company_charges (revenue)
        $this->total_us2pk_charges = $shipments->sum('company_charges');

        // ✅ Calculate Import Taxes = Customs Duty + Sales Tax + Income Tax + CAA Charges
        $this->import_taxes = ($this->customs_duty ?? 0)
            + ($this->sales_tax ?? 0)
            + ($this->income_tax ?? 0)
            + ($this->caa_charges ?? 0);

        // Net Sales Tax Payable = Output Tax - Input Tax (Sales Tax)
        $this->net_st_payable = ($this->output_sales_tax ?? 0) - ($this->sales_tax ?? 0);

        // ✅ The direct_cost and gross_income are STORED columns
        // They will be automatically updated by the database:
        // direct_cost = ware_house_charges + import_taxes
        // gross_income = total_us2pk_charges - direct_cost

        // Save to trigger stored column updates
        $this->saveQuietly();

        // ✅ Refresh to get updated stored columns
        $this->refresh();

        // ✅ Calculate ROI manually
        $directCost = $this->direct_cost ?? 0;
        $grossIncome = $this->gross_income ?? 0;

        $this->roi_percent = $directCost > 0
            ? ($grossIncome / $directCost) * 100
            : 0;

        // Save ROI
        $this->saveQuietly();

        // Log for debugging
        \Log::info('Consolidation recalculated', [
            'consolidation' => $this->consol_id,
            'ware_house_charges' => $this->ware_house_charges,
            'customs_duty' => $this->customs_duty,
            'sales_tax' => $this->sales_tax,
            'income_tax' => $this->income_tax,
            'caa_charges' => $this->caa_charges,
            'import_taxes' => $this->import_taxes,
            'direct_cost' => $this->direct_cost,
            'total_us2pk_charges' => $this->total_us2pk_charges,
            'gross_income' => $this->gross_income,
            'roi_percent' => $this->roi_percent,
        ]);

        return $this;
    }
}
