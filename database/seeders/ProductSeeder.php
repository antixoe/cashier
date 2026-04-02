<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fruitCategory = \App\Models\Category::firstOrCreate(['name' => 'Fruits'], ['description' => 'Fresh fruits category']);
        $bakeryCategory = \App\Models\Category::firstOrCreate(['name' => 'Bakery'], ['description' => 'Baked goods']);
        $dairyCategory = \App\Models\Category::firstOrCreate(['name' => 'Dairy'], ['description' => 'Dairy products']);

        Product::create(['name' => 'Apple', 'code' => 'FRU-001', 'price' => 1.50, 'description' => 'Fresh red apple', 'category_id' => $fruitCategory->id]);
        Product::create(['name' => 'Banana', 'code' => 'FRU-002', 'price' => 0.75, 'description' => 'Yellow banana', 'category_id' => $fruitCategory->id]);
        Product::create(['name' => 'Orange', 'code' => 'FRU-003', 'price' => 2.00, 'description' => 'Juicy orange', 'category_id' => $fruitCategory->id]);
        Product::create(['name' => 'Bread', 'code' => 'BAK-001', 'price' => 3.00, 'description' => 'Whole wheat bread', 'category_id' => $bakeryCategory->id]);
        Product::create(['name' => 'Milk', 'code' => 'DAI-001', 'price' => 2.50, 'description' => '1L milk', 'category_id' => $dairyCategory->id]);
    }
}
