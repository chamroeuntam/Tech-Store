<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TestOrderSeeder extends Seeder
{
    public function run()
    {
        // Create a test category
        $category = \App\Models\Category::firstOrCreate([
            'name' => 'Demo Category'
        ], [
            'description' => 'Demo category for test product'
        ]);

        // Create multiple test products
        $products = [
            [
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Demo product for sales report',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Second Product',
                'price' => 49.50,
                'description' => 'Another product for sales report',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Third Product',
                'price' => 25.00,
                'description' => 'Third product for sales report',
                'category_id' => $category->id,
            ],
        ];

        $productModels = [];
        foreach ($products as $prod) {
            $productModels[] = Product::firstOrCreate([
                'name' => $prod['name'],
            ], $prod);
        }

        // Create multiple completed orders for today
        $users = [1, 2]; // Use valid user IDs in your DB
        $orderData = [
            [
                'user_id' => $users[0],
                'items' => [
                    ['product' => $productModels[0], 'quantity' => 1, 'price' => 99.99],
                    ['product' => $productModels[1], 'quantity' => 2, 'price' => 49.50],
                ],
                'total_amount' => 99.99 + 2 * 49.50,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '123456789',
            ],
            [
                'user_id' => $users[1],
                'items' => [
                    ['product' => $productModels[2], 'quantity' => 3, 'price' => 25.00],
                ],
                'total_amount' => 3 * 25.00,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'phone' => '987654321',
            ],
        ];

        foreach ($orderData as $data) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'status' => 'completed',
                'total_amount' => $data['total_amount'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'shipping_first_name' => $data['first_name'],
                'shipping_last_name' => $data['last_name'],
                'shipping_phone' => $data['phone'],
                'shipping_email' => $data['email'],
                'shipping_address' => '123 Main St',
                'shipping_method_id' => null,
                'payment_method_id' => null,
                'shipping_cost' => 0,
                'order_notes' => 'Test order',
            ]);
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }
    }
}
