<?php

namespace Database\Seeders;

use App\Models\Values;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Values::insert([

            [
                'name' => '	Organic product friendly',
            ],
            [
                'name'=>'Vegan product only'
            ],
            [
                'name'=>'	Environmentally friendly'
            ],
            [
                'name'=>'Black-owned'
            ],
            [
                'name'=>'Women-owned'
            ],
            [
                'name'=>'Asian-owned'
            ],
            [
                'name'=>'Hispanic-owned'
            ],

        ]);
    }
}
