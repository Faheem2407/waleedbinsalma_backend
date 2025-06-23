<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BusinessHelpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_BANNER,
            'title' => 'Explore. Learn. Grow.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_POPULAR_ARTICLE_BANNER,
            'title' => 'Popular articles',
            'sub_title' => 'Explore Insights and Tips from Our Top Resources',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_POPULAR_ARTICLES,
            'title' => 'Add and manage client patch tests',
            'description' => 'Onboard new team members by setting them up with a profile in your workspace.',
            'background_image'=> 'backend/images/cms/popular/popular_one.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_POPULAR_ARTICLES,
            'title' => 'Add team members',
            'description' => 'Incorporating patch tests for certain services to detect potential allergies.',
            'background_image'=> 'backend/images/cms/popular/popular_two.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_POPULAR_ARTICLES,
            'title' => 'Creating smart pricing',
            'description' => 'Craft and launch effective email campaigns that drive client engagement and boost sales.',
            'background_image'=> 'backend/images/cms/popular/popular_three.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_POPULAR_ARTICLES,
            'title' => 'Create email blast campaigns',
            'description' => 'Set up smart pricing to automatically increase and decrease during busy or quiet periods.',
            'background_image'=> 'backend/images/cms/popular/popular_four.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE_BANNER,
            'title' => 'Knowledge base',
            'sub_title' => 'Dive into our guides and master the ins-and-outs of using Fresha for your business',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);



        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Client messaging',
            'sub_title' => 'Enhance your Fresha experience by enabling add-ons and and integrations',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/badge.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Services and memberships',
            'sub_title' => 'Business Pricing Section Description',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/book.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Team',
            'sub_title' => 'Business Pricing Section Description',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/bottles.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Clients',
            'sub_title' => 'Track and analyze data to gain valuable business insights',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/calendar.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Sales and checkout',
            'sub_title' => 'Boost sales and keep clients coming back with gift cards',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/group1.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Calendar and schedule',
            'sub_title' => 'Boost sales and keep clients coming back with gift cards',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/lock.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Billing and fees',
            'sub_title' => 'Track and analyze data to gain valuable business insights',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/mail.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Workspace settings',
            'sub_title' => 'Boost sales and keep clients coming back with gift cards',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/megaphone.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Add-ons and integrations',
            'sub_title' => 'Track and analyze data to gain valuable business insights',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/puzzle.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
            'title' => 'Reports and Insights',
            'sub_title' => 'Set up automated messages to keep clients updated and engaged',
            'description'=>'<p><strong>About the calendar</strong></p><p>The Fresha calendar is a powerful scheduling tool that allows your businesses to manage appointments, team availability, and resources in one central place. Designed for flexibility and ease, it helps you stay on top of your bookings while reducing admin time.<br><br><strong>Built for beauty and wellness</strong><br><br>Keep everything organized by&nbsp;syncing&nbsp;your Fresha calendar with Google, Outlook, or Apple calendars, so you never miss a booking.<br><br>Start learning more about the Fresha calendar and take full control of your schedule!<br><br><strong>Accurate availability</strong><br><br>The Fresha calendar is tailored for businesses in the beauty and wellness space, providing all the essential tools to easily manage bookings. Whether you’re scheduling&nbsp;individual,&nbsp;repeat, or&nbsp;group&nbsp;appointments, the calendar adapts to your business needs - you can also&nbsp;block&nbsp;time out for breaks or team availability!</p>',
            'icon' => 'backend/images/cms/knowledge_base/setting.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        CMS::create([
            'page' => Page::BUSINESS_HELP,
            'section' => Section::BUSINESS_HELP,
            'title' => 'Here to help',
            'sub_title' => 'Can\'t find an answer? We\'ve got the solution. Find more support and connect with our team.',
            'button_text'=>'Contact Us',
            'button_link'=>'#',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}
