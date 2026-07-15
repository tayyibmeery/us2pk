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
        'output_tax',
        'status',
        'paid_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'cod_date' => 'date',
        'paid_date' => 'date',
        'amount_due' => 'decimal:2',
        'cod' => 'decimal:2',
        'output_tax' => 'decimal:2',
    ];

    protected $attributes = [
        'cod' => 0,
        'output_tax' => 0,
        'status' => 'pending',
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
        return ($this->amount_due ?? 0) + ($this->output_tax ?? 0);
    }

    /**
     * Get the net amount (amount due - COD)
     */
    public function getNetAmountAttribute(): float
    {
        return ($this->amount_due ?? 0) - ($this->cod ?? 0);
    }

    /**
     * Check if COD is fully paid
     */
    public function getCodPaidAttribute(): bool
    {
        return $this->cod_date !== null && ($this->cod ?? 0) > 0;
    }

    /**
     * Check if invoice is paid
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Get the balance amount
     */
    public function getBalanceAttribute(): float
    {
        if ($this->is_paid) {
            return 0;
        }
        return $this->total_with_tax - ($this->cod ?? 0);
    }
}
