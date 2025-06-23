<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BusinessPricingController extends Controller
{
    use ApiResponse;
    public function businessPricing(Request $request)
    {
        $page = Page::BUSINESS_PRICING;

        $banner = CMS::where('page', $page)->where('section', Section::BUSINESS_PRICING_BANNER)->first();
        $sectionTitle = CMS::where('page', $page)->where('section', Section::BUSINESS_PRICING_SECTION_TITLE)->first();
        $sectionDescription = CMS::where('page', $page)->where('section', Section::BUSINESS_PRICING_SECTION_DESCRIPTION)->first();
        $descriptionItems = CMS::where('page', $page)->where('section', Section::BUSINESS_PRICING_DESCRIPITON)->get();
        $faqItems = CMS::where('page', $page)->where('section', Section::BUSINESS_PRICING_FAQ)->get();

        $data = [
            'banner' => $banner ? [
                'title' => $banner->title,
                'sub_title' => $banner->sub_title,
                'button_text' => $banner->button_text,
                'button_link' => $banner->button_link,
                'image' => $banner->background_image ? $banner->background_image : null,
            ] : null,

            'section_title' => $sectionTitle ? [
                'title' => $sectionTitle->title,
                'sub_title' => $sectionTitle->sub_title,
            ] : null,

            'section_description' => $sectionDescription ? [
                'title' => $sectionDescription->title,
                'sub_title' => $sectionDescription->sub_title,
            ] : null,

            'description_items' => $descriptionItems->isNotEmpty() ? $descriptionItems->map(function ($item) {
                return [
                    'title' => $item->title,
                    'sub_title' => $item->sub_title,
                ];
            }) : null,

            'faqs' => $faqItems->isNotEmpty() ? $faqItems->map(function ($item) {
                return [
                    'question' => $item->title,
                    'answer' => $item->description,
                ];
            }) : null,
        ];

        if (empty(array_filter($data, fn($value) => !is_null($value) && (!is_countable($value) || count($value) > 0)))) {
            return $this->error('No data found', 404);
        }

        return $this->success((object) $data, 'Business pricing page data retrieved successfully', 200);
    }

    public function searchFaq(Request $request)
    {
        $searchTerm = trim($request->query('search'));

        if (!$searchTerm) {
            return $this->error('Search term is required', 422);
        }

        $results = CMS::query()
            ->where('page', Page::BUSINESS_PRICING)
            ->where('section', Section::BUSINESS_PRICING_FAQ)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->get();

        $data = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'question' => $item->title,
                'answer' => $item->description,
            ];
        });

        return $this->success($data, 'FAQ search results retrieved successfully', 200);
    }


}
