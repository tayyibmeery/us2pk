<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;

class LandingSettingController extends Controller
{
    /**
     * Display all landing page settings.
     */
    public function index()
    {
        $settings = LandingSetting::ordered()->get();
        $availableSections = LandingSetting::getSections();

        return response()->json([
            'success' => true,
            'data' => $settings,
            'available_sections' => $availableSections
        ]);
    }

    /**
     * Get enabled sections for landing page (public).
     */
    public function getEnabled()
    {
        $settings = LandingSetting::enabled()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get navbar items (public).
     */
    public function getNavbar()
    {
        $items = LandingSetting::enabled()
            ->navbar()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Get footer items (public).
     */
    public function getFooter()
    {
        $items = LandingSetting::enabled()
            ->footer()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Update a specific setting.
     */
    public function update(Request $request, LandingSetting $landingSetting)
    {
        $validated = $request->validate([
            'enabled' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
            'section_title' => 'nullable|string|max:255',
            'section_subtitle' => 'nullable|string|max:500',
            'nav_label' => 'nullable|string|max:100',
            'route_path' => 'nullable|string|max:255',
            'show_in_navbar' => 'sometimes|boolean',
            'show_in_footer' => 'sometimes|boolean',
            'display_options' => 'nullable|array',
        ]);

        $landingSetting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $landingSetting
        ]);
    }

    /**
     * Bulk update multiple settings.
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|integer|exists:landing_settings,id',
            'settings.*.enabled' => 'sometimes|boolean',
            'settings.*.order' => 'sometimes|integer|min:0',
            'settings.*.section_title' => 'nullable|string|max:255',
            'settings.*.section_subtitle' => 'nullable|string|max:500',
            'settings.*.nav_label' => 'nullable|string|max:100',
            'settings.*.route_path' => 'nullable|string|max:255',
            'settings.*.show_in_navbar' => 'sometimes|boolean',
            'settings.*.show_in_footer' => 'sometimes|boolean',
        ]);

        $updated = [];
        foreach ($validated['settings'] as $item) {
            $setting = LandingSetting::find($item['id']);
            if ($setting) {
                $setting->update($item);
                $updated[] = $setting;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $updated
        ]);
    }

    /**
     * Reset all settings to default.
     */
    public function reset()
    {
        LandingSetting::truncate();
        LandingSetting::initializeDefaults();

        $settings = LandingSetting::ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Settings reset to defaults',
            'data' => $settings
        ]);
    }

    /**
     * Get a single setting.
     */
    public function show(LandingSetting $landingSetting)
    {
        return response()->json([
            'success' => true,
            'data' => $landingSetting
        ]);
    }
}
