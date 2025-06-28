<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\ProductBrand;
use Illuminate\Database\Seeder;

class ProductBrandsSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::take(3)->get();
        
        $brands = ['L\'Oreal', 'Olaplex', 'Dyson', 'MAC', 'NARS'];
        
        foreach ($businesses as $business) {
            foreach ($brands as $brand) {
                ProductBrand::create([
                    'business_profile_id' => $business->id,
                    'name' => $brand
                ]);
            }
        }
    }
}