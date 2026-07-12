<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentPayment;
use App\Models\Debtor;
use App\Models\Invoice;
use App\Models\Voucher;
use App\Models\VoucherDetail;
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
            // Create the payment
            $payment = $shipment->payments()->create($validated);

            // Recalc received_amount using the trait method
            $this->updateShipmentReceivedAmount($shipment);

            // Update debtor balance
            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $debtor->applyPayment($validated['amount']);
            }

            // ✅ Check if this is a COD payment
            // Use receivable_cod from shipment instead of invoice->cod
            $voucherService = new VoucherService();
            $receivableCod = $shipment->receivable_cod ?? 0;

            // Check if this payment is for COD (amount >= receivable_cod)
            // AND the shipment has delivery charges
            if ($receivableCod > 0 && $validated['amount'] >= $receivableCod && $shipment->delivery_charges > 0) {
                // ✅ This is a COD payment - Generate COD voucher with courier charges
                Log::info('Generating COD voucher', [
                    'shipment' => $shipment->shipment_code,
                    'receivable_cod' => $receivableCod,
                    'delivery_charges' => $shipment->delivery_charges,
                    'amount' => $validated['amount']
                ]);

                $voucherService->generateCODVoucher($shipment, $receivableCod, $validated['payment_date']);

                // Check if there's remaining balance
                $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
                $remainingBalance = $totalPayable - $shipment->received_amount;
                if ($remainingBalance <= 0) {
                    // Shipment is fully paid - generate settlement voucher
                    $voucherService->generateSettlementVoucher($shipment, 0, $validated['payment_date']);
                }
            } else {
                // ✅ Regular payment - generate payment voucher
                Log::info('Generating regular payment voucher', [
                    'shipment' => $shipment->shipment_code,
                    'receivable_cod' => $receivableCod,
                    'delivery_charges' => $shipment->delivery_charges,
                    'amount' => $validated['amount']
                ]);
                $this->createPaymentVoucher($shipment, $payment);
            }

            DB::commit();
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
            // Get old amount to adjust debtor
            $oldAmount = $payment->amount;
            $shipment = $payment->shipment;

            $payment->update($validated);

            $this->updateShipmentReceivedAmount($shipment);

            // Update debtor balance with the difference
            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $difference = $validated['amount'] - $oldAmount;
                if ($difference != 0) {
                    $debtor->amount_due += $difference;
                    $debtor->balance = $debtor->calculateBalance();
                    $debtor->save();
                }
            }

            // ✅ Update voucher for this payment
            $this->updatePaymentVoucher($shipment, $payment);

            DB::commit();
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

            // Delete the payment voucher first
            $this->deletePaymentVoucher($payment);

            $payment->delete();

            $this->updateShipmentReceivedAmount($shipment);

            // Update debtor balance (subtract the deleted payment)
            $debtor = Debtor::where('shipment_id', $shipment->id)->first();
            if ($debtor) {
                $debtor->amount_due -= $amount;
                $debtor->balance = $debtor->calculateBalance();
                $debtor->save();
            }

            DB::commit();
            return response()->json(['message' => 'Deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment delete error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Create a voucher for a regular payment
     */
    private function createPaymentVoucher($shipment, $payment)
    {
        $voucherService = new VoucherService();

        // Get account IDs
        $cashAccountId = Account::where('name', 'Cash Account')->value('id');
        $debtorAccountId = Account::where('name', 'Debtors')->value('id');

        if (!$cashAccountId || !$debtorAccountId) {
            Log::warning('Missing required accounts for payment voucher');
            return;
        }

        // Payment voucher: Debit Cash Account, Credit Debtors
        $entries = [
            [
                'account_id' => $cashAccountId,
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
            'payment',
            $payment->id,
            $payment->payment_date,
            "Payment of {$payment->amount} for shipment {$shipment->shipment_code}",
            $entries
        );

        return $voucher;
    }

    /**
     * Update a payment voucher
     */
    private function updatePaymentVoucher($shipment, $payment)
    {
        // Delete existing payment voucher
        $this->deletePaymentVoucher($payment);

        // Create new one with updated amount
        $this->createPaymentVoucher($shipment, $payment);
    }

    /**
     * Delete a payment voucher
     */
    private function deletePaymentVoucher($payment)
    {
        $voucher = Voucher::where('reference_type', 'payment')
            ->where('reference_id', $payment->id)
            ->first();

        if ($voucher) {
            $voucher->details()->delete();
            $voucher->delete();
        }
    }
}
