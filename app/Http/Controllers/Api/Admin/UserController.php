<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('city')->where('role', 'user');
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('pcode', 'like', "%{$request->search}%");
            });
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate(['status' => 'required|in:pending,verified,approved']);
        $user->status = $request->status;
        $user->save();
        return response()->json(['message' => 'Status updated']);
    }

    public function show(User $user)
    {
        return response()->json($user->load('city'));
    }
}
