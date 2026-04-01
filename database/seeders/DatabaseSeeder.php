<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);
        $customerRole = \App\Models\Role::firstOrCreate(['name' => 'customer'], ['description' => 'Customer']);

        // Admin user
        $superadminRole = \App\Models\Role::firstOrCreate(['name' => 'superadmin'], ['description' => 'Super Administrator']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id, $superadminRole->id]);

        // Customer user
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Customer', 'password' => bcrypt('password')]
        );
        $customerUser->roles()->syncWithoutDetaching([$customerRole->id]);

        // Existing test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );
        $testUser->roles()->syncWithoutDetaching([$customerRole->id]);

        $this->call(ProductSeeder::class);
    }
}
