<?php

namespace App\Http\Controllers\Api;

use App\Models\ProhibitedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json($request->user()->load('city'));
    }

    public function shipments(Request $request)
    {
        $shipments = $request->user()->shipments()
            ->with('consolidation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json($shipments);
    }

    public function prohibitedItems()
    {
        return response()->json(ProhibitedItem::all());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }
}
