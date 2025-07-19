<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin and tenant users
        $admin = User::where('email', 'admin@canteen.com')->first();
        $tenant = User::where('email', 'tenant@canteen.com')->first();

        // Create menu categories
        $categories = [
            [
                'name' => 'Main Dishes',
                'description' => 'Hearty main courses and entrees',
                'sort_order' => 1,
            ],
            [
                'name' => 'Appetizers',
                'description' => 'Small plates and starters',
                'sort_order' => 2,
            ],
            [
                'name' => 'Beverages',
                'description' => 'Hot and cold drinks',
                'sort_order' => 3,
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats and desserts',
                'sort_order' => 4,
            ],
            [
                'name' => 'Snacks',
                'description' => 'Quick bites and light snacks',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            MenuCategory::create($categoryData);
        }

        // Get created categories
        $mainDishes = MenuCategory::where('slug', 'main-dishes')->first();
        $appetizers = MenuCategory::where('slug', 'appetizers')->first();
        $beverages = MenuCategory::where('slug', 'beverages')->first();
        $desserts = MenuCategory::where('slug', 'desserts')->first();
        $snacks = MenuCategory::where('slug', 'snacks')->first();

        // Create menu items
        $menuItems = [
            // Main Dishes (Admin owned)
            [
                'name' => 'Grilled Chicken Breast',
                'description' => 'Tender grilled chicken breast served with rice and vegetables',
                'price' => 12.99,
                'preparation_time' => 20,
                'menu_category_id' => $mainDishes->id,
                'user_id' => $admin->id,
                'is_featured' => true,
                'calories' => 450,
                'dietary_info' => ['gluten-free', 'high-protein'],
                'ingredients' => 'Chicken breast, rice, mixed vegetables, herbs, spices',
            ],
            [
                'name' => 'Beef Burger Deluxe',
                'description' => 'Juicy beef patty with cheese, lettuce, tomato, and fries',
                'price' => 14.50,
                'preparation_time' => 15,
                'menu_category_id' => $mainDishes->id,
                'user_id' => $admin->id,
                'calories' => 680,
                'ingredients' => 'Beef patty, cheese, lettuce, tomato, onion, bun, potato fries',
            ],
            [
                'name' => 'Vegetarian Pasta',
                'description' => 'Fresh pasta with seasonal vegetables in marinara sauce',
                'price' => 11.99,
                'preparation_time' => 18,
                'menu_category_id' => $mainDishes->id,
                'user_id' => $tenant->id,
                'dietary_info' => ['vegetarian'],
                'calories' => 520,
                'ingredients' => 'Pasta, seasonal vegetables, marinara sauce, herbs',
            ],

            // Appetizers (Tenant owned)
            [
                'name' => 'Spring Rolls',
                'description' => 'Crispy spring rolls with sweet chili sauce',
                'price' => 6.99,
                'preparation_time' => 10,
                'menu_category_id' => $appetizers->id,
                'user_id' => $tenant->id,
                'is_featured' => true,
                'calories' => 180,
                'dietary_info' => ['vegetarian'],
                'ingredients' => 'Rice paper, vegetables, herbs, sweet chili sauce',
            ],
            [
                'name' => 'Chicken Wings',
                'description' => 'Spicy buffalo chicken wings with ranch dressing',
                'price' => 8.99,
                'preparation_time' => 12,
                'menu_category_id' => $appetizers->id,
                'user_id' => $admin->id,
                'calories' => 320,
                'allergens' => ['dairy'],
                'ingredients' => 'Chicken wings, buffalo sauce, ranch dressing',
            ],

            // Beverages
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 3.99,
                'preparation_time' => 3,
                'menu_category_id' => $beverages->id,
                'user_id' => $admin->id,
                'calories' => 110,
                'dietary_info' => ['vegan', 'gluten-free'],
                'ingredients' => 'Fresh oranges',
            ],
            [
                'name' => 'Iced Coffee',
                'description' => 'Cold brew coffee served over ice',
                'price' => 4.50,
                'preparation_time' => 2,
                'menu_category_id' => $beverages->id,
                'user_id' => $tenant->id,
                'calories' => 5,
                'dietary_info' => ['vegan'],
                'ingredients' => 'Coffee beans, water, ice',
            ],
            [
                'name' => 'Green Tea',
                'description' => 'Traditional green tea with honey',
                'price' => 2.99,
                'preparation_time' => 5,
                'menu_category_id' => $beverages->id,
                'user_id' => $admin->id,
                'calories' => 25,
                'dietary_info' => ['vegan', 'gluten-free'],
                'ingredients' => 'Green tea leaves, honey, hot water',
            ],

            // Desserts
            [
                'name' => 'Chocolate Brownie',
                'description' => 'Rich chocolate brownie with vanilla ice cream',
                'price' => 5.99,
                'preparation_time' => 8,
                'menu_category_id' => $desserts->id,
                'user_id' => $tenant->id,
                'calories' => 420,
                'allergens' => ['dairy', 'eggs', 'gluten'],
                'ingredients' => 'Chocolate, flour, eggs, butter, vanilla ice cream',
            ],
            [
                'name' => 'Fresh Fruit Salad',
                'description' => 'Seasonal fresh fruits with mint',
                'price' => 4.99,
                'preparation_time' => 5,
                'menu_category_id' => $desserts->id,
                'user_id' => $admin->id,
                'calories' => 120,
                'dietary_info' => ['vegan', 'gluten-free'],
                'ingredients' => 'Seasonal fruits, fresh mint',
            ],

            // Snacks
            [
                'name' => 'Potato Chips',
                'description' => 'Crispy seasoned potato chips',
                'price' => 2.99,
                'preparation_time' => 1,
                'menu_category_id' => $snacks->id,
                'user_id' => $admin->id,
                'calories' => 150,
                'dietary_info' => ['vegan'],
                'ingredients' => 'Potatoes, seasoning, vegetable oil',
            ],
            [
                'name' => 'Mixed Nuts',
                'description' => 'Assorted roasted nuts',
                'price' => 4.99,
                'preparation_time' => 1,
                'menu_category_id' => $snacks->id,
                'user_id' => $tenant->id,
                'calories' => 280,
                'allergens' => ['nuts'],
                'dietary_info' => ['vegan', 'gluten-free'],
                'ingredients' => 'Almonds, cashews, peanuts, walnuts',
            ],
        ];

        foreach ($menuItems as $itemData) {
            MenuItem::create($itemData);
        }
    }
}