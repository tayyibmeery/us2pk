<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of team members.
     */
    public function index(Request $request)
    {
        $query = TeamMember::query();

        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                ->orWhere('position', 'LIKE', "%{$request->search}%")
                ->orWhere('bio', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->sort_by ?? 'order';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 20;
        $members = $query->paginate($perPage);

        return response()->json($members);
    }

    /**
     * Store a newly created team member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('team', 'public');
        }

        if (!isset($validated['order'])) {
            $validated['order'] = TeamMember::count() + 1;
        }

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $member = TeamMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully',
            'data' => $member
        ], 201);
    }

    /**
     * Display the specified team member.
     */
    public function show(TeamMember $teamMember)
    {
        return response()->json([
            'success' => true,
            'data' => $teamMember
        ]);
    }

    /**
     * Update the specified team member.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($teamMember->image) {
                Storage::disk('public')->delete($teamMember->image);
            }
            $validated['image'] = $request->file('image')->store('team', 'public');
        }

        $teamMember->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully',
            'data' => $teamMember
        ]);
    }

    /**
     * Remove the specified team member.
     */
    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->image) {
            Storage::disk('public')->delete($teamMember->image);
        }

        $teamMember->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully'
        ]);
    }

    /**
     * Reorder team members.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:team_members,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            TeamMember::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Team members reordered successfully'
        ]);
    }

    /**
     * Update bulk status for team members.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:team_members,id',
            'status' => 'required|boolean',
        ]);

        TeamMember::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Team members status updated successfully'
        ]);
    }

    /**
     * Bulk delete team members.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:team_members,id',
        ]);

        $members = TeamMember::whereIn('id', $request->ids)->get();
        foreach ($members as $member) {
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
        }

        TeamMember::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team members deleted successfully'
        ]);
    }
}
