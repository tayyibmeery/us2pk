<?php

namespace App\Http\Controllers\Api;

use App\Models\ProhibitedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
class UserDashboardController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json($request->user()->load('city'));
    }
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'phone'       => 'required|string|max:20',
            'bio'         => 'nullable|string|max:500',
            'address'     => 'required|string',
            'city_id'     => 'required|exists:cities,id',
            'country'     => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_id'      => 'nullable|string|max:50',
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // new
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh('city'),
        ]);
    }



    public function updateAvatar(Request $request)
    {


        \Log::info('Avatar upload debug', [
            'hasFile' => $request->hasFile('avatar'),
            'file'    => $request->file('avatar'),
            'size'    => $request->file('avatar') ? $request->file('avatar')->getSize() : null,
            'all'     => $request->all(),
            'files'   => $request->files->all(),
        ]);
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Avatar updated',
            'user'    => $user->fresh('city'),
        ]);
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
