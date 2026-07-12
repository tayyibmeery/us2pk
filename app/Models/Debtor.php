<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debtor extends Model
{
    protected $fillable = [
        'invoice_no',
        'shipment_id',
        'user_id',
        'amount_due',
        'receivable_cod',
        'balance'
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'receivable_cod' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Apply a payment to this debtor
     */
    public function applyPayment(float $amount): void
    {
        $this->amount_due = max(0, $this->amount_due - $amount);
        $this->balance = $this->calculateBalance();
        $this->save();
    }

    /**
     * Calculate the balance (receivable_cod - amount_due)
     */
    public function calculateBalance(): float
    {
        return max(0, $this->receivable_cod - $this->amount_due);
    }

    /**
     * Check if the debtor is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->balance <= 0;
    }

    /**
     * Get the total amount paid
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->receivable_cod - $this->balance;
    }
}
