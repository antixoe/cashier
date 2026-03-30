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
        Product::create(['name' => 'Apple', 'price' => 1.50, 'description' => 'Fresh red apple']);
        Product::create(['name' => 'Banana', 'price' => 0.75, 'description' => 'Yellow banana']);
        Product::create(['name' => 'Orange', 'price' => 2.00, 'description' => 'Juicy orange']);
        Product::create(['name' => 'Bread', 'price' => 3.00, 'description' => 'Whole wheat bread']);
        Product::create(['name' => 'Milk', 'price' => 2.50, 'description' => '1L milk']);
    }
}
