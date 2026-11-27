<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('city')->where('role', 'user');
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('pcode', 'like', "%{$request->search}%");
            });
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,approved'
        ]);
        $user->status = $request->status;
        if ($request->status === 'approved') {
            $user->email_verified_at = now();
        } else {
            $user->email_verified_at = null;
        }
        $user->save();
        return response()->json([
            'message' => 'Status updated successfully.'
        ]);
    }

    public function show(User $user)
    {
        return response()->json($user->load('city'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'address'  => 'required|string',
            'city_id'  => 'required|exists:cities,id',
            'pcode'    => 'nullable|string|max:50|unique:users,pcode',
            'source'   => 'nullable|string|max:100',
            'status'   => ['required', Rule::in(['pending', 'verified', 'approved'])],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // Role is always forced to 'user' for admin-created accounts
        $validated['role'] = 'user';

        $user = User::create($validated);

        return response()->json($user->load('city'), 201);
    }

    /**
     * Update an existing user (admin only)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'city_id'  => 'nullable|exists:cities,id',
            'pcode'    => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'source'   => 'nullable|string|max:100',
            'status'   => ['sometimes', Rule::in(['pending', 'verified', 'approved'])],
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Role is never changed via this endpoint
        unset($validated['role']);

        $user->update($validated);

        return response()->json($user->load('city'));
    }
}
