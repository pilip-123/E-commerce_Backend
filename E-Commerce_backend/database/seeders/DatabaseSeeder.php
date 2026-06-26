<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'chavyyom007@gmail.com'],
            [
                'name' => 'chavy yom',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '012345678',
                'address' => 'Phnom Penh, Cambodia',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+855 111 111',
                'address' => 'Phnom Penh',
            ]
        );

        $categories = collect([
            ['name' => 'Electronics', 'description' => 'Phones, laptops, and gadgets.'],
            ['name' => 'Fashion', 'description' => 'Clothing and accessories.'],
            ['name' => 'Home', 'description' => 'Furniture and daily essentials.'],
        ])->map(function (array $data) {
            return Category::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + ['slug' => Str::slug($data['name'])]
            );
        });

        Product::updateOrCreate(
            ['slug' => 'smart-watch-pro'],
            [
                'category_id' => $categories[0]->id,
                'name' => 'Smart Watch Pro',
                'description' => 'Track your day with style and battery life that lasts.',
                'price' => 149.99,
                'stock' => 20,
                'status' => true,
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'classic-cotton-shirt'],
            [
                'category_id' => $categories[1]->id,
                'name' => 'Classic Cotton Shirt',
                'description' => 'A clean everyday shirt in a comfortable fit.',
                'price' => 29.99,
                'stock' => 50,
                'status' => true,
            ]
        );

        Product::updateOrCreate(
            ['slug' => 'minimal-desk-lamp'],
            [
                'category_id' => $categories[2]->id,
                'name' => 'Minimal Desk Lamp',
                'description' => 'Soft light for a focused workspace.',
                'price' => 39.50,
                'stock' => 35,
                'status' => true,
            ]
        );
    }
}
