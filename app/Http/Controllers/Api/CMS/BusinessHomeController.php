<?php

namespace App\Http\Controllers\Api\CMS;

use App\Enum\Page;
use App\Enum\Section;
use App\Models\CMS;
use App\Models\ClientReview;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class BusinessHomeController extends Controller
{
    use ApiResponse;

    public function businessHome(Request $request)
    {
        $page = Page::BUSINESS_HOME;

        $banner = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_BANNER)->first();
        $stats = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_STATS)->first();
        $growTitle = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS_SECTION_TITLE)->first();
        $growItems = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_GROW_YOUR_BUSINESS)->get();
        $stayConnected = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_STAY_CONNECTED)->get();
        $getStarted = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_GET_STARTED)->first();
        $interested = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_INTERESTED)->first();
        $clientSays = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY)->first();
        $clientReview = ClientReview::get();

        $data = [
            'banner' => $banner ? [
                'title' => $banner->title,
                'sub_title' => $banner->sub_title,
                'button_text' => $banner->button_text,
                'button_link' => $banner->button_link,
                'image' => $banner->background_image ? $banner->background_image : null,
            ] : null,

            'stats' => $stats ? [
                'title' => $stats->title,
                'sub_title' => $stats->sub_title,
                'satisfied_clients'=>$stats->satisfied_clients,
                'pro_consultants'=> $stats->pro_consultants,
                'years_in_businesses'=>$stats->years_in_businesses,
                'successful_cases'=> $stats->successful_cases
            ] : null,

            'grow_business' => [
                'title' => $growTitle ? $growTitle->title : null,
                'sub_title'=> $growTitle ? $growTitle->sub_title : null,
                'items' => $growItems->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'description' => $item->description,
                        'icon' => $item->icon ? $item->icon : null,
                    ];
                }),
            ],

            'stay_connected' => $stayConnected->isNotEmpty() ? [
                'items' => $stayConnected->map(function ($item) {
                    return [
                        'link_text' => $item->button_text,
                        'link_url' => $item->button_link,
                        'title' => $item->title,
                        'description' => $item->description,
                        'icon' => $item->icon ? $item->icon : null,
                    ];
                }),
            ] : null,

            'get_started' => $getStarted ? [
                'title' => $getStarted->title,
                'sub_title' => $getStarted->sub_title,
            ] : null,

            'interested' => $interested ? [
                'title' => $interested->title,
                'sub_title' => $interested->sub_title,
                'button_text' => $interested->button_text,
                'button_link' => $interested->button_link,
            ] : null,

            'client_says' => $clientSays ? [
                'title' => $clientSays->title,
                'background_image' => $clientSays->background_image ? $clientSays->background_image : null,
                'client_reviews' => $clientReview,
            ] : null,
        ];

        if (empty(array_filter($data, fn($value) => !is_null($value) && (!is_countable($value) || count($value) > 0)))) {
            return $this->error('No data found', 404);
        }

        return $this->success((object)$data, 'Business home page data retrieved successfully', 200);
    }
}
