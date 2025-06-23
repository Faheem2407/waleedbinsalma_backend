<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function blogBannerIndex(): View
    {
        $data = CMS::where('page', Page::BLOG)->where('section', Section::BLOG_BANNER)->first();
        return view('backend.layouts.cms.blog.banner', compact('data'));
    }

    public function blogBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BLOG)->where('section', Section::BLOG_BANNER)->first();



        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BLOG,
                    'section' => Section::BLOG_BANNER,
                ],
                [
                    'title' => $request->title,
                ]
            );

            return redirect()->back()->with('t-success', 'Blog Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function blogFooterIndex(): View
    {
        $data = CMS::where('page', Page::BLOG)->where('section', Section::BLOG_FOOTER)->first();
        return view('backend.layouts.cms.blog.footer', compact('data'));
    }

    public function blogFooterUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_BANNER)->first();

        if ($request->hasFile('background_image')) {
            if ($cmsData && $cmsData->background_image) {
                $image_path = public_path($cmsData->background_image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageName = uploadImage($request->file('background_image'), 'cms/blog');
        } else {
            $imageName = $cmsData?->background_image;
        }

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BLOG,
                    'section' => Section::BLOG_FOOTER,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'button_text' => $request->button_text,
                    'button_link' => $request->button_link,
                    'background_image' => $imageName,
                ]
            );

            return redirect()->back()->with('t-success', 'Blog Footer Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }
}
