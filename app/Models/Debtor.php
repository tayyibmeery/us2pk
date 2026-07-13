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
        // Total amount owed (gross COD)
        $grossCod = $this->cod ?? 0;

        // Courier deduction
        $courierDeduction = $this->courier_deduction ?? 0;

        // Net receivable after courier deduction
        $netReceivable = max(0, $grossCod - $courierDeduction);

        // Total amount received (advance payments)
        $amountReceived = $this->amount_received ?? 0;

        // Balance = Net receivable - Amount received (excluding courier deduction)
        // Because the courier deduction is already factored into net_receivable
        $balance = max(0, $netReceivable - $amountReceived);

        Log::info('Calculating debtor balance', [
            'invoice_no' => $this->invoice_no,
            'gross_cod' => $grossCod,
            'courier_deduction' => $courierDeduction,
            'net_receivable' => $netReceivable,
            'amount_received' => $amountReceived,
            'balance' => $balance
        ]);

        return $balance;
    }

    /**
     * Apply a payment to this debtor
     * Updates the debtor balance correctly
     */
    public function applyPayment(float $amount): void
    {
        // Track total amount received
        $this->amount_received = ($this->amount_received ?? 0) + $amount;

        // Update last payment date
        $this->last_payment_date = now();

        // Update net receivable
        $this->net_receivable = max(0, ($this->cod ?? 0) - ($this->courier_deduction ?? 0));

        // Calculate the balance
        $this->balance = $this->calculateBalance();

        // Update receivable_cod for backward compatibility
        $this->receivable_cod = $this->net_receivable;

        // Update amount_due for backward compatibility
        $this->amount_due = $this->balance;

        $this->save();

        Log::info('Applied payment to debtor', [
            'invoice_no' => $this->invoice_no,
            'amount' => $amount,
            'new_balance' => $this->balance,
            'net_receivable' => $this->net_receivable
        ]);
    }

    /**
     * Calculate the correct receivable_cod (amount owed by customer)
     */
    public function calculateReceivableCod(): float
    {
        return max(0, ($this->total_payable ?? 0) - ($this->amount_received ?? 0));
    }

    /**
     * Apply courier deduction to the debtor
     * This should be called when COD is completed
     */
    public function applyCourierDeduction(float $courierCharges): void
    {
        $this->courier_deduction = $courierCharges;
        $this->net_receivable = $this->cod - $courierCharges;
        $this->balance = $this->calculateBalance();
        $this->save();

        \Log::info('Applied courier deduction to debtor', [
            'invoice_no' => $this->invoice_no,
            'courier_charges' => $courierCharges,
            'new_balance' => $this->balance
        ]);
    }

    /**
     * Initialize debtor from shipment data
     */
    public static function createFromShipment(Shipment $shipment): ?self
    {
        $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
        $received = $shipment->received_amount ?? 0;
        $cod = max(0, $totalPayable - $received);

        // Only create debtor if bought_by is 'By Customer' and cod > 0
        if ($shipment->bought_by !== 'By Customer' || $cod <= 0) {
            return null;
        }

        $invoiceNo = 'INV-' . $shipment->shipment_code;

        return self::create([
            'invoice_no' => $invoiceNo,
            'shipment_id' => $shipment->id,
            'user_id' => $shipment->user_id,
            'total_payable' => $totalPayable,
            'cod' => $cod,
            'receivable_cod' => $cod,
            'amount_received' => $received,
            'amount_due' => $cod,
            'balance' => $cod,
            'courier_deduction' => 0,
            'net_receivable' => $cod,
            'last_payment_date' => null,
        ]);
    }

    /**
     * Update debtor from shipment data
     */
    public function updateFromShipment(Shipment $shipment): void
    {
        $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
        $received = $shipment->received_amount ?? 0;
        $cod = max(0, $totalPayable - $received);

        // If bought_by is not 'By Customer' or cod is 0, delete the debtor
        if ($shipment->bought_by !== 'By Customer' || $cod <= 0) {
            $this->delete();
            return;
        }

        // Update debtor fields
        $this->total_payable = $totalPayable;
        $this->cod = $cod;
        $this->receivable_cod = $cod;
        $this->amount_received = $received;
        $this->amount_due = $cod;
        $this->balance = $this->calculateBalance();
        $this->net_receivable = $this->cod - ($this->courier_deduction ?? 0);
        $this->save();
    }

    /**
     * Check if the debtor is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->balance <= 0;
    }

    /**
     * Get the total amount actually received (net of courier charges)
     */
    public function getNetReceivedAttribute(): float
    {
        return max(0, ($this->amount_received ?? 0) - ($this->courier_deduction ?? 0));
    }

    /**
     * Get the remaining amount to be received
     */
    public function getRemainingAttribute(): float
    {
        return $this->balance;
    }

    /**
     * Check if COD has been completed
     */
    public function isCODCompleted(): bool
    {
        return $this->courier_deduction > 0 && $this->amount_received >= $this->total_payable;
    }

    /**
     * Get payment status with details
     */
    public function getPaymentStatusAttribute(): array
    {
        return [
            'total_payable' => $this->total_payable,
            'advance_paid' => max(0, ($this->amount_received ?? 0) - $this->cod),
            'cod_amount' => $this->cod,
            'courier_deduction' => $this->courier_deduction ?? 0,
            'net_receivable' => $this->net_receivable ?? $this->cod,
            'amount_received' => $this->amount_received ?? 0,
            'net_received' => $this->getNetReceivedAttribute(),
            'balance' => $this->balance,
            'is_fully_paid' => $this->isFullyPaid(),
            'is_cod_completed' => $this->isCODCompleted(),
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

    public function scopeCodPending($query)
    {
        return $query->where('cod', '>', 0)
            ->where('courier_deduction', 0);
    }

    public function scopeCodCompleted($query)
    {
        return $query->where('courier_deduction', '>', 0);
    }
}
