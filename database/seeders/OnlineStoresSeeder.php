<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\OnlineStore;
use Illuminate\Database\Seeder;

class OnlineStoresSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::all();
        
        $stores = [
            [
                'name' => 'Urban Style Main Store',
                'about' => 'Premium beauty services in downtown',
                'phone' => '5551234567',
                'email' => 'info@urbanstyle.com',
                'address' => '123 Main St, New York, NY',
                'latitude' => '40.7128',
                'longitude' => '-74.0060',
                'status' => 'active'
            ],
            // Add more stores
        ];
        
        foreach ($businesses as $index => $business) {
            if (isset($stores[$index])) {
                OnlineStore::create(array_merge(
                    ['business_profile_id' => $business->id],
                    $stores[$index]
                ));
            }
        }
    }
}