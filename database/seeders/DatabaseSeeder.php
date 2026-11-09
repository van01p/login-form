<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
         // Aman dijalankan berulang: kalau data sudah ada, jangan seed lagi
        if (User::exists()) {
            return;
        }
        
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $seller1 = User::create([
            'name' => 'Seller Satu',
            'email' => 'seller@mail.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $seller2 = User::create([
            'name' => 'Seller Dua',
            'email' => 'seller2@mail.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'status' => 'active',
        ]);

        $customer1 = User::create([
            'name' => 'Customer Satu',
            'email' => 'customer@mail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $customer2 = User::create([
            'name' => 'Customer Dua',
            'email' => 'customer2@mail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $categories = [];
        foreach (['Fashion', 'Elektronik', 'Makanan'] as $name) {
            $categories[$name] = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        // [seller, kategori, nama, harga, stok, terjual]
        $items = [
            [$seller1, 'Fashion',    'Kaos Polos Emerald',   75000,   40, 120],
            [$seller1, 'Fashion',    'Topi Mint',            50000,   25,  64],
            [$seller1, 'Elektronik', 'Earphone Bluetooth',  180000,   30,  87],
            [$seller1, 'Elektronik', 'Charger Fast 33W',    120000,   50,  45],
            [$seller1, 'Makanan',    'Keripik Singkong',     15000,  100, 210],
            [$seller1, 'Makanan',    'Kacang Bali',          20000,   80, 155],

            [$seller2, 'Fashion',    'Jaket Hoodie',        220000,   20,  33],
            [$seller2, 'Elektronik', 'Powerbank 10000mAh',  150000,   35,  58],
            [$seller2, 'Makanan',    'Es Teh Jumbo',          4000,  200, 340],
        ];

        $buyers = [$customer1, $customer2];

        foreach ($items as $i => [$seller, $category, $name, $price, $stock, $sold]) {
            $product = Product::create([
                'user_id' => $seller->id,
                'category_id' => $categories[$category]->id,
                'name' => $name,
                'description' => 'Contoh produk ' . $name,
                'price' => $price,
                'stock' => $stock,
                'sold' => $sold,
            ]);

            // Riwayat pembelian contoh supaya dashboard seller/admin ada isinya
            $buyer    = $buyers[$i % 2];
            $subtotal = $price * $sold;

            $order = Order::create([
                'user_id' => $buyer->id,
                'total' => $subtotal,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'seller_id' => $seller->id,
                'product_name' => $name,
                'price' => $price,
                'quantity' => $sold,
                'subtotal' => $subtotal,
            ]);
        }
    }
}