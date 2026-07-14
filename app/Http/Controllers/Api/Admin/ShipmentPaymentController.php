<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentPayment;
use App\Models\Debtor;
use App\Models\Voucher;
use App\Models\Account;
use Illuminate\Http\Request;
use App\ShipmentPaymentHelper;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentPaymentController extends Controller
{
    use ShipmentPaymentHelper;

    public function index(Shipment $shipment)
    {
        return response()->json($shipment->payments()->latest()->get());
    }

    public function store(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = $shipment->payments()->create($validated);
            $this->updateShipmentReceivedAmount($shipment);
            $shipment->recalculateReceivable();

            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $debtor->applyPayment($validated['amount']);
            }

            // Generate voucher for the payment
            $this->createPaymentVoucher($shipment, $payment);

            DB::commit();

            Log::info('Payment created successfully', [
                'shipment' => $shipment->shipment_code,
                'payment_id' => $payment->id,
                'amount' => $payment->amount
            ]);

            return response()->json($payment, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment store error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to record payment: ' . $e->getMessage()], 500);
        }
    }

    public function show(ShipmentPayment $payment)
    {
        return response()->json($payment);
    }

    public function update(Request $request, ShipmentPayment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $payment->amount;
            $shipment = $payment->shipment;

            // Delete old voucher
            $this->deletePaymentVoucher($payment);

            $payment->update($validated);
            $this->updateShipmentReceivedAmount($shipment);
            $shipment->recalculateReceivable();

            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $difference = $validated['amount'] - $oldAmount;
                if ($difference != 0) {
                    $debtor->amount_due += $difference;
                    $debtor->balance = $debtor->calculateBalance();
                    $debtor->save();
                }
            }

            // Create new voucher with updated amount
            $this->createPaymentVoucher($shipment, $payment);

            DB::commit();

            Log::info('Payment updated successfully', [
                'shipment' => $shipment->shipment_code,
                'payment_id' => $payment->id,
                'old_amount' => $oldAmount,
                'new_amount' => $payment->amount
            ]);

            return response()->json($payment);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update payment: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(ShipmentPayment $payment)
    {
        DB::beginTransaction();
        try {
            $shipment = $payment->shipment;
            $amount = $payment->amount;

            Log::info('Deleting payment', [
                'shipment' => $shipment->shipment_code,
                'payment_id' => $payment->id,
                'amount' => $amount,
                'payment_method' => $payment->payment_method,
                'notes' => $payment->notes
            ]);

            // ✅ DELETE the payment voucher (handles both 'payment' and 'shipment_advance')
            $this->deletePaymentVoucher($payment);

            // Delete the payment
            $payment->delete();

            // Recalc received_amount
            $this->updateShipmentReceivedAmount($shipment);

            // Recalculate receivable_cod and amount_due
            $shipment->recalculateReceivable();

            // Update debtor balance
            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $debtor->amount_due -= $amount;
                $debtor->balance = $debtor->calculateBalance();
                $debtor->save();
            }

            DB::commit();

            Log::info('Payment deleted successfully', [
                'shipment' => $shipment->shipment_code,
                'payment_id' => $payment->id,
                'amount' => $amount
            ]);

            return response()->json(['message' => 'Payment and associated voucher deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment delete error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Create a voucher for a payment
     */
    private function createPaymentVoucher($shipment, $payment)
    {
        $voucherService = new VoucherService();

        // Get account ID - use mapped account from payment method if available
        $accountId = null;
        if ($shipment->payment_method_id) {
            $paymentMethod = \App\Models\PaymentMethod::with('account')->find($shipment->payment_method_id);
            if ($paymentMethod && $paymentMethod->account_id) {
                $accountId = $paymentMethod->account_id;
            }
        }

        // Fallback to Cash Account if no mapping found
        if (!$accountId) {
            $accountId = Account::where('name', 'Cash Account')->value('id');
        }

        $debtorAccountId = Account::where('name', 'Debtors')->value('id');

        if (!$accountId || !$debtorAccountId) {
            Log::warning('Missing required accounts for payment voucher');
            return;
        }

        $entries = [
            [
                'account_id' => $accountId,
                'debit' => $payment->amount,
                'credit' => 0,
                'description' => "Payment received for shipment {$shipment->shipment_code}"
            ],
            [
                'account_id' => $debtorAccountId,
                'debit' => 0,
                'credit' => $payment->amount,
                'description' => "Payment received for shipment {$shipment->shipment_code}"
            ]
        ];

        $voucher = $voucherService->generateVoucher(
            'system',
            'payment',  // ← This is the reference_type for payment vouchers
            $payment->id,
            $payment->payment_date,
            "Payment of {$payment->amount} for shipment {$shipment->shipment_code}",
            $entries
        );

        Log::info('Payment voucher created', [
            'shipment' => $shipment->shipment_code,
            'payment_id' => $payment->id,
            'voucher_no' => $voucher->voucher_no
        ]);

        return $voucher;
    }

    /**
     * Delete a payment voucher
     * This handles both 'payment' and 'shipment_advance' reference types
     */
    private function deletePaymentVoucher($payment)
    {
        // First try to find voucher with reference_type = 'payment'
        $voucher = Voucher::where('reference_type', 'payment')
            ->where('reference_id', $payment->id)
            ->first();

        // If not found, try 'shipment_advance' (for initial payments created during shipment creation)
        if (!$voucher) {
            $voucher = Voucher::where('reference_type', 'shipment_advance')
                ->where('reference_id', $payment->shipment_id)
                ->first();
        }

        // If still not found, try to find any voucher with this payment_id
        if (!$voucher) {
            $voucher = Voucher::where('reference_id', $payment->id)
                ->whereIn('reference_type', ['payment', 'shipment_advance'])
                ->first();
        }

        if ($voucher) {
            $voucher->details()->delete();
            $voucher->delete();

            Log::info('Payment voucher deleted', [
                'payment_id' => $payment->id,
                'voucher_no' => $voucher->voucher_no,
                'reference_type' => $voucher->reference_type
            ]);

            return true;
        }

        Log::warning('No voucher found for payment', [
            'payment_id' => $payment->id,
            'shipment_id' => $payment->shipment_id,
            'amount' => $payment->amount
        ]);

        return false;
    }
}
