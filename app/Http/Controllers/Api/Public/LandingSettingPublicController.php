<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;

class LandingSettingPublicController extends Controller
{
    public function index()
    {
        $settings = LandingSetting::enabled()
            ->ordered()
            ->get()
            ->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'section_key' => $setting->section_key,
                    'section_name' => $setting->section_name,
                    'component_name' => $setting->component_name,
                    'icon' => $setting->icon,
                    'enabled' => $setting->enabled,
                    'order' => $setting->order,
                    'section_title' => $setting->section_title,
                    'section_subtitle' => $setting->section_subtitle,
                    'route_path' => $setting->route_path,
                    'nav_label' => $setting->nav_label,
                    'show_in_navbar' => $setting->show_in_navbar,
                    'show_in_footer' => $setting->show_in_footer,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
