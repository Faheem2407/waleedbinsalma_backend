<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogCategory::create(['name' => 'Fresha News']);
        BlogCategory::create(['name' => 'Business Tips']);
        BlogCategory::create(['name' => 'Meet the Partners']);

        $category = BlogCategory::first();

        $blogs = [
            'one.png',
            'two.png',
            'three.png',
            'four.png',
            'five.png',
            'six.png',
        ];

        foreach ($blogs as $index => $image) {
            Blog::create([
                'blog_category_id' => $category->id,
                'title' => 'How you can book 100+ clients a month using Fresha\'s tools',
                'image' => 'backend/images/cms/blogs/' . $image,
                'description' => 'Fresha boasts a network of over 120,000 beauty businesses worldwide, with salons making up a large portion of its partners. Hair coloring is a significant revenue driver for salons but remains one of the most complex services to manage effectively. By integrating Yuv\'s groundbreaking technology, Fresha is set to revolutionize the hair coloring process—enhancing precision, streamlining inventory management, and delivering personalized and consistent services for clients. This strategic move further reinforces Fresha\'s position as an industry leader, providing salons and beauty businesses with the advanced tools they need to elevate both their service quality and operational efficiency.<br><br>Hair coloring is a complex service that requires precision and consistency. Yuv\'s technology allows salons to save personalized hair color formulas directly in customer profiles, ensuring a consistent and tailored experience for every client. For salon owners, this means they can offer a high-quality, customized service every time, which not only strengthens client loyalty and drives repeat business but also contributes to environmental sustainability.<br><br>Beyond enhancing the client experience, Yuv\'s technology significantly improves inventory management. Integrated into Fresha\'s backend systems, the solution provides salons with real-time insights into product usage and automates restocking processes. This not only reduces waste and lowers costs but also ensures that salons always have the necessary products on hand, optimizing efficiency and profitability.',
                'blog_creator' => 'Fresha',
                'status' => 'active',
            ]);
        }




        // CMS Blog banner and footer
        CMS::create([
            'page' => Page::BLOG,
            'section' => Section::BLOG_BANNER,
            'title' => 'Latest news on Fresha',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        CMS::create([
            'page' => Page::BLOG,
            'section' => Section::BLOG_FOOTER,
            'title' => 'The #1 Software for Salons and Spas',
            'sub_title' => 'Simple, flexible and powerful booking software for your business',
            'button_text' => 'Find Out More',
            'button_link' => '#',
            'background_image' => 'backend/images/cms/blog_footer.png',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
