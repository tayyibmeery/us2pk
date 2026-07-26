<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\FooterSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class FooterSectionController extends Controller
{
    public function index(Request $request)
    {
        $query = FooterSection::query();

        // Search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                    ->orWhere('address', 'LIKE', "%{$request->search}%")
                    ->orWhere('email', 'LIKE', "%{$request->search}%")
                    ->orWhere('phone', 'LIKE', "%{$request->search}%")
                    ->orWhere('whatsapp_number', 'LIKE', "%{$request->search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', filter_var($request->status, FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'id';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->per_page ?? 20;
        $sections = $query->paginate($perPage);

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255|email',
            'whatsapp_number' => 'nullable|string|max:50',
            'social_icons' => 'nullable|array',
            'social_icons.*.platform' => 'nullable|string|max:255',
            'social_icons.*.url' => 'nullable|string|max:255',
            'social_icons.*.icon' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'newsletter_text' => 'nullable|string',
            'service_links' => 'nullable|array',
            'service_links.*.title' => 'nullable|string|max:255',
            'service_links.*.url' => 'nullable|string|max:255',
            'quick_links' => 'nullable|array',
            'quick_links.*.title' => 'nullable|string|max:255',
            'quick_links.*.url' => 'nullable|string|max:255',
            'company_links' => 'nullable|array',
            'company_links.*.title' => 'nullable|string|max:255',
            'company_links.*.url' => 'nullable|string|max:255',
            'status' => 'sometimes|boolean'
        ]);

        // Set default status
        if (!isset($validated['status'])) {
            $validated['status'] = true;
        }

        // Clean up empty arrays
        foreach (['social_icons', 'service_links', 'quick_links', 'company_links'] as $field) {
            if (isset($validated[$field]) && empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        $section = FooterSection::create($validated);
        return response()->json($section, 201);
    }

    public function show(FooterSection $footerSection)
    {
        return response()->json($footerSection);
    }

    public function update(Request $request, FooterSection $footerSection)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255|email',
            'whatsapp_number' => 'nullable|string|max:50',
            'social_icons' => 'nullable|array',
            'social_icons.*.platform' => 'nullable|string|max:255',
            'social_icons.*.url' => 'nullable|string|max:255',
            'social_icons.*.icon' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'newsletter_text' => 'nullable|string',
            'service_links' => 'nullable|array',
            'service_links.*.title' => 'nullable|string|max:255',
            'service_links.*.url' => 'nullable|string|max:255',
            'quick_links' => 'nullable|array',
            'quick_links.*.title' => 'nullable|string|max:255',
            'quick_links.*.url' => 'nullable|string|max:255',
            'company_links' => 'nullable|array',
            'company_links.*.title' => 'nullable|string|max:255',
            'company_links.*.url' => 'nullable|string|max:255',
            'status' => 'sometimes|boolean'
        ]);

        // Clean up empty arrays
        foreach (['social_icons', 'service_links', 'quick_links', 'company_links'] as $field) {
            if (isset($validated[$field]) && empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        $footerSection->update($validated);
        return response()->json($footerSection);
    }

    public function destroy(FooterSection $footerSection)
    {
        $footerSection->delete();
        return response()->json(['message' => 'Footer section deleted']);
    }
}
