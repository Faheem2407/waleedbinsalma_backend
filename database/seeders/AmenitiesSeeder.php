<?php

namespace Database\Seeders;

use App\Models\Amenities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Amenities::insert([
            [
                'name' => 'Available Parking',
                'icon' => 'backend/images/parking.png',
                'status' => 'active',
            ],
            [
                'name' => 'Near Public Transport',
                'icon' => 'backend/images/bus.png',
                'status' => 'active',
            ],
            [
                'name' => 'Showers',
                'icon' => 'backend/images/shower.png',
                'status' => 'active',
            ],
            [
                'name' => 'Lockers',
                'icon' => 'backend/images/locker.png',
                'status' => 'active',
            ],
            [
                'name' => 'Bath Towel',
                'icon' => 'backend/images/bath-towel.png',
                'status' => 'active',
            ],
            [
                'name' => 'Swimming Pool',
                'icon' => 'backend/images/ooooooooooooooooooooooooooooooooooo.png',
                'status' => 'active',
            ],
            [
                'name' => 'Sauna',
                'icon' => 'backend/images/sauna.png',
                'status' => 'active',
            ],
        ]);
    }
}
