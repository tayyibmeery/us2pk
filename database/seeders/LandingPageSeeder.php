<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================================
        // 1. HERO SLIDES (3 entries)
        // ============================================================
        DB::table('hero_slides')->insert([
            [
                'title' => '#1 Shipping & Logistics Solution for <span class="text-primary">USA to Pakistan</span>',
                'subtitle' => 'Shipping & Logistics Solution',
                'content' => 'Fast, Reliable & Affordable Shipping from USA to Pakistan',
                'description' => 'Trusted by thousands of customers for secure package forwarding, consolidation, and door-to-door delivery across Pakistan.',
                'image' => 'hero/hero-1.jpg',
                'button1_text' => 'Get Started',
                'button1_link' => '/signup',
                'button2_text' => 'Free Quote',
                'button2_link' => '#contact',
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Your Personal <span class="text-primary">US Address</span> for Shopping',
                'subtitle' => 'Package Forwarding Made Easy',
                'content' => 'Shop from Any US Retailer & We\'ll Deliver to Your Doorstep in Pakistan',
                'description' => 'Get your free US shipping address today. Consolidate multiple packages and save up to 70% on international shipping.',
                'image' => 'hero/hero-2.jpg',
                'button1_text' => 'Get Your Address',
                'button1_link' => '/signup',
                'button2_text' => 'Learn More',
                'button2_link' => '#services',
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Trusted Logistics Partner for <span class="text-primary">Businesses</span>',
                'subtitle' => 'Commercial Import Solutions',
                'content' => 'End-to-End Supply Chain Management from USA to Pakistan',
                'description' => 'Customs brokerage, warehousing, and fulfillment solutions for eCommerce sellers, importers, and corporate enterprises.',
                'image' => 'hero/hero-3.jpg',
                'button1_text' => 'Contact Sales',
                'button1_link' => '#contact',
                'button2_text' => 'View Services',
                'button2_link' => '#services',
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 2. ABOUT SECTIONS (1 entry with features)
        // ============================================================
        DB::table('about_sections')->insert([
            [
                'title' => 'Quick Transport and Logistics Solutions',
                'subtitle' => 'About US2PK',
                'content' => 'US2PK connects Pakistan to the world\'s best products through trusted shopping, shipping, and delivery solutions. We handle everything from purchase to doorstep with precision, security, and efficiency. Our comprehensive logistics network combines advanced technology, experienced professionals, and trusted transportation partners to deliver every shipment with the highest standards of service.',
                'image' => 'about/about-us.jpg',
                'features' => json_encode([
                    ['icon' => 'fa fa-globe', 'title' => 'Global Coverage', 'description' => 'Ship from anywhere in the USA to any city in Pakistan.'],
                    ['icon' => 'fa fa-shipping-fast', 'title' => 'On Time Delivery', 'description' => 'We guarantee your packages arrive safely and on schedule.'],
                    ['icon' => 'fa fa-shield-alt', 'title' => 'Secure & Reliable', 'description' => 'Every package is monitored, protected, and professionally managed.'],
                    ['icon' => 'fa fa-headset', 'title' => '24/7 Customer Support', 'description' => 'Our multilingual team is always ready to assist you.'],
                ]),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 3. SERVICES (6 entries)
        // ============================================================
        DB::table('services')->insert([
            [
                'title' => 'Air Freight',
                'subtitle' => 'Fast & Reliable',
                'content' => 'Fast and reliable air shipping from major US airports to all major cities in Pakistan. Ideal for urgent packages, electronics, fashion products, medical equipment, and high-value goods. Estimated delivery: 5–8 business days.',
                'image' => 'services/air-freight.jpg',
                'icon' => 'flaticon-airplane',
                'link' => '#services',
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Ocean Freight',
                'subtitle' => 'Cost-Effective Shipping',
                'content' => 'Cost‑effective sea freight for bulk shipments, containers, furniture, industrial machinery, and wholesale imports. We handle FCL and LCL shipments with full tracking and documentation. Estimated delivery: 30–45 days.',
                'image' => 'services/ocean-freight.jpg',
                'icon' => 'flaticon-ferry',
                'link' => '#services',
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Customs Clearance',
                'subtitle' => 'Hassle-Free Import',
                'content' => 'We manage all customs documentation, duties, and taxes on your behalf. Our specialists handle HS code classification, duty estimation, customs declaration, and clearance coordination. Avoid delays and ensure smooth import clearance in Pakistan.',
                'image' => 'services/customs.jpg',
                'icon' => 'flaticon-warehouse',
                'link' => '#services',
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Package Consolidation',
                'subtitle' => 'Save on Shipping',
                'content' => 'Combine multiple purchases from different US retailers into one shipment to save on shipping costs. We professionally repack, optimize weight, and securely consolidate your items with barcode tracking and inventory verification.',
                'image' => 'services/consolidation.jpg',
                'icon' => 'flaticon-box',
                'link' => '#services',
                'order' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Warehousing & Storage',
                'subtitle' => 'Secure Inventory Management',
                'content' => 'Safe, climate‑controlled warehouse storage in the USA and Pakistan. Services include package receiving, inventory management, barcode tracking, quality inspection, secure storage, and shipment preparation for both personal and commercial shipments.',
                'image' => 'services/warehouse.jpg',
                'icon' => 'flaticon-warehouse',
                'link' => '#services',
                'order' => 5,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Door‑to‑Door Delivery',
                'subtitle' => 'Complete Logistics',
                'content' => 'We pick up from your US supplier and deliver directly to your doorstep anywhere in Pakistan. Full tracking from pickup to delivery with professional handling at every stage. Includes supplier pickup, consolidation, shipping, customs clearance, and last-mile delivery.',
                'image' => 'services/door-to-door.jpg',
                'icon' => 'flaticon-lorry',
                'link' => '#services',
                'order' => 6,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 4. TESTIMONIALS (3 entries)
        // ============================================================
        DB::table('testimonials')->insert([
            [
                'name' => 'Ahmed Raza',
                'title' => 'eCommerce Business Owner',
                'content' => 'US2PK has transformed my business. I can now import products from the USA with confidence. Their consolidation service saved me over 40% on shipping costs, and their customs clearance team handled everything seamlessly. Highly recommended!',
                'image' => 'testimonials/ahmed.jpg',
                'rating' => 5,
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sana Malik',
                'title' => 'Online Shopper',
                'content' => 'I love shopping from US brands but shipping was always a hassle. US2PK made it so easy! I got my US address, shopped from multiple stores, and they consolidated everything beautifully. My package arrived in perfect condition within 7 days.',
                'image' => 'testimonials/sana.jpg',
                'rating' => 5,
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bilal Khan',
                'title' => 'Corporate Importer',
                'content' => 'We\'ve been using US2PK for our corporate imports for over two years. Their reliability, transparency, and customer service are unmatched. They handle our entire supply chain from US suppliers to our warehouse in Karachi. An invaluable logistics partner.',
                'image' => 'testimonials/bilal.jpg',
                'rating' => 5,
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 5. TEAM MEMBERS (4 entries)
        // ============================================================
        DB::table('team_members')->insert([
            [
                'name' => 'John Doe',
                'position' => 'CEO & Co-Founder',
                'bio' => 'With over 15 years of experience in international logistics and customs brokerage, John leads our operations in the USA. His vision and expertise drive our commitment to excellence in cross-border shipping.',
                'image' => 'team/john.jpg',
                'facebook' => '#',
                'twitter' => '#',
                'instagram' => '#',
                'linkedin' => '#',
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ayesha Khan',
                'position' => 'Head of Logistics',
                'bio' => 'Ayesha is our logistics expert, ensuring that every shipment is optimized for cost and speed. She manages our partnership network in Pakistan and oversees the entire supply chain operation with precision and care.',
                'image' => 'team/ayesha.jpg',
                'facebook' => '#',
                'twitter' => '#',
                'instagram' => '#',
                'linkedin' => '#',
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usman Ali',
                'position' => 'Customs Compliance Manager',
                'bio' => 'Usman specializes in customs clearance and regulatory compliance. He ensures your shipments clear Pakistan customs without any hassle, handling all documentation, duties, and import regulations with expertise.',
                'image' => 'team/usman.jpg',
                'facebook' => '#',
                'twitter' => '#',
                'instagram' => '#',
                'linkedin' => '#',
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Ahmed',
                'position' => 'Customer Success Manager',
                'bio' => 'Sarah leads our customer success team with passion and dedication. She is committed to delivering exceptional support and ensuring every client has a seamless experience from registration to final delivery.',
                'image' => 'team/sarah.jpg',
                'facebook' => '#',
                'twitter' => '#',
                'instagram' => '#',
                'linkedin' => '#',
                'order' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 6. PRICING PLANS (3 entries)
        // ============================================================
        DB::table('pricing_plans')->insert([
            [
                'title' => 'Standard',
                'section_title' => 'Choose Your Plan',
                'section_content' => 'Select the plan that best fits your shipping needs. All plans include our core logistics services with transparent pricing.',
                'price' => '$49',
                'interval' => 'shipment',
                'features' => '<li><i class="fa fa-check text-primary"></i> Consolidation Service</li><li><i class="fa fa-check text-primary"></i> Air & Sea Freight Options</li><li><i class="fa fa-check text-primary"></i> Customs Clearance</li><li><i class="fa fa-check text-primary"></i> Tracking & Notifications</li><li><i class="fa fa-check text-primary"></i> Email Support</li>',
                'button_text' => 'Get Started',
                'button_link' => '#contact',
                'featured' => false,
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Premium',
                'section_title' => 'Choose Your Plan',
                'section_content' => 'Select the plan that best fits your shipping needs. All plans include our core logistics services with transparent pricing.',
                'price' => '$99',
                'interval' => 'shipment',
                'features' => '<li><i class="fa fa-check text-primary"></i> Everything in Standard</li><li><i class="fa fa-check text-primary"></i> Priority Handling</li><li><i class="fa fa-check text-primary"></i> Expedited Customs Clearance</li><li><i class="fa fa-check text-primary"></i> 24/7 Phone Support</li><li><i class="fa fa-check text-primary"></i> Personal Account Manager</li>',
                'button_text' => 'Get Started',
                'button_link' => '#contact',
                'featured' => true,
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Business',
                'section_title' => 'Choose Your Plan',
                'section_content' => 'Select the plan that best fits your shipping needs. All plans include our core logistics services with transparent pricing.',
                'price' => 'Custom',
                'interval' => 'contact',
                'features' => '<li><i class="fa fa-check text-primary"></i> Everything in Premium</li><li><i class="fa fa-check text-primary"></i> Bulk Discounts</li><li><i class="fa fa-check text-primary"></i> Dedicated Cargo Consolidation</li><li><i class="fa fa-check text-primary"></i> Monthly Reporting</li><li><i class="fa fa-check text-primary"></i> Customs Duty Advisory</li>',
                'button_text' => 'Contact Sales',
                'button_link' => '#contact',
                'featured' => false,
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 7. FAQS (6 entries)
        // ============================================================
        DB::table('faqs')->insert([
            [
                'question' => 'How does US2PK work?',
                'answer' => 'Simply sign up to get your free US shipping address. Shop from any US retailer and have your purchases delivered to our US warehouse. We\'ll inspect, consolidate, and ship your packages directly to your doorstep in Pakistan with full tracking and customs clearance.',
                'section_title' => 'Frequently Asked Questions',
                'section_content' => 'Find answers to common questions about our services and shipping process.',
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How long does shipping from the USA to Pakistan take?',
                'answer' => 'Air freight typically takes 5–8 business days, while ocean freight takes 30–45 days depending on the port and route. We provide real‑time tracking for all shipments so you can monitor your package every step of the way.',
                'section_title' => null,
                'section_content' => null,
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What are the customs duties and taxes?',
                'answer' => 'Customs duties vary based on the product category, value, and Pakistan\'s import regulations. Our customs experts provide detailed estimates and can handle all payments on your behalf to ensure a smooth clearance process.',
                'section_title' => null,
                'section_content' => null,
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Do you offer insurance for shipments?',
                'answer' => 'Yes, we offer comprehensive cargo insurance to protect your items against loss or damage during transit. You can add insurance to any shipment for complete peace of mind.',
                'section_title' => null,
                'section_content' => null,
                'order' => 4,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I track my shipment in real-time?',
                'answer' => 'Yes! Every shipment gets a unique tracking number. You can track your package 24/7 through our customer portal with live updates on every stage of the shipping journey from pickup to delivery.',
                'section_title' => null,
                'section_content' => null,
                'order' => 5,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Do you offer pickup from my address in the USA?',
                'answer' => 'Yes, we offer door‑to‑door pickup from any location in the USA. Simply provide the address and we will arrange a courier to collect your package and deliver it to our warehouse for processing.',
                'section_title' => null,
                'section_content' => null,
                'order' => 6,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 8. BLOG POSTS (3 entries)
        // ============================================================
        DB::table('blog_posts')->insert([
            [
                'title' => 'Top 10 Tips for Importing Goods to Pakistan',
                'slug' => 'top-10-tips-importing-goods-pakistan',
                'excerpt' => 'Learn the essential tips for successful imports to Pakistan, from customs documentation to cost-saving strategies.',
                'content' => '<p>Importing goods to Pakistan can be challenging if you don\'t know the regulations. In this comprehensive guide, we share expert insights on customs clearance, duty calculation, packaging best practices, and common pitfalls to avoid.</p><p>Key topics covered include: understanding HS codes, calculating import duties, choosing the right freight option, preparing documentation, and working with customs brokers. Whether you\'re a first-time importer or an experienced business owner, these tips will help you save time and money.</p>',
                'image' => 'blog/blog-1.jpg',
                'section_title' => 'Latest Articles',
                'section_content' => 'Stay updated with the latest news, tips, and insights from the logistics industry.',
                'published_at' => now()->subDays(15),
                'author' => 'John Doe',
                'order' => 1,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Air Freight vs Sea Freight: Which is Right for You?',
                'slug' => 'air-freight-vs-sea-freight',
                'excerpt' => 'A detailed comparison of air and sea freight to help you choose the best shipping option for your needs.',
                'content' => '<p>Choosing between air and sea freight depends on your budget, timeline, and cargo type. This comprehensive guide compares both options to help you make an informed decision for your shipment from the USA to Pakistan.</p><p>We cover: delivery times, cost considerations, cargo types, packaging requirements, and customs clearance differences. Use this guide to optimize your shipping strategy and save money while ensuring reliable delivery.</p>',
                'image' => 'blog/blog-2.jpg',
                'section_title' => null,
                'section_content' => null,
                'published_at' => now()->subDays(10),
                'author' => 'Ayesha Khan',
                'order' => 2,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'How to Save Money on International Shipping',
                'slug' => 'save-money-international-shipping',
                'excerpt' => 'Discover practical strategies to reduce your international shipping costs without compromising on reliability.',
                'content' => '<p>International shipping doesn\'t have to be expensive. Learn about consolidation, forward planning, seasonal trends, and packaging optimization that can help you cut costs without compromising on reliability.</p><p>Topics include: package consolidation benefits, choosing the right shipping method, timing your shipments, negotiating rates, and using professional repackaging services. Start saving on your US to Pakistan shipments today.</p>',
                'image' => 'blog/blog-3.jpg',
                'section_title' => null,
                'section_content' => null,
                'published_at' => now()->subDays(5),
                'author' => 'Usman Ali',
                'order' => 3,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 9. WHY US SECTIONS (1 entry with features)
        // ============================================================
        DB::table('why_us_sections')->insert([
            [
                'title' => 'Why Choose US2PK',
                'content' => 'We understand the challenges of shipping from the USA to Pakistan. Our expertise, network, and commitment to customer satisfaction set us apart. With thousands of successful shipments delivered, we are the trusted choice for individuals and businesses alike.',
                'image' => 'whyus/why-choose-us.jpg',
                'features' => json_encode([
                    'Transparent pricing with no hidden fees',
                    'Real‑time tracking from pickup to delivery',
                    'Expert customs clearance support',
                    'Secure packaging and consolidation services',
                    'Dedicated account managers for businesses',
                    '24/7 multilingual customer support',
                    'Door-to-door delivery across Pakistan',
                    'Secure warehousing and inventory management',
                    'Business logistics and supply chain solutions',
                    'Competitive rates with premium service quality'
                ]),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 10. CONTACT SECTIONS (1 entry)
        // ============================================================
        DB::table('contact_sections')->insert([
            [
                'title' => 'Request A Free Quote!',
                'subtitle' => 'Get A Quote',
                'content' => 'Tell us what you need and we\'ll give you a competitive price – no obligation. Our logistics experts are ready to assist you with all your shipping needs from the USA to Pakistan.',
                'phone' => '+92 123 4567890',
                'email' => 'info@us2pk.com',
                'address' => 'Lahore, Pakistan',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 11. FOOTER SECTIONS (1 entry) - UPDATED WITH NEW STRUCTURE
        // ============================================================
        DB::table('footer_sections')->insert([
            [
                'title' => 'US2PK',
                'address' => 'Lahore, Pakistan',
                'phone' => '+92 123 4567890',
                'email' => 'info@us2pk.com',
                'whatsapp_number' => '923015579810', // NEW: WhatsApp number
                'copyright' => '© 2026 US2PK. All rights reserved.',
                'newsletter_text' => 'Connecting Pakistan to the world\'s best products through trusted shopping, shipping, and delivery.',

                // NEW: Social Icons as JSON array with platform, url, and icon
                'social_icons' => json_encode([
                    ['platform' => 'twitter', 'url' => 'https://twitter.com/us2pk', 'icon' => 'fab fa-twitter'],
                    ['platform' => 'facebook', 'url' => 'https://facebook.com/us2pk', 'icon' => 'fab fa-facebook-f'],
                    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/us2pk', 'icon' => 'fab fa-linkedin-in'],
                    ['platform' => 'youtube', 'url' => 'https://youtube.com/us2pk', 'icon' => 'fab fa-youtube'],
                    ['platform' => 'instagram', 'url' => 'https://instagram.com/us2pk', 'icon' => 'fab fa-instagram'],
                ]),

                // Service Links (What we offer)
                'service_links' => json_encode([
                    ['title' => 'Air Freight', 'url' => '#services-section'],
                    ['title' => 'Sea Freight', 'url' => '#services-section'],
                    ['title' => 'Road Freight', 'url' => '#services-section'],
                    ['title' => 'Customs Clearance', 'url' => '#services-section'],
                    ['title' => 'Warehousing', 'url' => '#services-section'],
                ]),

                // Company Links (About the company)
                'company_links' => json_encode([
                    ['title' => 'About Us', 'url' => '/about'],
                    ['title' => 'Contact Us', 'url' => '/contact'],
                    ['title' => 'Our Services', 'url' => '#services-section'],
                    ['title' => 'Support', 'url' => '/support'],
                ]),

                // Quick Links (Additional pages)
                'quick_links' => json_encode([
                    ['title' => 'Terms & Conditions', 'url' => '/terms'],
                    ['title' => 'Privacy Policy', 'url' => '/privacy'],
                    ['title' => 'FAQs', 'url' => '/faq'],
                    ['title' => 'Track Order', 'url' => '/track'],
                ]),

                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 12. STATISTICS (1 entry)
        // ============================================================
        DB::table('stats')->insert([
            [
                'happy_clients' => 12500,
                'complete_shipments' => 45678,
                'customer_reviews' => 2345,
                'active_services' => 15,
                'section_title' => 'Our Achievements',
                'section_content' => 'Thousands of satisfied customers trust US2PK for their shipping needs from the USA to Pakistan.',
                'phone' => '+92 123 4567890',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 13. PROHIBITED ITEMS (12 entries)
        // ============================================================
        DB::table('prohibited_items')->insert([
            [
                'item_name' => 'Firearms and Ammunition',
                'category' => 'Weapons',
                'description' => 'All types of firearms, guns, pistols, rifles, and ammunition.',
                'reason' => 'Strictly prohibited by Pakistan customs regulations and international shipping laws.',
                'severity' => 'high',
                'icon' => 'fas fa-gun',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Illegal Drugs and Narcotics',
                'category' => 'Drugs',
                'description' => 'Any form of illegal drugs, narcotics, or controlled substances.',
                'reason' => 'Violation of international drug trafficking laws and Pakistan customs regulations.',
                'severity' => 'high',
                'icon' => 'fas fa-skull',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Explosives and Fireworks',
                'category' => 'Hazardous Materials',
                'description' => 'Fireworks, explosives, blasting caps, and other explosive materials.',
                'reason' => 'Poses serious safety and security risks during transportation.',
                'severity' => 'high',
                'icon' => 'fas fa-bomb',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Perishable Food Items',
                'category' => 'Perishable Goods',
                'description' => 'Fresh fruits, vegetables, meat, dairy, and other perishable foods.',
                'reason' => 'Risk of spoilage and contamination during transit.',
                'severity' => 'medium',
                'icon' => 'fas fa-apple-alt',
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Counterfeit Goods',
                'category' => 'Illegal Items',
                'description' => 'Fake branded products, counterfeit currency, and pirated items.',
                'reason' => 'Violation of intellectual property laws and customs regulations.',
                'severity' => 'high',
                'icon' => 'fas fa-copyright',
                'is_active' => true,
                'order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Flammable Liquids',
                'category' => 'Hazardous Materials',
                'description' => 'Gasoline, paint thinner, alcohol, and other flammable liquids.',
                'reason' => 'Fire hazard during transportation and storage.',
                'severity' => 'high',
                'icon' => 'fas fa-fire',
                'is_active' => true,
                'order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Corrosive Chemicals',
                'category' => 'Hazardous Materials',
                'description' => 'Acids, alkalis, and other corrosive substances.',
                'reason' => 'Can cause damage to other cargo and harm handlers.',
                'severity' => 'high',
                'icon' => 'fas fa-flask',
                'is_active' => true,
                'order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Lithium Batteries',
                'category' => 'Electronics',
                'description' => 'Loose lithium batteries and power banks.',
                'reason' => 'Fire risk during air transportation.',
                'severity' => 'medium',
                'icon' => 'fas fa-battery-full',
                'is_active' => true,
                'order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Radioactive Materials',
                'category' => 'Hazardous Materials',
                'description' => 'Any radioactive substances or materials.',
                'reason' => 'Extreme health and safety hazards.',
                'severity' => 'high',
                'icon' => 'fas fa-radiation',
                'is_active' => true,
                'order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Live Animals',
                'category' => 'Live Animals',
                'description' => 'Live animals, birds, reptiles, and insects.',
                'reason' => 'Strict quarantine and health regulations.',
                'severity' => 'medium',
                'icon' => 'fas fa-paw',
                'is_active' => true,
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Pornographic Materials',
                'category' => 'Illegal Items',
                'description' => 'Obscene or pornographic content in any form.',
                'reason' => 'Violation of Pakistan content laws.',
                'severity' => 'high',
                'icon' => 'fas fa-ban',
                'is_active' => true,
                'order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_name' => 'Currency and Monetary Instruments',
                'category' => 'Currency',
                'description' => 'Large amounts of cash, traveler\'s checks, and bearer bonds.',
                'reason' => 'Subject to strict financial regulations.',
                'severity' => 'medium',
                'icon' => 'fas fa-money-bill-wave',
                'is_active' => true,
                'order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ============================================================
        // 14. QUOTE REQUESTS (3 entries - for demonstration)
        // ============================================================
        DB::table('quote_requests')->insert([
            [
                'name' => 'Muhammad Hassan',
                'email' => 'hassan@example.com',
                'mobile' => '+92 300 1234567',
                'service' => 'Air Freight',
                'note' => 'I need to ship electronics from New York to Lahore. Please provide a quote for 50kg shipment.',
                'status' => 'pending',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Fatima Ahmed',
                'email' => 'fatima@example.com',
                'mobile' => '+92 301 7654321',
                'service' => 'Ocean Freight',
                'note' => 'I want to import furniture from Los Angeles to Karachi. Need FCL container pricing.',
                'status' => 'contacted',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(3),
            ],
            [
                'name' => 'Ali Raza',
                'email' => 'ali@example.com',
                'mobile' => '+92 302 9876543',
                'service' => 'Customs Clearance',
                'note' => 'I need help with customs clearance for my shipment that just arrived. Please assist.',
                'status' => 'completed',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(8),
            ],
        ]);
    }
}
