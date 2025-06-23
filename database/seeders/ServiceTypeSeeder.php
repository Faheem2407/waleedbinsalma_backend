<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::insert([
            [
                'service_name' => 'haircut',
                'icon' => 'backend/images/services/haircut.png',
                'status' => 'active',
            ],
            [
                'service_name' => 'Laundry Service',
                'icon' => 'backend/images/services/washing-machine.png',
                'status' => 'active',
            ],
            [
                'service_name' => 'Spa Service',
                'icon' => 'backend/images/services/spa.png',
                'status' => 'active',
            ],
            [
                'service_name' => 'Massage Service',
                'icon' => 'backend/images/services/massage.jpg',
                'status' => 'active',
            ],
            [
                'service_name' => 'Hair Salon',
                'icon' => 'backend/images/services/hair_salon.jpg',
                'status' => 'active',
            ],
            [
                'service_name' => 'Personal Trainer',
                'icon' => 'backend/images/services/personal_trainer.jpg',
                'status' => 'active',
            ],
            [
                'service_name' => 'Gym & Training',
                'icon' => 'backend/images/services/gym.jpg',
                'status' => 'active',
            ],
            [
                'service_name' => 'Wax Salon',
                'icon' => 'backend/images/services/wax_salon.jpg',
                'status' => 'active',
            ],
            [
                'service_name' => 'Eye brows',
                'icon' => 'backend/images/services/eye_brows.jpg',
                'status' => 'active',
            ]
        ]);
    }
}
