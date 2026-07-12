<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'shipment_id',
        'date',
        'amount_due',
        'cod',
        'cod_date',
        'output_tax'
    ];

    protected $casts = [
        'date' => 'date',
        'cod_date' => 'date',
        'amount_due' => 'decimal:2',
        'cod' => 'decimal:2',
        'output_tax' => 'decimal:2',
    ];

    protected $attributes = [
        'cod' => 0,
        'output_tax' => 0,
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the total amount including tax
     */
    public function getTotalWithTaxAttribute(): float
    {
        return $this->amount_due + $this->output_tax;
    }

    /**
     * Get the net amount (amount due - COD)
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount_due - $this->cod;
    }

    /**
     * Check if COD is fully paid
     */
    public function getCodPaidAttribute(): bool
    {
        return $this->cod_date !== null && $this->cod > 0;
    }
}
