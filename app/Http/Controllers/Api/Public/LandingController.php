<?php
// app/Http/Controllers/Api/Public/LandingController.php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\User;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    /**
     * Get all landing page data
     */
    public function index()
    {
        try {
            // Get all page sections
            $pages = Page::where('status', true)
                ->whereIn('type', ['hero', 'service', 'testimonial', 'team', 'pricing', 'faq', 'blog', 'about', 'whyus', 'contact'])
                ->orderBy('order')
                ->get();

            // Group by type
            $sections = $pages->groupBy('type');

            // Get stats
            $stats = $this->getStats();

            return response()->json([
                'success' => true,
                'data' => [
                    'sections' => $sections,
                    'stats' => $stats,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Landing API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching landing data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get website stats with error handling
     */
    public function getStats()
    {
        try {
            $stats = [
                'happy_clients' => 0,
                'complete_shipments' => 0,
                'customer_reviews' => 0,
                'active_services' => 0,
            ];

            // Get users count
            try {
                $stats['happy_clients'] = User::where('role', 'user')->count();
            } catch (\Exception $e) {
                Log::warning('Could not get users count: ' . $e->getMessage());
                $stats['happy_clients'] = 0;
            }

            // Get shipments count
            try {
                $stats['complete_shipments'] = Shipment::count();
            } catch (\Exception $e) {
                Log::warning('Could not get shipments count: ' . $e->getMessage());
                $stats['complete_shipments'] = 0;
            }

            // Get reviews count - skip if table doesn't exist
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
                    $stats['customer_reviews'] = \DB::table('reviews')->count();
                } else {
                    $stats['customer_reviews'] = 0;
                }
            } catch (\Exception $e) {
                Log::warning('Reviews table not available: ' . $e->getMessage());
                $stats['customer_reviews'] = 0;
            }

            // Get active services count
            try {
                $stats['active_services'] = Page::where('type', 'service')->where('status', true)->count();
            } catch (\Exception $e) {
                Log::warning('Could not get services count: ' . $e->getMessage());
                $stats['active_services'] = 0;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return [
                'happy_clients' => 0,
                'complete_shipments' => 0,
                'customer_reviews' => 0,
                'active_services' => 0,
            ];
        }
    }

    /**
     * Get a specific section by type
     */
    public function getSection($type)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error fetching section: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching section'
            ], 500);
        }
    }

    /**
     * Get hero sections
     */
    public function getHero()
    {
        try {
            $heroes = Page::where('status', true)
                ->where('type', 'hero')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $heroes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching hero: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get services
     */
    public function getServices()
    {
        try {
            $services = Page::where('status', true)
                ->where('type', 'service')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get testimonials
     */
    public function getTestimonials()
    {
        try {
            $testimonials = Page::where('status', true)
                ->where('type', 'testimonial')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $testimonials
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching testimonials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get team members
     */
    public function getTeam()
    {
        try {
            $team = Page::where('status', true)
                ->where('type', 'team')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $team
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching team: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get pricing plans
     */
    public function getPricing()
    {
        try {
            $pricing = Page::where('status', true)
                ->where('type', 'pricing')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pricing
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pricing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get about content
     */
    public function getAbout()
    {
        try {
            $about = Page::where('status', true)
                ->where('type', 'about')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $about
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching about: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => null
            ], 500);
        }
    }

    /**
     * Get FAQ content
     */
    public function getFaq()
    {
        try {
            $faq = Page::where('status', true)
                ->where('type', 'faq')
                ->orderBy('order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $faq
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching faq: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get Why Us content
     */
    public function getWhyUs()
    {
        try {
            $whyUs = Page::where('status', true)
                ->where('type', 'whyus')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $whyUs
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching whyus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => null
            ], 500);
        }
    }

    /**
     * Get blog posts
     */
    public function getBlog()
    {
        try {
            $blog = Page::where('status', true)
                ->where('type', 'blog')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $blog
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get contact section
     */
    public function getContact()
    {
        try {
            $contact = Page::where('status', true)
                ->where('type', 'contact')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => null
            ], 500);
        }
    }

    /**
     * Get website stats (simplified version without Review model)
     */
    public function getStatsSimple()
    {
        try {
            $stats = [
                'happy_clients' => 0,
                'complete_shipments' => 0,
                'customer_reviews' => 0,
                'active_services' => 0,
            ];

            // Get users count
            try {
                $stats['happy_clients'] = \App\Models\User::where('role', 'user')->count();
            } catch (\Exception $e) {
                // User table might not exist yet
                $stats['happy_clients'] = 0;
            }

            // Get shipments count
            try {
                $stats['complete_shipments'] = \App\Models\Shipment::count();
            } catch (\Exception $e) {
                // Shipment table might not exist yet
                $stats['complete_shipments'] = 0;
            }

            // Reviews - use 0 as default since table doesn't exist
            $stats['customer_reviews'] = 0;

            // Get active services count
            try {
                $stats['active_services'] = \App\Models\Page::where('type', 'service')->where('status', true)->count();
            } catch (\Exception $e) {
                $stats['active_services'] = 0;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return [
                'happy_clients' => 0,
                'complete_shipments' => 0,
                'customer_reviews' => 0,
                'active_services' => 0,
            ];
        }
    }
}
