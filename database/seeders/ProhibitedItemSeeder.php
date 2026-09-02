<?php

namespace Database\Seeders;

use App\Models\ProhibitedItem;
use Illuminate\Database\Seeder;

class ProhibitedItemSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing records
        ProhibitedItem::truncate();

        $items = [
            [
                'item_name' => 'Combustible & Flammable Items',
                'category' => 'Hazardous Materials',
                'description' => 'Easily ignitable items such as paints, oils, lighters, perfumes and nail polish.',
                'reason' => 'Fire hazard during transportation and storage.',
                'severity' => 'high',
                'icon' => 'fa fa-fire',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'item_name' => 'Uninstalled Lithium-Ion Batteries',
                'category' => 'Batteries',
                'description' => 'Standalone lithium-ion batteries that may pose fire and transport risks.',
                'reason' => 'Fire risk during air transportation and potential short-circuit hazards.',
                'severity' => 'high',
                'icon' => 'fa fa-battery-full',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'item_name' => 'Pressurized Cans & Aerosols',
                'category' => 'Hazardous Materials',
                'description' => 'Pressurized products such as hair spray, shaving cream and aerosol cans.',
                'reason' => 'Risk of explosion or combustion under pressure changes during transit.',
                'severity' => 'high',
                'icon' => 'fa fa-spray-can',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'item_name' => 'Hazardous Materials',
                'category' => 'Hazardous Materials',
                'description' => 'Dangerous goods including matches, chemicals, explosives and similar materials.',
                'reason' => 'Poses serious safety and security risks during transport.',
                'severity' => 'high',
                'icon' => 'fa fa-exclamation-triangle',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'item_name' => 'Firearms, Weapons & Weapon Parts',
                'category' => 'Weapons',
                'description' => 'Firearms, ammunition, weapon parts, replicas and tactical equipment.',
                'reason' => 'Strictly prohibited by Pakistan customs regulations and international arms laws.',
                'severity' => 'high',
                'icon' => 'fa fa-crosshairs',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'item_name' => 'Tobacco, Plants & Seeds',
                'category' => 'Agricultural Products',
                'description' => 'Tobacco, plants, seeds and other agricultural products subject to restrictions.',
                'reason' => 'Subject to strict agricultural and quarantine regulations.',
                'severity' => 'high',
                'icon' => 'fa fa-leaf',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'item_name' => 'Coffee',
                'category' => 'Food & Beverages',
                'description' => 'Coffee products that may require additional customs review or documentation.',
                'reason' => 'May require additional customs review and specific documentation.',
                'severity' => 'medium',
                'icon' => 'fa fa-coffee',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'item_name' => 'Perishable Foods',
                'category' => 'Perishable Goods',
                'description' => 'Fresh, frozen, refrigerated or other food items that can spoil during transit.',
                'reason' => 'Risk of spoilage, contamination and health hazards during transit.',
                'severity' => 'high',
                'icon' => 'fa fa-apple-alt',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'item_name' => 'Animals & Animal Skin/Fur Products',
                'category' => 'Restricted Goods',
                'description' => 'Animals and products containing animal skin, fur or other restricted materials.',
                'reason' => 'Strict quarantine and wildlife protection regulations apply.',
                'severity' => 'high',
                'icon' => 'fa fa-paw',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'item_name' => 'Alcohol',
                'category' => 'Restricted Goods',
                'description' => 'Alcoholic beverages and alcohol-based products restricted by shipping regulations.',
                'reason' => 'Strictly regulated or prohibited by Pakistan customs and import laws.',
                'severity' => 'high',
                'icon' => 'fa fa-wine-glass',
                'is_active' => true,
                'order' => 10,
            ],
            [
                'item_name' => 'Cash, Currency & Valuable Financial Items',
                'category' => 'Financial Items',
                'description' => 'Cash, currency, money orders, bonds and other financial instruments.',
                'reason' => 'Subject to strict financial regulations and anti-money laundering laws.',
                'severity' => 'high',
                'icon' => 'fa fa-money-bill',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'item_name' => 'Jewelry & Precious Stones',
                'category' => 'Valuable Items',
                'description' => 'Valuable jewelry and precious stones subject to country and insurance limits.',
                'reason' => 'Subject to country-specific limits and insurance valuation requirements.',
                'severity' => 'medium',
                'icon' => 'fa fa-gem',
                'is_active' => true,
                'order' => 12,
            ],
            [
                'item_name' => 'Lottery Tickets & Gambling Devices',
                'category' => 'Restricted Goods',
                'description' => 'Lottery tickets, gambling devices and related restricted items.',
                'reason' => 'Prohibited or restricted by gambling laws in many jurisdictions.',
                'severity' => 'high',
                'icon' => 'fa fa-ticket-alt',
                'is_active' => true,
                'order' => 13,
            ],
            [
                'item_name' => 'Prescription & Veterinary Medication',
                'category' => 'Medicines',
                'description' => 'Prescription medicines and veterinary drugs that may require authorization.',
                'reason' => 'Requires proper authorization and may be subject to strict regulations.',
                'severity' => 'high',
                'icon' => 'fa fa-pills',
                'is_active' => true,
                'order' => 14,
            ],
            [
                'item_name' => 'Pornography',
                'category' => 'Restricted Content',
                'description' => 'Sexually explicit or pornographic materials prohibited or restricted by law.',
                'reason' => 'Violation of Pakistan content laws and international obscenity regulations.',
                'severity' => 'high',
                'icon' => 'fa fa-ban',
                'is_active' => true,
                'order' => 15,
            ],
            [
                'item_name' => 'Lock Picking Devices',
                'category' => 'Security Tools',
                'description' => 'Tools designed to pick, bypass or defeat locks and security systems.',
                'reason' => 'Restricted as potential security threats and illegal in many jurisdictions.',
                'severity' => 'high',
                'icon' => 'fa fa-key',
                'is_active' => true,
                'order' => 16,
            ],
            [
                'item_name' => 'Government IDs, Licenses & Official-Looking Items',
                'category' => 'Government Documents',
                'description' => 'Government IDs, licenses, badges, uniforms or items resembling official documents.',
                'reason' => 'Potential for fraud and identity theft; restricted by law.',
                'severity' => 'high',
                'icon' => 'fa fa-id-card',
                'is_active' => true,
                'order' => 17,
            ],
        ];

        foreach ($items as $item) {
            ProhibitedItem::create($item);
        }
    }
}
