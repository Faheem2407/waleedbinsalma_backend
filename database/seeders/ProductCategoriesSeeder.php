<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::take(3)->get();
        
        $categories = ['Hair Care', 'Skin Care', 'Makeup', 'Tools & Accessories'];
        
        foreach ($businesses as $business) {
            foreach ($categories as $category) {
                ProductCategory::create([
                    'business_profile_id' => $business->id,
                    'name' => $category
                ]);
            }
        }
    }
}