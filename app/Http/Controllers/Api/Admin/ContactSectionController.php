<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSection;
use Illuminate\Http\Request;

class ContactSectionController extends Controller
{
    /**
     * Display a listing of contact sections.
     */
    public function index(Request $request)
    {
        $query = ContactSection::query();

        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%")
                ->orWhere('subtitle', 'LIKE', "%{$request->search}%")
                ->orWhere('content', 'LIKE', "%{$request->search}%")
                ->orWhere('email', 'LIKE', "%{$request->search}%")
                ->orWhere('phone', 'LIKE', "%{$request->search}%");
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $request->per_page ?? 20;
        $sections = $query->paginate($perPage);

        return response()->json($sections);
    }

    /**
     * Store a newly created contact section.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255|email',
            'address' => 'nullable|string|max:255',
            'status' => 'sometimes|boolean'
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        $section = ContactSection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact section created successfully',
            'data' => $section
        ], 201);
    }

    /**
     * Display the specified contact section.
     */
    public function show(ContactSection $contactSection)
    {
        return response()->json([
            'success' => true,
            'data' => $contactSection
        ]);
    }

    /**
     * Update the specified contact section.
     */
    public function update(Request $request, ContactSection $contactSection)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255|email',
            'address' => 'nullable|string|max:255',
            'status' => 'sometimes|boolean'
        ]);

        $contactSection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact section updated successfully',
            'data' => $contactSection
        ]);
    }

    /**
     * Remove the specified contact section.
     */
    public function destroy(ContactSection $contactSection)
    {
        $contactSection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact section deleted successfully'
        ]);
    }
}
