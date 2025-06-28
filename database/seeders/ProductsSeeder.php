<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::take(3)->get();
        
        $products = [
            [
                'name' => 'Professional Hair Shampoo',
                'barcode' => '123456789012',
                'measure' => '500ml',
                'amount' => 25,
                'short_description' => 'For all hair types',
                'description' => 'Professional salon-quality shampoo',
                'supply_price' => 8.50,
                'price' => 25.00,
                'stock_quantity' => 50,
                'image_url' => 'shampoo.jpg'
            ],
            // Add more products
        ];
        
        foreach ($businesses as $business) {
            $categories = ProductCategory::where('business_profile_id', $business->id)->get();
            $brands = ProductBrand::where('business_profile_id', $business->id)->get();
            
            foreach ($products as $product) {
                Product::create(array_merge(
                    [
                        'business_profile_id' => $business->id,
                        'category_id' => $categories->random()->id,
                        'brand_id' => $brands->random()->id
                    ],
                    $product
                ));
            }
        }
    }
}