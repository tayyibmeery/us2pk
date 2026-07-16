<?php

namespace App\Http\Controllers\Api;

use App\Models\ProhibitedItem;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return response()->json($request->user()->load('city'));
    }

    /**
     * Update user profile
     */
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
            'avatar'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
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

    /**
     * Update user avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'message' => 'Avatar updated successfully',
            'user'    => $user->fresh('city'),
        ]);
    }

    /**
     * Get user shipments with pagination
     */
    public function shipments(Request $request)
    {
        $shipments = $request->user()
            ->shipments()
            ->with(['consolidation', 'shipmentStatus', 'localCourier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($shipments);
    }

    /**
     * Get single shipment details
     */
    public function shipmentDetails(Request $request, $id)
    {
        $shipment = $request->user()
            ->shipments()
            ->with(['consolidation', 'shipmentStatus', 'localCourier', 'payments'])
            ->findOrFail($id);

        return response()->json($shipment);
    }

    /**
     * Track shipment by tracking number
     */
    public function trackShipment(Request $request, $trackingNumber)
    {
        $shipment = Shipment::where('shipment_code', $trackingNumber)
            ->orWhere('seller_tracker_id', $trackingNumber)
            ->with(['shipmentStatus', 'user', 'consolidation'])
            ->first();

        if (!$shipment) {
            return response()->json([
                'message' => 'Shipment not found'
            ], 404);
        }

        // Check if the shipment belongs to the authenticated user
        if ($shipment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized to track this shipment'
            ], 403);
        }

        // Build tracking timeline
        $timeline = $this->buildTrackingTimeline($shipment);

        return response()->json([
            'shipment' => $shipment,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats(Request $request)
    {
        $user = $request->user();

        $total = $user->shipments()->count();
        $inTransit = $user->shipments()
            ->whereHas('shipmentStatus', function ($q) {
                $q->whereIn('name', ['In Transit', 'Departed Operations Facility - In Transit']);
            })->count();

        $delivered = $user->shipments()
            ->whereHas('shipmentStatus', function ($q) {
                $q->where('name', 'Delivered');
            })->count();

        $pending = $user->shipments()
            ->whereHas('shipmentStatus', function ($q) {
                $q->whereIn('name', ['Pending', 'Bought by Customer', 'Bought by Company']);
            })->count();

        $recentShipments = $user->shipments()
            ->with(['shipmentStatus'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => [
                'total' => $total,
                'inTransit' => $inTransit,
                'delivered' => $delivered,
                'pending' => $pending,
                'change' => '+12%',
            ],
            'recent_shipments' => $recentShipments,
        ]);
    }

    /**
     * Get prohibited items
     */
    public function prohibitedItems()
    {
        return response()->json(ProhibitedItem::all());
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Build tracking timeline for a shipment
     */
    private function buildTrackingTimeline($shipment)
    {
        $timeline = [];

        // Add creation event
        $timeline[] = [
            'title' => 'Shipment Created',
            'description' => 'Your shipment has been created and is being processed.',
            'date' => $shipment->created_at->format('d M Y, H:i'),
            'completed' => true,
        ];

        // Add purchase event
        if ($shipment->purchase_date) {
            $timeline[] = [
                'title' => 'Item Purchased',
                'description' => 'The item has been purchased from the seller.',
                'date' => \Carbon\Carbon::parse($shipment->purchase_date)->format('d M Y'),
                'completed' => true,
            ];
        }

        // Add consolidation event if exists
        if ($shipment->consolidation_id) {
            $timeline[] = [
                'title' => 'Consolidated',
                'description' => 'Your shipment has been consolidated with other shipments.',
                'date' => $shipment->consolidation->created_at->format('d M Y'),
                'completed' => true,
            ];
        }

        // Add status based events
        $status = $shipment->shipmentStatus?->name ?? 'Pending';

        $statusEvents = [
            'Departed Operations Facility - In Transit' => [
                'title' => 'In Transit',
                'description' => 'Your shipment has departed the operations facility.',
                'completed' => true,
            ],
            'Reached Shipment in USA facility' => [
                'title' => 'Reached USA Facility',
                'description' => 'Your shipment has arrived at the USA facility.',
                'completed' => true,
            ],
            'Custom Office at Lahore Airport' => [
                'title' => 'Customs Clearance',
                'description' => 'Your shipment is at customs office in Lahore Airport.',
                'completed' => true,
            ],
            'Reached Lahore Company Office' => [
                'title' => 'Reached Lahore Office',
                'description' => 'Your shipment has reached the Lahore company office.',
                'completed' => true,
            ],
            'Out for Delivery' => [
                'title' => 'Out for Delivery',
                'description' => 'Your shipment is out for delivery to your address.',
                'completed' => true,
            ],
            'Delivered' => [
                'title' => 'Delivered',
                'description' => 'Your shipment has been delivered successfully!',
                'completed' => true,
            ],
        ];

        if (isset($statusEvents[$status])) {
            $timeline[] = $statusEvents[$status];
        }

        // Add expected delivery if exists
        if ($shipment->expected_delivery_date) {
            $isPast = \Carbon\Carbon::parse($shipment->expected_delivery_date)->isPast();
            $timeline[] = [
                'title' => 'Expected Delivery',
                'description' => $isPast ? 'Your shipment should have been delivered by now.' : 'Your shipment is expected to be delivered on this date.',
                'date' => \Carbon\Carbon::parse($shipment->expected_delivery_date)->format('d M Y'),
                'completed' => $isPast && $status === 'Delivered',
            ];
        }

        return $timeline;
    }
}
