<?php

namespace Database\Seeders;

use App\Models\ProhibitedItem;
use Illuminate\Database\Seeder;

class ProhibitedItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'item_name' => 'Firearms and Ammunition',
                'category' => 'Weapons',
                'description' => 'All types of firearms, guns, pistols, rifles, and ammunition.',
                'reason' => 'Strictly prohibited by Pakistan customs regulations.',
                'severity' => 'high',
                'icon' => 'fas fa-gun',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'item_name' => 'Illegal Drugs and Narcotics',
                'category' => 'Drugs',
                'description' => 'Any form of illegal drugs, narcotics, or controlled substances.',
                'reason' => 'Violation of international drug trafficking laws.',
                'severity' => 'high',
                'icon' => 'fas fa-skull',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'item_name' => 'Explosives and Fireworks',
                'category' => 'Hazardous Materials',
                'description' => 'Fireworks, explosives, blasting caps, and other explosive materials.',
                'reason' => 'Poses serious safety and security risks.',
                'severity' => 'high',
                'icon' => 'fas fa-bomb',
                'is_active' => true,
                'order' => 3,
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
            ],
            [
                'item_name' => 'Counterfeit Goods',
                'category' => 'Illegal Items',
                'description' => 'Fake branded products, counterfeit currency, and pirated items.',
                'reason' => 'Violation of intellectual property laws.',
                'severity' => 'high',
                'icon' => 'fas fa-copyright',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'item_name' => 'Flammable Liquids',
                'category' => 'Hazardous Materials',
                'description' => 'Gasoline, paint thinner, alcohol, and other flammable liquids.',
                'reason' => 'Fire hazard during transportation.',
                'severity' => 'high',
                'icon' => 'fas fa-fire',
                'is_active' => true,
                'order' => 6,
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
            ],
            [
                'item_name' => 'Currency and Monetary Instruments',
                'category' => 'Currency',
                'description' => 'Large amounts of cash, traveler\'s checks, and bearer bonds.',
                'reason' => 'Subject to strict financial regulations.',
                'severity' => 'medium',
                'icon' => 'fas fa-money-bill-wave',
                'is_active' => true,
                'order' => 8,
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
            ],
            [
                'item_name' => 'Lithium Batteries',
                'category' => 'Electronics',
                'description' => 'Loose lithium batteries and power banks.',
                'reason' => 'Fire risk during air transportation.',
                'severity' => 'medium',
                'icon' => 'fas fa-battery-full',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'item_name' => 'Pornographic Materials',
                'category' => 'Illegal Items',
                'description' => 'Obscene or pornographic content in any form.',
                'reason' => 'Violation of Pakistan content laws.',
                'severity' => 'high',
                'icon' => 'fas fa-ban',
                'is_active' => true,
                'order' => 12,
            ],
        ];

        foreach ($items as $item) {
            ProhibitedItem::create($item);
        }
    }
}
