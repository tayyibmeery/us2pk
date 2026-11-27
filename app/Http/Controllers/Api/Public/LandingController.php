<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\PricingPlan;
use App\Models\Faq;
use App\Models\BlogPost;
use App\Models\WhyUsSection;
use App\Models\ContactSection;
use App\Models\FooterSection;
use App\Models\Stat;
use App\Models\ProhibitedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    public function index()
    {
        try {
            $data = [
                'hero' => HeroSlide::active()->ordered()->get(),
                'about' => AboutSection::active()->get(),
                'services' => Service::active()->ordered()->get(),
                'testimonials' => Testimonial::active()->ordered()->get(),
                'team' => TeamMember::active()->ordered()->get(),
                'pricing' => PricingPlan::active()->ordered()->get(),
                'faq' => Faq::active()->ordered()->get(),
                'blog' => BlogPost::active()->ordered()->take(4)->get(),
                'whyus' => WhyUsSection::active()->get(),
                'contact' => ContactSection::active()->get(),
                // ✅ FIX: Changed from get() to first() to return single object
                'footer' => FooterSection::active()->first(),
                'stats' => Stat::active()->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Landing API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching landing data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Individual endpoint methods
    public function getHero()
    {
        return response()->json(HeroSlide::active()->ordered()->get());
    }

    public function getAbout()
    {
        return response()->json(AboutSection::active()->get());
    }

    public function getServices()
    {
        return response()->json(Service::active()->ordered()->get());
    }

    public function getTestimonials()
    {
        return response()->json(Testimonial::active()->ordered()->get());
    }

    public function getTeam()
    {
        return response()->json(TeamMember::active()->ordered()->get());
    }

    public function getPricing()
    {
        return response()->json(PricingPlan::active()->ordered()->get());
    }

    public function getFaq()
    {
        return response()->json(Faq::active()->ordered()->get());
    }

    public function getBlog()
    {
        return response()->json(BlogPost::active()->ordered()->take(4)->get());
    }

    public function getWhyUs()
    {
        return response()->json(WhyUsSection::active()->first());
    }

    public function getContact()
    {
        return response()->json(ContactSection::active()->first());
    }

    public function getFooter()
    {
        return response()->json(FooterSection::active()->first());
    }

    public function getStats()
    {
        return response()->json(Stat::active()->first());
    }

    /**
     * Get prohibited items for landing page
     */
    public function getProhibitedItems()
    {
        try {
            $items = ProhibitedItem::active()
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching prohibited items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }
}
