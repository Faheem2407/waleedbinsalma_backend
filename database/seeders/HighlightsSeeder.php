<?php

namespace Database\Seeders;

use App\Models\Highlights;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HighlightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Highlights::insert([

            [
                'name' => 'Wheelchair Accessible',
                'icon' => 'backend/images/wheelchair.png',
                'status' => 'active',
            ],
            [
                'name' => 'Pet-friendly',
                'icon' => 'backend/images/pawprint.png',
                'status' => 'active',
            ],
            [
                'name' => 'Kid-friendly',
                'icon' => 'backend/images/children.png',
                'status' => 'active',
            ],
            [
                'name' => 'Only adults',
                'icon' => 'backend/images/couple.png',
                'status' => 'active',
            ],
        ]);
    }
}
