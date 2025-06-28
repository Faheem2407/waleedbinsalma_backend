<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\CatalogServiceCategory;
use Illuminate\Database\Seeder;

class CatalogServiceCategoriesSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::take(3)->get();
        
        $categories = [
            ['name' => 'Hair Services', 'description' => 'All hair related services'],
            ['name' => 'Nail Services', 'description' => 'Manicure & pedicure services'],
            ['name' => 'Skin Care', 'description' => 'Facials and treatments'],
        ];
        
        foreach ($businesses as $business) {
            foreach ($categories as $category) {
                CatalogServiceCategory::create(array_merge(
                    ['business_profile_id' => $business->id],
                    $category
                ));
            }
        }
    }
}