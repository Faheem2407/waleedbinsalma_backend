<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Exception;

class BusinessHelpController extends Controller
{
    public function businessHelpBannerIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_BANNER)->first();
        return view('backend.layouts.cms.businessHelp.banner', compact('data'));
    }

    public function businessHelpBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_BANNER)->first();

        if ($request->hasFile('background_image')) {
            if ($cmsData && $cmsData->background_image) {
                $image_path = public_path($cmsData->background_image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageName = uploadImage($request->file('background_image'), 'cms/business_help');
        } else {
            $imageName = $cmsData?->background_image;
        }

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HELP,
                    'section' => Section::BUSINESS_HELP_BANNER,
                ],
                [
                    'title' => $request->title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Help Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessHelpPopularArticleBannerIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLE_BANNER)->first();
        return view('backend.layouts.cms.businessHelp.popularArticleBanner', compact('data'));
    }

    public function businessHelpPopularArticleBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLE_BANNER)->first();


        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HELP,
                    'section' => Section::BUSINESS_HELP_POPULAR_ARTICLE_BANNER,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                ]
            );
            return redirect()->back()->with('t-success', 'Business Help Popular Article Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }


    public function businessPopularArticlesIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_HELP)
                ->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)
                ->select('id', 'title', 'description', 'background_image', 'status')
                ->latest()
                ->get();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', fn($data) => '<p>' . Str::limit($data->title, 100) . '</p>')
                ->addColumn('description', fn($data) => '<p>' . Str::limit($data->description, 100) . '</p>')
                ->addColumn('background_image', function ($data) {
                    $image = asset($data->background_image);
                    return "<img src='$image' width='80px' />";
                })
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch">
                    <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" name="status"'
                        . ($data->status == "active" ? "checked" : "") . '>
                    <label class="form-check-label" for="customSwitch' . $data->id . '"></label>
                </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm">
                    <a href="' . route('businessHelp.popularArticles.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'description', 'background_image', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.businessHelp.popularArticle.index');
    }


    public function businessPopularArticlesCreate(): View
    {
        return view('backend.layouts.cms.businessHelp.popularArticle.create');
    }


    public function businessPopularArticlesStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'background_image' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $backgroundImageName = uploadImage($request->file('background_image'), 'cms/business-help/popular-articles');

        try {
            CMS::create([
                'page' => Page::BUSINESS_HELP,
                'section' => Section::BUSINESS_HELP_POPULAR_ARTICLES,
                'title' => $request->title,
                'description' => $request->description,
                'background_image' => $backgroundImageName,
            ]);
            return to_route('businessHelp.popularArticles.index')->with('t-success', 'Business Help Popular Articles created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessPopularArticlesEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.businessHelp.popularArticle.edit', compact('data'));
    }


    public function businessPopularArticlesUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'background_image' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)
            ->where('id', $id)
            ->firstOrFail();

        if ($request->hasFile('background_image')) {
            if ($data->background_image && file_exists(public_path($data->background_image))) {
                unlink(public_path($data->background_image));
            }
            $backgroundImageName = uploadImage($request->file('background_image'), 'cms/business-help/popular-articles');
        } else {
            $backgroundImageName = $data->background_image;
        }

        try {
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
                'background_image' => $backgroundImageName,
            ]);
            return to_route('businessHelp.popularArticles.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessPopularArticlesStatus(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            't-success' => $data->status === 'active',
            'message' => $data->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }


    // Delete
    public function businessHelpPopularArticlesDestroy(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);

        // if ($data->background_image && file_exists(public_path($data->background_image))) {
        //     unlink(public_path($data->background_image));
        // }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }


    public function businessHelpKnowledgeBaseBannerIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE_BANNER)->first();
        return view('backend.layouts.cms.businessHelp.knowledgeBaseBanner', compact('data'));
    }

    public function businessHelpKnowledgeBaseBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE_BANNER)->first();


        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HELP,
                    'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE_BANNER,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Help Knowledge Base Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }






    public function businessKnowledgeBaseIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_HELP)
                ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
                ->latest()
                ->get();

            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', fn($data) => '<p>' . Str::limit($data->title, 100) . '</p>')
                ->addColumn('sub_title', fn($data) => '<p>' . Str::limit($data->sub_title, 100) . '</p>')
                ->addColumn('description', fn($data) => '<p>' . Str::limit($data->description, 100) . '</p>')
                ->addColumn('icon', function ($data) {
                    $image = asset($data->icon);
                    return "<img src='$image' width='80px' />";
                })
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch">
                    <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" name="status"'
                        . ($data->status == "active" ? "checked" : "") . '>
                    <label class="form-check-label" for="customSwitch' . $data->id . '"></label>
                </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm">
                    <a href="' . route('businessHelp.knowledgeBase.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'sub_title', 'description', 'icon', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.businessHelp.knowledgeBase.index');
    }


    public function businessKnowledgeBaseCreate(): View
    {
        return view('backend.layouts.cms.businessHelp.knowledgeBase.create');
    }


    public function businessKnowledgeBaseStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $iconName = uploadImage($request->file('icon'), 'cms/business-knowledge-base');

        try {
            CMS::create([
                'page' => Page::BUSINESS_HELP,
                'section' => Section::BUSINESS_HELP_KNOWLEDGE_BASE,
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'description' => $request->description,
                'icon' => $iconName,
            ]);
            return to_route('businessHelp.knowledgeBase.index')->with('t-success', 'Business Help Knowledge Base section created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessKnowledgeBaseEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.businessHelp.knowledgeBase.edit', compact('data'));
    }


    public function businessKnowledgeBaseUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
            ->where('id', $id)
            ->firstOrFail();

        if ($request->hasFile('icon')) {
            if ($data->icon && file_exists(public_path($data->icon))) {
                unlink(public_path($data->icon));
            }
            $iconName = uploadImage($request->file('icon'), 'cms/business-knowledge-base');
        } else {
            $iconName = $data->icon;
        }

        try {
            $data->update([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'description' => $request->description,
                'icon' => $iconName,
            ]);
            return to_route('businessHelp.knowledgeBase.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessKnowledgeBaseStatus(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            't-success' => $data->status === 'active',
            'message' => $data->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }


    public function businessKnowledgeBaseDestroy(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);

        if ($data->icon && file_exists(public_path($data->icon))) {
            unlink(public_path($data->icon));
        }

        $data->delete();

        return response()->json([
            't-success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }



    public function businessHelpIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP)->first();
        return view('backend.layouts.cms.businessHelp.help', compact('data'));
    }

    public function businessHelpUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HELP)->where('section', Section::BUSINESS_HELP)->first();

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HELP,
                    'section' => Section::BUSINESS_HELP,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'button_text' => $request->button_text,
                    'button_link' => $request->button_link,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Help Page help section Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }
}
