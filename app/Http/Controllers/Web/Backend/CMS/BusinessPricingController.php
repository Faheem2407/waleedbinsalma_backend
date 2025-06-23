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


class BusinessPricingController extends Controller
{
    public function businessPricingBannerIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_PRICING)->where('section', Section::BUSINESS_PRICING_BANNER)->first();
        return view('backend.layouts.cms.businessPricing.banner', compact('data'));
    }

    public function businessPricingBannerUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
            'button_text' => 'required|string',
            'button_link' => 'required|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $cmsData = CMS::where('page', Page::BUSINESS_PRICING)->where('section', Section::BUSINESS_PRICING_BANNER)->first();

        if ($request->hasFile('background_image')) {
            if ($cmsData && $cmsData->background_image) {
                $image_path = public_path($cmsData->background_image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageName = uploadImage($request->file('background_image'), 'cms/business_pricing');
        } else {
            $imageName = $cmsData?->background_image;
        }

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_PRICING,
                    'section' => Section::BUSINESS_PRICING_BANNER,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                    'button_text' => $request->button_text,
                    'button_link' => $request->button_link,
                    'background_image' => $imageName,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Pricing Banner Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessPricingSectionTitleIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_PRICING)->where('section', Section::BUSINESS_PRICING_SECTION_TITLE)->first();
        return view('backend.layouts.cms.businessPricing.sectionTitle', compact('data'));
    }
    public function businessPricingSectionTitleUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_PRICING,
                    'section' => Section::BUSINESS_PRICING_SECTION_TITLE,
                ],
                [
                    'title' => $request->title,
                    'sub_title' => $request->sub_title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Pricing Section Title Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }

    public function businessPricingSectionDescriptionIndex(): View
    {
        $data = CMS::where('page', Page::BUSINESS_PRICING)->where('section', Section::BUSINESS_PRICING_SECTION_DESCRIPTION)->first();
        return view('backend.layouts.cms.businessPricing.sectionDescription', compact('data'));
    }
    public function businessPricingSectionDescriptionUpdate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        try {
            CMS::updateOrCreate(
                [
                    'page' => Page::BUSINESS_PRICING,
                    'section' => Section::BUSINESS_PRICING_SECTION_DESCRIPTION,
                ],
                [
                    'title'      => $request->title,
                    'sub_title'  => $request->sub_title,
                ]
            );

            return redirect()->back()->with('t-success', 'Business Pricing Section Description Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('t-error', $th->getMessage());
        }
    }



    public function businessPricingDescriptionIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_PRICING)
                ->where('section', Section::BUSINESS_PRICING_DESCRIPITON)
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
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch">
                    <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" name="status"'
                        . ($data->status == "active" ? "checked" : "") . '>
                    <label class="form-check-label" for="customSwitch' . $data->id . '"></label>
                </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm">
                    <a href="' . route('businessPricing.description.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'sub_title', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.businessDescription.index');
    }

    
    public function businessPricingDescriptionCreate(): View
    {
        return view('backend.layouts.cms.businessDescription.create');
    }

    
    public function businessPricingDescriptionStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title'=> 'required|string'
        ]);

        try {
            CMS::create([
                'page' => Page::BUSINESS_PRICING,
                'section' => Section::BUSINESS_PRICING_DESCRIPITON,
                'title' => $request->title,
                'sub_title' => $request->sub_title
            ]);
            return to_route('businessPricing.description.index')->with('t-success', 'Business Grow section created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function businessPricingDescriptionEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_PRICING)
            ->where('section', Section::BUSINESS_PRICING_DESCRIPITON)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.businessDescription.edit', compact('data'));
    }

    
    public function businessPricingDescriptionUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'required|string',
        ]);

        $data = CMS::where('page', Page::BUSINESS_PRICING)
            ->where('section', Section::BUSINESS_PRICING_DESCRIPITON)
            ->where('id', $id)
            ->firstOrFail();

        try {
            $data->update([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
            ]);
            return to_route('businessPricing.description.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    
    public function businessPricingDescriptionStatus(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            't-success' => $data->status === 'active',
            'message' => $data->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }

    public function businessPricingDescriptionDestroy(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);

        $data->delete();

        return response()->json([
            't-success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }




    // businessPricingFaq  section replace the following
    public function businessPricingFaqIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::BUSINESS_PRICING)
                ->where('section', Section::BUSINESS_PRICING_FAQ)
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
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch">
                    <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" name="status"'
                        . ($data->status == "active" ? "checked" : "") . '>
                    <label class="form-check-label" for="customSwitch' . $data->id . '"></label>
                </div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm">
                    <a href="' . route('businessPricing.faq.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['title', 'description', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.cms.businessPricing.faq.index');
    }

    // Create View
    public function businessPricingFaqCreate(): View
    {
        return view('backend.layouts.cms.businessPricing.faq.create');
    }

    // Store
    public function businessPricingFaqStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        try {
            CMS::create([
                'page' => Page::BUSINESS_PRICING,
                'section' => Section::BUSINESS_PRICING_FAQ,
                'title' => $request->title,
                'description' => $request->description,
            ]);
            return to_route('businessPricing.faq.index')->with('t-success', 'Business Pricing FAQ created successfully!');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    // Edit
    public function businessPricingFaqEdit(int $id): View
    {
        $data = CMS::where('page', Page::BUSINESS_PRICING)
            ->where('section', Section::BUSINESS_PRICING_FAQ)
            ->where('id', $id)
            ->firstOrFail();

        return view('backend.layouts.cms.businessPricing.faq.edit', compact('data'));
    }

    // Update
    public function businessPricingFaqUpdate(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $data = CMS::where('page', Page::BUSINESS_PRICING)
            ->where('section', Section::BUSINESS_PRICING_FAQ)
            ->where('id', $id)
            ->firstOrFail();

        try {
            $data->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);
            return to_route('businessPricing.faq.index')->with('t-success', 'Updated successfully!');
        } catch (Exception $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    // Toggle status
    public function businessPricingFaqStatus(int $id): JsonResponse
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
    public function businessPricingFaqDestroy(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);

        if ($data->background_image && file_exists(public_path($data->background_image))) {
            unlink(public_path($data->background_image));
        }

        $data->delete();

        return response()->json([
            't-success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }
}
