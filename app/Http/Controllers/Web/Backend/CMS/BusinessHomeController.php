<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Enum\Page;
use App\Enum\Section;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class BusinessHomeController extends Controller
{
    public function businessHomeBannerIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_BANNER)->first();
        return view('backend.layouts.cms.businessHome.banner', compact('data'));
    }

    public function businessHomeBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_BANNER)->first();

        if ($request->hasFile('background_image')) {
            if ($cmsData && $cmsData->background_image) {
                $image_path = public_path($cmsData->background_image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageName = uploadImage($request->file('background_image'), 'cms/business_home');
        } else {
            $imageName = $cmsData?->background_image;
        }

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_BANNER,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'button_text' => $request->button_text,
                    'button_link' => $request->button_link,
                    'background_image' => $imageName,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Home Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessHomeStatsIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_STATS)->first();
        return view('backend.layouts.cms.businessHome.stats', compact('data'));
    }

    public function businessHomeStatsUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'satisfied_clients' => 'required|string',
            'pro_consultants' => 'required|string',
            'years_in_businesses' => 'required|string',
            'successful_cases' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_STATS)->first();

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_STATS,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'satisfied_clients' => $request->satisfied_clients,
                    'pro_consultants' => $request->pro_consultants,
                    'years_in_businesses' => $request->years_in_businesses,
                    'successful_cases' => $request->successful_cases,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Home Stats Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessGrowIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_HOME)
                ->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS)
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
                    <a href="' . route('businessHome.grow.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'description', 'icon', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.businessGrow.index');
    }


    public function businessGrowCreate(): View
    {
        return view('backend.layouts.cms.businessGrow.create');
    }


    public function businessGrowStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $iconName = uploadImage($request->file('icon'), 'cms/business-grow');

        try {
            CMS::create([
                'page' => Page::BUSINESS_HOME,
                'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS,
                'title' => $request->title,
                'description' => $request->description,
                'icon' => $iconName,
            ]);
            return to_route('businessHome.grow.index')->with('t-success', 'Business Grow section created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessGrowEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)
            ->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.businessGrow.edit', compact('data'));
    }


    public function businessGrowUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = CMS::where('page', Page::BUSINESS_HOME)
            ->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS)
            ->where('id', $id)
            ->firstOrFail();

        if ($request->hasFile('icon')) {
            if ($data->icon && file_exists(public_path($data->icon))) {
                unlink(public_path($data->icon));
            }
            $iconName = uploadImage($request->file('icon'), 'cms/business-grow');
        } else {
            $iconName = $data->icon;
        }

        try {
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
                'icon' => $iconName,
            ]);
            return to_route('businessHome.grow.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessGrowStatus(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            't-success' => $data->status === 'active',
            'message' => $data->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }


    public function businessGrowDestroy(int $id): JsonResponse
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

    public function businessGrowSectionTitleIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)
            ->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS_SECTION_TITLE)
            ->first();

        return view('backend.layouts.cms.businessGrow.sectionTitle', compact('data'));
    }
    public function businessGrowSectionTitleUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_GROW_YOUR_BUSINESS_SECTION_TITLE,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Grow Title section updated successfully!');
        } catch (Exception $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessStayConnectionIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_HOME)
                ->where('section', Section::BUSINESS_HOME_STAY_CONNECTED)
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
                    <a href="' . route('businessHome.stayConnection.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'description', 'icon', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.BusinessStayConnection.index');
    }


    public function businessStayConnectionCreate(): View
    {
        return view('backend.layouts.cms.BusinessStayConnection.create');
    }


    public function businessStayConnectionStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
            'icon' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        $iconName = uploadImage($request->file('icon'), 'cms/business-stay-connection');

        try {
            CMS::create([
                'page' => Page::BUSINESS_HOME,
                'section' => Section::BUSINESS_HOME_STAY_CONNECTED,
                'title' => $request->title,
                'description' => $request->description,
                'button_text' => $request->button_text,
                'button_link' => $request->button_link,
                'icon' => $iconName,
            ]);
            return to_route('businessHome.stayConnection.index')->with('t-success', 'Business Stay Connection section created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessStayConnectionEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)
            ->where('section', Section::BUSINESS_HOME_STAY_CONNECTED)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.BusinessStayConnection.edit', compact('data'));
    }

    public function businessStayConnectionUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg',
        ]);

        $data = CMS::where('page', Page::BUSINESS_HOME)
            ->where('section', Section::BUSINESS_HOME_STAY_CONNECTED)
            ->where('id', $id)
            ->firstOrFail();

        if ($request->hasFile('icon')) {
            if ($data->icon && file_exists(public_path($data->icon))) {
                unlink(public_path($data->icon));
            }
            $iconName = uploadImage($request->file('icon'), 'cms/business-stay-connection');
        } else {
            $iconName = $data->icon;
        }

        try {
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
                'button_text' => $request->button_text,
                'button_link' => $request->button_link,
                'icon' => $iconName,
            ]);
            return to_route('businessHome.stayConnection.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    public function businessStayConnectionStatus(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            't-success' => $data->status === 'active',
            'message' => $data->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }

    public function businessStayConnectionDestroy(int $id): JsonResponse
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


    public function businessHomeGetStartedIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_GET_STARTED)->first();
        return view('backend.layouts.cms.businessHome.getStarted', compact('data'));
    }

    public function businessHomeGetStartedUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_GET_STARTED)->first();

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_GET_STARTED,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Home Get Started Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessHomeInterestedIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_INTERESTED)->first();
        return view('backend.layouts.cms.businessHome.interested', compact('data'));
    }

    public function businessHomeInterestedUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string'
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_INTERESTED)->first();

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_INTERESTED,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'button_text' => $request->button_text,
                    'button_link' => $request->button_link,

                ]
            );
            return redirect()->back()->with('t-success', 'Business Home Interested Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }


    public function businessHomeWhatOurClientSayIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY)->first();
        return view('backend.layouts.cms.businessHome.whatOurClientSay', compact('data'));
    }

    public function businessHomeWhatOurClientSayUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_HOME)->where('section', Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY)->first();

        if ($request->hasFile('background_image')) {
            if ($cmsData && $cmsData->background_image) {
                $image_path = public_path($cmsData->background_image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageName = uploadImage($request->file('background_image'), 'cms/business_home/client_say_banner');
        } else {
            $imageName = $cmsData?->background_image;
        }

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_HOME,
                    'section' => Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY,
                ],
                [
                    'title' => $request->title,
                    'background_image' => $imageName,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Home Client Review Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }
}
