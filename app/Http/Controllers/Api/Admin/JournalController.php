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
                // Not balanced: exclude balanced ones
                $query->whereDoesntHave('details');
            }
        }

        $vouchers = $query->paginate(20);

        $journal = [];
        foreach ($vouchers as $voucher) {
            foreach ($voucher->details as $detail) {
                $journal[] = [
                    'date' => $voucher->date->format('d/m/Y'),
                    'voucher_no' => $voucher->voucher_no,
                    'shipment_code' => $voucher->reference_type === 'shipment' ? optional($voucher->reference)->shipment_code : null,
                    'source' => $voucher->source,
                    'account_name' => $detail->account->name,
                    'debit' => $detail->debit,
                    'credit' => $detail->credit,
                    'balanced' => $voucher->isBalanced() ? 'Balanced' : 'Not Balanced',
                    'description' => $voucher->description,
                ];
            }
        }

        return response()->json([
            'data' => $journal,
            'pagination' => $vouchers->toArray(),
        ]);
    }
}
