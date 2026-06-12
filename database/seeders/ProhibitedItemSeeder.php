<?php

namespace Database\Seeders;

use App\Models\ProhibitedItem;
use Illuminate\Database\Seeder;

class ProhibitedItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['item_name' => 'Narcotics & Drugs', 'description' => 'All illegal drugs and narcotics are strictly prohibited.'],
            ['item_name' => 'Weapons & Ammunition', 'description' => 'Firearms, explosives, and any kind of weaponry.'],
            ['item_name' => 'Alcohol & Beverages', 'description' => 'Alcoholic drinks and beverages containing alcohol.'],
            ['item_name' => 'Pork Products', 'description' => 'Any item containing pork or pork by‑products.'],
            ['item_name' => 'Religious Hate Material', 'description' => 'Material that insults any religion.'],
            ['item_name' => 'Counterfeit Goods', 'description' => 'Fake branded products, replica items.'],
            ['item_name' => 'Lithium Batteries (loose)', 'description' => 'Loose lithium batteries – only allowed inside devices.'],
            ['item_name' => 'Perishable Foods', 'description' => 'Fresh fruits, vegetables, dairy products.'],
            ['item_name' => 'Currency & Monetary Instruments', 'description' => 'Cash over allowed limits, bearer bonds.'],
            ['item_name' => 'Pornography', 'description' => 'Adult content, obscene materials.'],
        ];

        foreach ($items as $item) {
            ProhibitedItem::updateOrCreate(
                ['item_name' => $item['item_name']],
                $item
            );
        }
    }
}
