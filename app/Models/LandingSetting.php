<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = [
        'section_key',
        'section_name',
        'component_name',
        'icon',
        'enabled',
        'order',
        'section_title',
        'section_subtitle',
        'route_path',
        'nav_label',
        'show_in_navbar',
        'show_in_footer',
        'display_options'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'show_in_navbar' => 'boolean',
        'show_in_footer' => 'boolean',
        'display_options' => 'array',
    ];

    // Get all available sections
    public static function getSections(): array
    {
        return [
            'hero' => [
                'key' => 'hero',
                'name' => 'Hero Slides',
                'component' => 'HeroSection',
                'icon' => 'fas fa-sliders-h',
                'route_path' => '#home-section',
                'nav_label' => 'Home',
                'default_title' => 'Hero Section',
                'default_subtitle' => 'Featured slides with call to action',
                'show_in_navbar' => true,
                'show_in_footer' => false,
            ],
            'about' => [
                'key' => 'about',
                'name' => 'About Section',
                'component' => 'AboutSection',
                'icon' => 'fas fa-info-circle',
                'route_path' => '#about-section',
                'nav_label' => 'About',
                'default_title' => 'About Us',
                'default_subtitle' => 'Company introduction and mission',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'services' => [
                'key' => 'services',
                'name' => 'Services',
                'component' => 'ServicesSection',
                'icon' => 'fas fa-concierge-bell',
                'route_path' => '#services-section',
                'nav_label' => 'Services',
                'default_title' => 'Our Services',
                'default_subtitle' => 'What we offer',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'testimonials' => [
                'key' => 'testimonials',
                'name' => 'Testimonials',
                'component' => 'TestimonialsSection',
                'icon' => 'fas fa-star',
                'route_path' => '#testimonials-section',
                'nav_label' => 'Testimonials',
                'default_title' => 'Client Testimonials',
                'default_subtitle' => 'What our clients say',
                'show_in_navbar' => true,
                'show_in_footer' => false,
            ],
            'team' => [
                'key' => 'team',
                'name' => 'Team Members',
                'component' => 'TeamSection',
                'icon' => 'fas fa-users',
                'route_path' => '#team-section',
                'nav_label' => 'Team',
                'default_title' => 'Our Team',
                'default_subtitle' => 'Meet the experts',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'pricing' => [
                'key' => 'pricing',
                'name' => 'Pricing Plans',
                'component' => 'PricingSection',
                'icon' => 'fas fa-tags',
                'route_path' => '#pricing-section',
                'nav_label' => 'Pricing',
                'default_title' => 'Pricing Plans',
                'default_subtitle' => 'Choose your plan',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'faq' => [
                'key' => 'faq',
                'name' => 'FAQ Section',
                'component' => 'FaqSection',
                'icon' => 'fas fa-question-circle',
                'route_path' => '#faq-section',
                'nav_label' => 'FAQ',
                'default_title' => 'Frequently Asked Questions',
                'default_subtitle' => 'Got questions? We have answers',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'blog' => [
                'key' => 'blog',
                'name' => 'Blog Posts',
                'component' => 'BlogSection',
                'icon' => 'fas fa-blog',
                'route_path' => '#blog-section',
                'nav_label' => 'Blog',
                'default_title' => 'Latest Articles',
                'default_subtitle' => 'News and updates',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'whyus' => [
                'key' => 'whyus',
                'name' => 'Why Us Section',
                'component' => 'WhyUsSection',
                'icon' => 'fas fa-handshake',
                'route_path' => '#why-us-section',
                'nav_label' => 'Why Us',
                'default_title' => 'Why Choose Us',
                'default_subtitle' => 'Our advantages',
                'show_in_navbar' => true,
                'show_in_footer' => false,
            ],
            'contact' => [
                'key' => 'contact',
                'name' => 'Contact Section',
                'component' => 'ContactSection',
                'icon' => 'fas fa-envelope',
                'route_path' => '#contact',
                'nav_label' => 'Contact',
                'default_title' => 'Contact Us',
                'default_subtitle' => 'Get in touch',
                'show_in_navbar' => true,
                'show_in_footer' => true,
            ],
            'footer' => [
                'key' => 'footer',
                'name' => 'Footer Section',
                'component' => 'FooterSection',
                'icon' => 'fas fa-shoe-prints',
                'route_path' => '#footer',
                'nav_label' => 'Footer',
                'default_title' => 'Footer',
                'default_subtitle' => 'Site footer',
                'show_in_navbar' => false,
                'show_in_footer' => false,
            ],
            'stats' => [
                'key' => 'stats',
                'name' => 'Statistics Section',
                'component' => 'StatsSection',
                'icon' => 'fas fa-chart-bar',
                'route_path' => '#stats-section',
                'nav_label' => 'Stats',
                'default_title' => 'Our Achievements',
                'default_subtitle' => 'Numbers that matter',
                'show_in_navbar' => false,
                'show_in_footer' => false,
            ],
            'prohibited_items' => [
                'key' => 'prohibited_items',
                'name' => 'Prohibited Items',
                'component' => 'ProhibitedItemsSection',
                'icon' => 'fas fa-ban',
                'route_path' => '#prohibited-items',
                'nav_label' => 'Prohibited Items',
                'default_title' => 'Prohibited Items',
                'default_subtitle' => 'Items we cannot ship',
                'show_in_navbar' => false,
                'show_in_footer' => true,
            ],
        ];
    }

    // Initialize default settings
    public static function initializeDefaults(): void
    {
        $sections = self::getSections();
        $order = 1;

        foreach ($sections as $key => $section) {
            self::firstOrCreate(
                ['section_key' => $key],
                [
                    'section_name' => $section['name'],
                    'component_name' => $section['component'],
                    'icon' => $section['icon'],
                    'enabled' => true,
                    'order' => $order,
                    'section_title' => $section['default_title'],
                    'section_subtitle' => $section['default_subtitle'],
                    'route_path' => $section['route_path'],
                    'nav_label' => $section['nav_label'],
                    'show_in_navbar' => $section['show_in_navbar'],
                    'show_in_footer' => $section['show_in_footer'],
                    'display_options' => []
                ]
            );
            $order++;
        }
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeNavbar($query)
    {
        return $query->where('show_in_navbar', true);
    }

    public function scopeFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
