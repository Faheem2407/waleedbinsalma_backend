<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BusinessPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_BANNER,
            'title' => 'The only free software for beauty and wellness professionals',
            'sub_title' => 'Focus on what you do best. With Fresha\'s Professional app you can effortlessly manage your schedule and client communication from anywhere, at any time, right from your phone.',
            'button_text' => 'Sign Up For Free',
            'button_link' => '#',
            'background_image' => 'backend/images/cms/business_pricing_banner.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_SECTION_TITLE,
            'title' => 'From Startup to Enterprise.',
            'sub_title' => 'Perfectly tailored for every stage of your growth. Get started today, no credit card needed.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_SECTION_DESCRIPTION,
            'title' => 'Free for all, no monthly fee',
            'sub_title' => 'Unlimited usage with no subscription fees! The only free platform for beauty and wellness',
            'description' => '<ul>
    <li><strong>Unlimited appointment bookings</strong><br><span>Super easy to use across mobiles, tablets and desktops.</span></li>
    <li><strong>Automated email & SMS reminders</strong><br><span>Reduce client no-shows effectively.</span></li>
    <li><strong>Real-time calendar sync</strong><br><span>Stay updated with instant schedule changes.</span></li>
    <li><strong>Client profile management</strong><br><span>Store notes, history & preferences securely.</span></li>
    <li><strong>Custom service durations & pricing</strong><br><span>Flexible setup for any business type.</span></li>
    <li><strong>Detailed reports & analytics</strong><br><span>Track performance and appointments.</span></li>
    <li><strong>Secure cloud-based system</strong><br><span>Access your data anytime, anywhere.</span></li>
</ul>',

            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_DESCRIPITON,
            'title' => 'Unlimited appointment bookings',
            'sub_title' => 'Super easy to use across mobiles, tablets and desktops',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_FAQ,
            'title' => 'Which pricing plan is right for me?',
            'description' => 'We understand that each organization is unique, requiring specific features to support its workflows and projects. Above you can see the features included in the different plans to support your needs. If you need help in choosing the right plan for you, reach out to our sales team.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_FAQ,
            'title' => 'How does our pricing work?',
            'description' => 'We understand that each organization is unique, requiring specific features to support its workflows and projects. Above you can see the features included in the different plans to support your needs. If you need help in choosing the right plan for you, reach out to our sales team.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_FAQ,
            'title' => 'What if I change my mind?',
            'description' => 'We understand that each organization is unique, requiring specific features to support its workflows and projects. Above you can see the features included in the different plans to support your needs. If you need help in choosing the right plan for you, reach out to our sales team.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_FAQ,
            'title' => 'Do you offer any discounted plans?',
            'description' => 'We understand that each organization is unique, requiring specific features to support its workflows and projects. Above you can see the features included in the different plans to support your needs. If you need help in choosing the right plan for you, reach out to our sales team.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_PRICING,
            'section' => Section::BUSINESS_PRICING_FAQ,
            'title' => 'What payment methods do you accept?',
            'description' => 'We understand that each organization is unique, requiring specific features to support its workflows and projects. Above you can see the features included in the different plans to support your needs. If you need help in choosing the right plan for you, reach out to our sales team.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
