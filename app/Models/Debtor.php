<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Debtor extends Model
{
    protected $fillable = [
        'invoice_no',
        'shipment_id',
        'user_id',
        'amount_due',
        'receivable_cod',
        'balance',
        'amount_received',
        'courier_deduction',
        'net_receivable',
        'last_payment_date',
        'cod',
        'total_payable',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'receivable_cod' => 'decimal:2',
        'balance' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'courier_deduction' => 'decimal:2',
        'net_receivable' => 'decimal:2',
        'cod' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'last_payment_date' => 'date',
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
     * Calculate the correct balance considering courier deductions
     */
    public function calculateBalance(): float
    {
        $grossCod = $this->cod ?? 0;
        $courierDeduction = $this->courier_deduction ?? 0;
        $netReceivable = max(0, $grossCod - $courierDeduction);
        $amountReceived = $this->amount_received ?? 0;
        $balance = max(0, $netReceivable - $amountReceived);

        return $balance;
    }

    /**
     * Apply a payment to this debtor
     */
    public function applyPayment(float $amount): void
    {
        $this->amount_received = ($this->amount_received ?? 0) + $amount;
        $this->last_payment_date = now();
        $this->net_receivable = max(0, ($this->cod ?? 0) - ($this->courier_deduction ?? 0));
        $this->balance = $this->calculateBalance();
        $this->receivable_cod = $this->net_receivable;
        $this->amount_due = $this->balance;
        $this->save();
    }

    /**
     * Get payment status with details
     */
    public function getPaymentStatusAttribute(): array
    {
        return [
            'total_payable' => $this->total_payable ?? 0,
            'advance_paid' => max(0, ($this->amount_received ?? 0) - ($this->cod ?? 0)),
            'cod_amount' => $this->cod ?? 0,
            'courier_deduction' => $this->courier_deduction ?? 0,
            'net_receivable' => $this->net_receivable ?? $this->cod ?? 0,
            'amount_received' => $this->amount_received ?? 0,
            'balance' => $this->balance ?? 0,
            'is_fully_paid' => ($this->balance ?? 0) <= 0,
        ];
    }

    /**
     * Scopes
     */
    public function scopeUnpaid($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeFullyPaid($query)
    {
        return $query->where('balance', '<=', 0);
    }
}
