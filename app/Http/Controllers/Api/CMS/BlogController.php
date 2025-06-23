<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Blog;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BlogController extends Controller
{
    use ApiResponse;

    public function blog(Request $request)
    {
        $blog_page_title = CMS::where('page', Page::BLOG)
            ->where('section', Section::BLOG_BANNER)
            ->select('title')
            ->first();

        $perPage = 9;
        $categoryId = $request->get('blog_category_id');

        $query = Blog::select('id', 'blog_category_id', 'title','slug', 'description', 'image', 'created_at', 'updated_at');

        if ($categoryId) {
            $query->where('blog_category_id', $categoryId);
        }

        $blogs = $query->latest()->paginate($perPage);

        if ($blogs->isEmpty()) {
            return $this->error('No data found', 404);
        }

        $blogData = (object) [
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
            'current_page_url' => $blogs->url($blogs->currentPage()),
            'previous_page_url' => $blogs->previousPageUrl(),
            'next_page_url' => $blogs->nextPageUrl(),
            'per_page' => $blogs->perPage(),
            'total' => $blogs->total(),
            'blogs' => $blogs->items(),
        ];

        $data = (object) [
            'blog_banner' => $blog_page_title,
            'blog' => $blogData,
        ];

        return $this->success($data, 'Blog page data retrieved successfully', 200);
    }

    public function blogDetails($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return $this->error('Blog not found', 404);
        }

        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('slug', '!=', $blog->slug)
            ->select('id', 'blog_category_id', 'title', 'description', 'image', 'created_at', 'updated_at')
            ->latest()
            ->take(5)
            ->get();

        $blogFooter = CMS::where('page', Page::BLOG)
            ->where('section', Section::BLOG_FOOTER)
            ->select('title', 'sub_title', 'button_text', 'button_link', 'background_image')
            ->first();

        $data = (object) [
            'blog' => $blog,
            'related_blogs' => $relatedBlogs,
            'blog_footer' => $blogFooter
        ];

        return $this->success($data, 'Blog details retrieved successfully', 200);
    }

}
