<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            // ===================== HERO =====================
            [
                'title' => 'US2PK – USA to Pakistan Shipping & Logistics',
                'slug' => 'hero',
                'type' => 'hero',
                'content' => 'Trusted shipping, consolidation, and door‑to‑door delivery from the USA to Pakistan. Let us handle your international logistics.',
                'image' => '/depot/images/depot_hero_1.jpg',
                'order' => 1,
            ],

            // ===================== SERVICES =====================
            [
                'title' => 'Air Freight',
                'slug' => 'air-freight',
                'type' => 'service',
                'content' => 'Fast and reliable air shipping from major US airports to all major cities in Pakistan. Ideal for urgent packages and high‑value goods.',
                'icon' => 'flaticon-airplane',
                'image' => '/depot/images/depot_img_2.jpg',
                'order' => 2,
            ],
            [
                'title' => 'Ocean Freight',
                'slug' => 'ocean-freight',
                'type' => 'service',
                'content' => 'Cost‑effective sea freight for bulk shipments, containers, and heavy equipment. We handle FCL and LCL shipments with full tracking.',
                'icon' => 'flaticon-ferry',
                'image' => '/depot/images/depot_img_1.jpg',
                'order' => 3,
            ],
            [
                'title' => 'Customs Clearance',
                'slug' => 'customs-clearance',
                'type' => 'service',
                'content' => 'We manage all customs documentation, duties, and taxes on your behalf. Avoid delays and ensure smooth import clearance in Pakistan.',
                'icon' => 'flaticon-warehouse',
                'image' => '/depot/images/depot_img_3.jpg',
                'order' => 4,
            ],
            [
                'title' => 'Package Consolidation',
                'slug' => 'package-consolidation',
                'type' => 'service',
                'content' => 'Combine multiple purchases into one shipment to save on shipping costs. We repack and consolidate your items securely.',
                'icon' => 'flaticon-box',
                'image' => '/depot/images/depot_img_1.jpg',
                'order' => 5,
            ],
            [
                'title' => 'Warehousing & Storage',
                'slug' => 'warehousing',
                'type' => 'service',
                'content' => 'Safe, climate‑controlled warehouse storage in the USA and Pakistan. Short‑term and long‑term options available.',
                'icon' => 'flaticon-warehouse',
                'order' => 6,
            ],
            [
                'title' => 'Door‑to‑Door Delivery',
                'slug' => 'door-to-door',
                'type' => 'service',
                'content' => 'We pick up from your US supplier and deliver directly to your doorstep in Pakistan. Full tracking from pickup to delivery.',
                'icon' => 'flaticon-lorry',
                'order' => 7,
            ],

            // ===================== ABOUT =====================
            [
                'title' => 'Who We Are',
                'slug' => 'about',
                'type' => 'about',
                'content' => 'US2PK is a specialized logistics company connecting the United States and Pakistan. With years of experience in international shipping, customs brokerage, and supply chain management, we offer reliable, transparent, and affordable services for individuals and businesses. Our mission is to make international shipping simple, fast, and stress‑free.',
                'image' => '/depot/images/depot_delivery_1.jpg',
                'order' => 8,
            ],

            // ===================== WHY US =====================
            [
                'title' => 'Why Choose US2PK',
                'slug' => 'why-us',
                'type' => 'whyus',
                'content' => 'We understand the challenges of shipping from the USA to Pakistan. Our expertise, network, and commitment to customer satisfaction set us apart.',
                'image' => '/depot/images/depot_delivery_1.jpg',
                'meta' => [
                    'features' => [
                        'Transparent pricing with no hidden fees',
                        'Real‑time tracking from pickup to delivery',
                        'Expert customs clearance support',
                        'Secure packaging and consolidation',
                        'Dedicated account managers'
                    ]
                ],
                'order' => 9,
            ],

            // ===================== TESTIMONIALS =====================
            [
                'title' => 'Ahmed Raza',
                'slug' => 'testimonial-ahmed',
                'type' => 'testimonial',
                'content' => 'US2PK made my first international purchase so easy. They handled everything – from consolidation to customs clearance. My items arrived in perfect condition and on time.',
                'image' => '/depot/images/person_1.jpg',
                'meta' => ['rating' => 5],
                'order' => 10,
            ],
            [
                'title' => 'Sana Malik',
                'slug' => 'testimonial-sana',
                'type' => 'testimonial',
                'content' => 'As a business owner, I rely on US2PK for all my imports from the US. Their team is professional, responsive, and their rates are the best in the market.',
                'image' => '/depot/images/person_2.jpg',
                'meta' => ['rating' => 5],
                'order' => 11,
            ],
            [
                'title' => 'Bilal Khan',
                'slug' => 'testimonial-bilal',
                'type' => 'testimonial',
                'content' => 'I’ve shipped dozens of packages with US2PK and never had a single issue. Their tracking system is accurate and their customer support is top‑notch.',
                'image' => '/depot/images/person_1.jpg',
                'meta' => ['rating' => 4],
                'order' => 12,
            ],
            [
                'title' => 'Fatima Noor',
                'slug' => 'testimonial-fatima',
                'type' => 'testimonial',
                'content' => 'The consolidation service saved me so much money. US2PK is now my go‑to shipping partner for everything from the USA.',
                'image' => '/depot/images/person_3.jpg',
                'meta' => ['rating' => 5],
                'order' => 13,
            ],

            // ===================== TEAM =====================
            [
                'title' => 'John Doe',
                'slug' => 'team-john',
                'type' => 'team',
                'content' => 'John has over 15 years of experience in international logistics and customs brokerage. He leads our operations in the USA.',
                'image' => '/depot/images/person_1.jpg',
                'meta' => [
                    'position' => 'CEO & Co-Founder',
                    'facebook' => '#',
                    'twitter' => '#',
                    'instagram' => '#',
                ],
                'order' => 14,
            ],
            [
                'title' => 'Ayesha Khan',
                'slug' => 'team-ayesha',
                'type' => 'team',
                'content' => 'Ayesha is our logistics expert, ensuring that every shipment is optimized for cost and speed. She manages our partnership network in Pakistan.',
                'image' => '/depot/images/person_2.jpg',
                'meta' => [
                    'position' => 'Head of Logistics',
                    'facebook' => '#',
                    'twitter' => '#',
                    'instagram' => '#',
                ],
                'order' => 15,
            ],
            [
                'title' => 'Usman Ali',
                'slug' => 'team-usman',
                'type' => 'team',
                'content' => 'Usman specializes in customs clearance and regulatory compliance. He ensures your shipments clear Pakistan customs without any hassle.',
                'image' => '/depot/images/person_3.jpg',
                'meta' => [
                    'position' => 'Customs Compliance Manager',
                    'facebook' => '#',
                    'twitter' => '#',
                    'instagram' => '#',
                ],
                'order' => 16,
            ],
            [
                'title' => 'Sarah Ahmed',
                'slug' => 'team-sarah',
                'type' => 'team',
                'content' => 'Sarah leads our customer success team. She is passionate about delivering exceptional support and ensuring every client has a seamless experience.',
                'image' => '/depot/images/person_1.jpg',
                'meta' => [
                    'position' => 'Customer Success Manager',
                    'facebook' => '#',
                    'twitter' => '#',
                    'instagram' => '#',
                ],
                'order' => 17,
            ],

            // ===================== PRICING =====================
            [
                'title' => 'Standard',
                'slug' => 'pricing-standard',
                'type' => 'pricing',
                'content' => '<li>Consolidation service</li><li>Air & Sea freight options</li><li>Customs clearance included</li><li>Tracking and notifications</li><li>Email support</li>',
                'meta' => ['price' => '$49', 'interval' => 'shipment'],
                'order' => 18,
            ],
            [
                'title' => 'Premium',
                'slug' => 'pricing-premium',
                'type' => 'pricing',
                'content' => '<li>Everything in Standard</li><li>Priority handling</li><li>Expedited customs clearance</li><li>24/7 phone support</li><li>Personal account manager</li>',
                'meta' => ['price' => '$99', 'interval' => 'shipment'],
                'order' => 19,
            ],
            [
                'title' => 'Business',
                'slug' => 'pricing-business',
                'type' => 'pricing',
                'content' => '<li>Everything in Premium</li><li>Bulk discounts</li><li>Dedicated cargo consolidation</li><li>Monthly reporting</li><li>Customs duty advisory</li>',
                'meta' => ['price' => 'Custom', 'interval' => 'contact'],
                'order' => 20,
            ],

            // ===================== FAQ =====================
            [
                'title' => 'How long does shipping from the USA to Pakistan take?',
                'slug' => 'faq-delivery-time',
                'type' => 'faq',
                'content' => 'Air freight typically takes 5–10 business days, while ocean freight takes 30–45 days depending on the port and route. We provide real‑time tracking for all shipments.',
                'order' => 21,
            ],
            [
                'title' => 'What are the customs duties and taxes?',
                'slug' => 'faq-customs',
                'type' => 'faq',
                'content' => 'Customs duties vary based on the product category, value, and Pakistan’s import regulations. We provide detailed estimates and can handle all payments on your behalf.',
                'order' => 22,
            ],
            [
                'title' => 'Do you offer insurance for shipments?',
                'slug' => 'faq-insurance',
                'type' => 'faq',
                'content' => 'Yes, we offer comprehensive cargo insurance to protect your items against loss or damage during transit. You can add it to any shipment.',
                'order' => 23,
            ],
            [
                'title' => 'What items are prohibited?',
                'slug' => 'faq-prohibited',
                'type' => 'faq',
                'content' => 'We do not ship illegal items, dangerous goods, perishable items, or restricted goods. Please check with our support team before shipping.',
                'order' => 24,
            ],
            [
                'title' => 'Can I track my shipment?',
                'slug' => 'faq-tracking',
                'type' => 'faq',
                'content' => 'Yes, every shipment gets a tracking number. You can track your package online 24/7 through our customer portal.',
                'order' => 25,
            ],
            [
                'title' => 'Do you offer pickup from my address in the USA?',
                'slug' => 'faq-pickup',
                'type' => 'faq',
                'content' => 'Yes, we offer door‑to‑door pickup from any location in the USA. Simply provide the address and we will arrange a courier to collect your package.',
                'order' => 26,
            ],

            // ===================== BLOG =====================
            [
                'title' => 'Top 10 Tips for Importing Goods to Pakistan',
                'slug' => 'blog-import-tips',
                'type' => 'blog',
                'content' => 'Importing goods to Pakistan can be tricky if you don’t know the regulations. In this post, we share expert tips on customs clearance, duty calculation, and packaging best practices to save you time and money.',
                'image' => '/depot/images/depot_img_1.jpg',
                'meta' => ['date' => 'June 15, 2025'],
                'order' => 27,
            ],
            [
                'title' => 'Air vs Sea Freight: Which is Right for You?',
                'slug' => 'blog-air-vs-sea',
                'type' => 'blog',
                'content' => 'Choosing between air and sea freight depends on your budget, timeline, and cargo type. This guide compares the two options to help you make an informed decision for your shipment from the USA to Pakistan.',
                'image' => '/depot/images/depot_img_2.jpg',
                'meta' => ['date' => 'May 22, 2025'],
                'order' => 28,
            ],
            [
                'title' => 'How to Save Money on International Shipping',
                'slug' => 'blog-save-money',
                'type' => 'blog',
                'content' => 'International shipping doesn’t have to be expensive. Learn about consolidation, forward planning, and seasonal trends that can help you cut costs without compromising on reliability.',
                'image' => '/depot/images/depot_img_3.jpg',
                'meta' => ['date' => 'May 5, 2025'],
                'order' => 29,
            ],

            // ===================== CONTACT =====================
            [
                'title' => 'Contact US2PK',
                'slug' => 'contact',
                'type' => 'contact',
                'content' => 'Have a question? Get in touch with our team. We are here to help with all your shipping needs from the USA to Pakistan.',
                'order' => 30,
            ],
        ];

        foreach ($pages as $pageData) {
            // Ensure slug is set if not provided
            if (empty($pageData['slug'])) {
                $pageData['slug'] = Str::slug($pageData['title']);
            }
            // Set default status
            $pageData['status'] = true;

            // Convert meta to JSON string if array
            if (isset($pageData['meta']) && is_array($pageData['meta'])) {
                $pageData['meta'] = json_encode($pageData['meta']);
            }

            // Use firstOrCreate to avoid duplicates (by slug)
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }
}
