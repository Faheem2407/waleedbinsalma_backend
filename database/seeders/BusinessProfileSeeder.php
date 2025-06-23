<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BusinessProfile;

class BusinessProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::inRandomOrder()->take(5)->get();

        foreach ($users as $user) {
            BusinessProfile::create([
                'user_id' => $user->id,
                'business_name' => 'Company of ' . $user->name,
                'website_url' => 'https://company' . $user->id . '.com',
                'team_size' => '5-10',
                'address' => '123 Main St, City ' . $user->id,
                'longitude' => '77.1025',
                'latitude' => '28.7041',
                'do_not_business_adders' => rand(0, 1),
                'calendly' => 'https://calendly.com/user' . $user->id,
            ]);
        }
    }
}
