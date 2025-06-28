<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run()
    {
        $services = [
            ['service_name' => 'Hair Cutting', 'icon' => 'scissors', 'status' => 'active'],
            ['service_name' => 'Manicure', 'icon' => 'hand-sparkles', 'status' => 'active'],
            ['service_name' => 'Massage', 'icon' => 'spa', 'status' => 'active'],
            ['service_name' => 'Facial', 'icon' => 'face-smile', 'status' => 'active'],
            ['service_name' => 'Waxing', 'icon' => 'fire', 'status' => 'active'],
        ];

        Service::insert($services);
    }
}