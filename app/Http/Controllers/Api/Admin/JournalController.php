<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with('details.account')
            ->where('is_deleted', false)
            ->where('approved', true)
            ->orderBy('date', 'desc');

        if ($request->id) $query->where('voucher_no', 'like', "%{$request->id}%");
        if ($request->description) $query->where('description', 'like', "%{$request->description}%");
        if ($request->source) $query->where('source', $request->source);
        if ($request->balanced) {
            if ($request->balanced === 'Balanced') {
                $query->has('details');
            } else {
                $query->whereDoesntHave('details');
            }
        }

        $vouchers = $query->paginate(20);

        $journal = [];
        foreach ($vouchers as $voucher) {
            // Get reference safely
            $shipmentCode = null;
            if ($voucher->reference_type === 'shipment' && $voucher->reference_id) {
                try {
                    $shipment = \App\Models\Shipment::find($voucher->reference_id);
                    if ($shipment) {
                        $shipmentCode = $shipment->shipment_code;
                    }
                } catch (\Exception $e) {
                    // Ignore
                }
            }

            // Calculate totals for this voucher
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($voucher->details as $detail) {
                $totalDebit += (float) $detail->debit;
                $totalCredit += (float) $detail->credit;
            }

            // Each voucher gets one row with its details
            $row = [
                'date' => $voucher->date->format('d/m/Y'),
                'voucher_no' => $voucher->voucher_no,
                'shipment_code' => $shipmentCode,
                'source' => $voucher->source,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balanced' => ($totalDebit == $totalCredit) ? 'Balanced' : 'Not Balanced',
                'description' => $voucher->description,
                'details' => $voucher->details->map(function ($detail) {
                    return [
                        'account_name' => $detail->account->name,
                        'debit' => (float) $detail->debit,
                        'credit' => (float) $detail->credit,
                    ];
                }),
            ];

            $journal[] = $row;
        }

        return response()->json([
            'data' => $journal,
            'pagination' => $vouchers->toArray(),
        ]);
    }
}
