<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\Team;
use App\Models\Pricing;

class LandingController extends Controller
{
    /**
     * Get all landing page data
     */
    public function index()
    {
        // Get all page sections from the pages table
        $pages = Page::where('status', true)
            ->whereIn('type', ['hero', 'service', 'testimonial', 'team', 'pricing', 'faq', 'blog', 'about', 'whyus', 'contact'])
            ->orderBy('order')
            ->get();

        // Group by type for easier frontend consumption
        $sections = $pages->groupBy('type');

        // Optionally, you can fetch additional data from other tables
        // Here are some examples if you have separate models:

        // Get testimonials if you have a dedicated testimonials table
        // $testimonials = Testimonial::where('is_active', true)->get();

        // Get services if you have a dedicated services table
        // $services = Service::where('is_active', true)->get();

        // Get team members if you have a dedicated team table
        // $team = Team::where('is_active', true)->get();

        // Get pricing plans if you have a dedicated pricing table
        // $pricing = Pricing::where('is_active', true)->get();

        // Get stats (example counts)
        $stats = [
            'happy_clients' => \App\Models\User::where('role', 'user')->count(),
            'complete_shipments' => \App\Models\Shipment::count(),
            'customer_reviews' => \App\Models\Review::count(),
            // Add more stats as needed
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'sections' => $sections,      // Page sections from the pages table
                'stats' => $stats,             // Statistics
                // 'testimonials' => $testimonials, // Uncomment if you have these tables
                // 'services' => $services,
                // 'team' => $team,
                // 'pricing' => $pricing,
            ]
        ]);
    }

    /**
     * Get a specific section by type
     */
    public function getSection($type)
    {
        $page = Page::where('status', true)
            ->where('type', $type)
            ->orderBy('order')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Section not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Get hero sections (for carousel/slider)
     */
    public function getHero()
    {
        $heroes = Page::where('status', true)
            ->where('type', 'hero')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $heroes
        ]);
    }

    /**
     * Get services
     */
    public function getServices()
    {
        $services = Page::where('status', true)
            ->where('type', 'service')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get testimonials
     */
    public function getTestimonials()
    {
        $testimonials = Page::where('status', true)
            ->where('type', 'testimonial')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $testimonials
        ]);
    }

    /**
     * Get team members
     */
    public function getTeam()
    {
        $team = Page::where('status', true)
            ->where('type', 'team')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
    }

    /**
     * Get pricing plans
     */
    public function getPricing()
    {
        $pricing = Page::where('status', true)
            ->where('type', 'pricing')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pricing
        ]);
    }

    /**
     * Get about us content
     */
    public function getAbout()
    {
        $about = Page::where('status', true)
            ->where('type', 'about')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $about
        ]);
    }

    /**
     * Get FAQ content
     */
    public function getFaq()
    {
        $faq = Page::where('status', true)
            ->where('type', 'faq')
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $faq
        ]);
    }

    /**
     * Get website stats
     */
    public function getStats()
    {
        $stats = [
            'happy_clients' => \App\Models\User::where('role', 'user')->count(),
            'complete_shipments' => \App\Models\Shipment::count(),
            'customer_reviews' => \App\Models\Review::count(),
            'active_services' => \App\Models\Service::where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
