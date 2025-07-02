<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    public function run()
    {
        $businesses = BusinessProfile::take(3)->get();
        
        $teamMembers = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah@example.com',
                'phone' => '5550101001',
                'country' => 'USA',
                'birthday' => '1985-06-15',
                'job_title' => 'Senior Stylist',
                'start_date' => '2020-01-10',
                'employment_type' => 'full_time',
                'employee_id' => 'EMP001',
                'photo' => ''
            ],
            // Add more team members
        ];

        foreach ($businesses as $business) {
            foreach ($teamMembers as $member) {
                Team::create(array_merge(
                    ['business_profile_id' => $business->id],
                    $member
                ));
            }
        }
    }
}