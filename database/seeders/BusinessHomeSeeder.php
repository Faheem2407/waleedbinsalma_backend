<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BusinessHomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_BANNER,
            'title' => 'The #1 Software for Salons and Spas',
            'sub_title' => 'Take your salon or spa to the next level for free with the leading booking platform. Simple, flexible and powerful booking software for your business.',
            'button_text' => 'Join Now',
            'button_link' => '#',
            'background_image' => 'backend/images/cms/business_home_banner.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_STATS,
            'title' => 'The top-rated destination for beauty and wellness',
            'sub_title' => 'One solution, one software. Trusted by the best in the beauty and wellness industry',
            'satisfied_clients' => '120,000+',
            'pro_consultants' => '450,000+',
            'years_in_businesses' => '1 billion+',
            'successful_cases' => '120+ countries',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS_SECTION_TITLE,
            'title' => 'Grow Your Business Easily',
            'sub_title' => 'All-in-one tools to boost sales, streamline scheduling, and retain clients effortlessly.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Appointment scheduling',
            'description' => 'A sleek and user-friendly salon software compatible with all devices for seamless appointments scheduling and management.',
            'icon'        => 'backend/images/cms/grow_business/one.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Payment processing',
            'description' => 'Securely process client payments via pay by link, saved card and Fresha card terminals for a seamless checkout experience.',
            'icon'        => 'backend/images/cms/grow_business/two.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Point of sale (POS)',
            'description' => 'All the tools to manage your salon retail operations with barcode scanners, receipt prints and more.',
            'icon'        => 'backend/images/cms/grow_business/three.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Marketing promotions',
            'description' => 'The most powerful salon software with marketing features to grow your business and appointment bookings from new clients.',
            'icon'        => 'backend/images/cms/grow_business/four.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Product inventory and online store',
            'description' => 'Manage your stock and set up your own online store to sell products worldwide.',
            'icon'        => 'backend/images/cms/grow_business/five.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
            'title' => 'Reporting and analytics',
            'description' => 'Leverage Fresha\'s performance analytics and reporting tools to gain valuable insights into your salon\'s financials, client trends, and overall business growth.',
            'icon'        => 'backend/images/cms/grow_business/six.png',
            'button_text' => 'btn_text',
            'button_link' => 'btn_link',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_STAY_CONNECTED,
            'title' => 'Stay Connected Automatically',
            'description' => '<p>Be seen, be available, build your brand online. Create an online profile on our marketplace to get noticed by thousands of potential clients in your area.<br>From your social feed to your door - add unlimited Book now buttons to your Instagram and Facebook pages so new or existing clients can book instantly online.</p><ul><li>Attract and retain clients</li><li>Online self-booking</li><li>Get trusted ratings and reviews</li></ul>',
            'icon' => 'backend/images/cms/stay_connected_one.png',
            'button_text' => 'Stay Connected Automatically',
            'button_link' => '#',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_STAY_CONNECTED,
            'title' => 'Attract Clients Online',
            'description' => '<p>Be seen, be available, build your brand online. Create an online profile on our marketplace to get noticed by thousands of potential clients in your area.<br>From your social feed to your door - add unlimited Book now buttons to your Instagram and Facebook pages so new or existing clients can book instantly online.</p><ul><li>Attract and retain clients</li><li>Online self-booking</li><li>Get trusted ratings and reviews</li></ul>',
            'icon' => 'backend/images/cms/stay_connected_two.png',
            'button_text' => 'Online Booking',
            'button_link' => '#',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_GET_STARTED,
            'title' => 'Get started in seconds',
            'sub_title' => 'Pick a business type and try Cleanse for free',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_INTERESTED,
            'title' => 'Interested to find out more?',
            'sub_title' => 'Discover More and Begin Your Journey',
            'button_text' => 'Get Start Now',
            'button_link' => '#',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HOME,
            'section' => Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY,
            'title' => 'What our user say',
            'background_image' => 'backend/images/cms/business_home_what_client_say_banner.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
