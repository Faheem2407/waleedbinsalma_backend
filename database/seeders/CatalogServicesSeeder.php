<?php

namespace Database\Seeders;

use App\Models\CatalogService;
use App\Models\CatalogServiceCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;

class CatalogServicesSeeder extends Seeder
{
    public function run()
    {
        $categories = CatalogServiceCategory::all();
        $services = Service::all();
        
        $catalogServices = [
            [
                'name' => 'Premium Haircut',
                'description' => 'Includes wash, cut, and style',
                'duration' => '60',
                'price_type' => 'fixed',
                'price' => '75.00'
            ],
            [
                'name' => 'Child Stylish Haircut',
                'description' => 'Includes wash, cut, and style',
                'duration' => '80',
                'price_type' => 'fixed',
                'price' => '90.00'
            ],
            [
                'name' => 'Breed & Haircut',
                'description' => 'Includes wash, cut, and style',
                'duration' => '120',
                'price_type' => 'fixed',
                'price' => '175.00'
            ],
            
        ];
        
        foreach ($categories as $category) {
            foreach ($services as $service) {
                foreach ($catalogServices as $catalogService) {
                    CatalogService::create(array_merge(
                        [
                            'catalog_service_category_id' => $category->id,
                            'business_profile_id' => $category->business_profile_id,
                            'service_id' => $service->id
                        ],
                        $catalogService
                    ));
                }
            }
        }
    }
}
