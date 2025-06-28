<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(UsersTableSeeder::class);
        $this->call(SocialMediaSeeder::class);
        $this->call(SystemSettingSeeder::class);
        $this->call(DynamicPageSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(BusinessProfileSeeder::class);
        $this->call(BusinessHomeSeeder::class);
        $this->call(BusinessPricingSeeder::class);
        $this->call(BusinessHelpSeeder::class);
        $this->call(BlogSeeder::class);        
        $this->call(AmenitiesSeeder::class);
        $this->call(HighlightsSeeder::class);
        $this->call(ValuesSeeder::class);
        

        $this->call(ServicesSeeder::class);
        $this->call(ProductCategoriesSeeder::class);
        $this->call(ProductBrandsSeeder::class);
        $this->call(TeamsSeeder::class);
        $this->call(CatalogServiceCategoriesSeeder::class);
        $this->call(CatalogServicesSeeder::class);
        $this->call(OnlineStoresSeeder::class);
        $this->call(ProductsSeeder::class);

    }
}
