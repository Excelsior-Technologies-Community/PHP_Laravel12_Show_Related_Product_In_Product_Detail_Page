<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductQuestion;
use App\Models\ProductReview;
use App\Models\ProductVariant;
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
        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $categoryNames = [
            'Men Fashion',
            'Women Fashion',
            'Electronics',
            'Home & Living',
            'Sports & Fitness',
        ];

        $categories = [];
        foreach ($categoryNames as $name) {
            $categories[$name] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $products = [
            ['name' => 'Classic Cotton T-Shirt', 'category' => 'Men Fashion', 'brand' => 'UrbanThread', 'price' => 799, 'discount_price' => 599, 'stock' => 45, 'tags' => ['cotton', 'casual', 'summer'], 'featured' => true, 'is_new_arrival' => true, 'sales_count' => 128],
            ['name' => 'Slim Fit Denim Jacket', 'category' => 'Men Fashion', 'brand' => 'BlueStone', 'price' => 2499, 'discount_price' => 1999, 'stock' => 18, 'tags' => ['denim', 'jacket', 'winter'], 'featured' => true, 'is_new_arrival' => false, 'sales_count' => 86],
            ['name' => 'Floral Summer Dress', 'category' => 'Women Fashion', 'brand' => 'BloomWear', 'price' => 1899, 'discount_price' => 1499, 'stock' => 24, 'tags' => ['dress', 'floral', 'summer'], 'featured' => true, 'is_new_arrival' => true, 'sales_count' => 143],
            ['name' => 'Leather Crossbody Bag', 'category' => 'Women Fashion', 'brand' => 'CrownCraft', 'price' => 2299, 'discount_price' => null, 'stock' => 12, 'tags' => ['bag', 'leather', 'accessories'], 'featured' => false, 'is_new_arrival' => true, 'sales_count' => 67],
            ['name' => 'Wireless Noise Cancelling Headphones', 'category' => 'Electronics', 'brand' => 'SoundPeak', 'price' => 5999, 'discount_price' => 4799, 'stock' => 20, 'tags' => ['audio', 'wireless', 'headphones'], 'featured' => true, 'is_new_arrival' => true, 'sales_count' => 215],
            ['name' => 'Smart Fitness Watch', 'category' => 'Electronics', 'brand' => 'PulseTech', 'price' => 3499, 'discount_price' => 2899, 'stock' => 31, 'tags' => ['watch', 'fitness', 'smart'], 'featured' => false, 'is_new_arrival' => true, 'sales_count' => 174],
            ['name' => 'Minimal Desk Lamp', 'category' => 'Home & Living', 'brand' => 'GlowNest', 'price' => 1299, 'discount_price' => 999, 'stock' => 36, 'tags' => ['lamp', 'desk', 'home'], 'featured' => false, 'is_new_arrival' => false, 'sales_count' => 51],
            ['name' => 'Ergonomic Office Chair', 'category' => 'Home & Living', 'brand' => 'ComfortCore', 'price' => 8999, 'discount_price' => 7499, 'stock' => 7, 'tags' => ['chair', 'office', 'ergonomic'], 'featured' => true, 'is_new_arrival' => false, 'sales_count' => 92],
            ['name' => 'Performance Running Shoes', 'category' => 'Sports & Fitness', 'brand' => 'SprintPro', 'price' => 3299, 'discount_price' => 2699, 'stock' => 29, 'tags' => ['shoes', 'running', 'sports'], 'featured' => true, 'is_new_arrival' => true, 'sales_count' => 188],
            ['name' => 'Yoga Mat Premium', 'category' => 'Sports & Fitness', 'brand' => 'FlexFit', 'price' => 999, 'discount_price' => 799, 'stock' => 55, 'tags' => ['yoga', 'fitness', 'mat'], 'featured' => false, 'is_new_arrival' => false, 'sales_count' => 116],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['sku' => 'SKU-' . Str::upper(Str::slug($data['name'], '-'))],
                [
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'brand' => $data['brand'],
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'],
                    'stock' => $data['stock'],
                    'details' => 'Premium quality ' . strtolower($data['name']) . ' designed for everyday use.',
                    'tags' => $data['tags'],
                    'category_id' => $categories[$data['category']]->id,
                    'status' => 'active',
                    'featured' => $data['featured'],
                    'is_new_arrival' => $data['is_new_arrival'],
                    'sales_count' => $data['sales_count'],
                ]
            );

            $variantData = match ($data['category']) {
                'Men Fashion', 'Women Fashion' => [
                    ['name' => 'Size', 'value' => 'S', 'stock' => 10],
                    ['name' => 'Size', 'value' => 'M', 'stock' => 15],
                    ['name' => 'Size', 'value' => 'L', 'stock' => 12],
                    ['name' => 'Color', 'value' => 'Black', 'stock' => 8],
                ],
                'Electronics' => [
                    ['name' => 'Color', 'value' => 'Black', 'stock' => 12],
                    ['name' => 'Color', 'value' => 'Silver', 'stock' => 8],
                ],
                default => [
                    ['name' => 'Color', 'value' => 'Natural', 'stock' => $data['stock']],
                ],
            };

            foreach ($variantData as $variant) {
                ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'name' => $variant['name'], 'value' => $variant['value']],
                    ['stock' => $variant['stock']]
                );
            }
        }

        $reviewProduct = Product::where('sku', 'SKU-WIRELESS-NOISE-CANCELLING-HEADPHONES')->first();
        if ($reviewProduct) {
            ProductReview::updateOrCreate(
                ['product_id' => $reviewProduct->id, 'customer_name' => 'Aarav Patel'],
                ['user_id' => $user->id, 'rating' => 5, 'review' => 'Excellent sound quality and comfortable for long use.', 'is_verified_purchase' => true, 'is_approved' => true]
            );
            ProductQuestion::updateOrCreate(
                ['product_id' => $reviewProduct->id, 'customer_name' => 'Meera Shah'],
                ['user_id' => $user->id, 'question' => 'Does it support wireless Bluetooth connection?', 'answer' => 'Yes, it supports Bluetooth wireless connection.', 'is_approved' => true]
            );
        }
    }
}
