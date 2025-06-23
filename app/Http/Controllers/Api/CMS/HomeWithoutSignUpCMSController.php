<?php

namespace App\Http\Controllers\Api\CMS;

use App\Enum\Page;
use App\Enum\Section;
use App\Models\CMS;
use App\Models\ClientReview;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;

class HomeWithoutSignUpCMSController extends Controller
{
    use ApiResponse;

    public function businessHomeWithoutSignUp(Request $request)
    {
        $page = Page::BUSINESS_HOME;

        $banner = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_BANNER)->first();
        $stats = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_STATS)->first();

        
        $clientSays = CMS::where('page', $page)->where('section', Section::BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY)->first();
        $clientReview = ClientReview::get();

        $data = [
            'client_reviews' => $clientReview ?? null,

            'stats' => $stats ? [
                'title' => $stats->title,
                'sub_title' => $stats->sub_title,
                'satisfied_clients'=>$stats->satisfied_clients,
                'pro_consultants'=> $stats->pro_consultants,
                'years_in_businesses'=>$stats->years_in_businesses,
                'successful_cases'=> $stats->successful_cases
            ] : null,
            'banner' => $banner ? [
                'title' => $banner->title,
                'sub_title' => $banner->sub_title,
                'button_text' => $banner->button_text,
                'button_link' => $banner->button_link,
                'image' => $banner->background_image ? $banner->background_image : null,
            ] : null,

            
        ];

        if (empty(array_filter($data, fn($value) => !is_null($value) && (!is_countable($value) || count($value) > 0)))) {
            return $this->error('No data found', 404);
        }

        return $this->success((object)$data, 'Business home page without signup data retrieved successfully', 200);
    }
}
