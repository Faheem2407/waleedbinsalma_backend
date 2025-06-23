<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessDocument;
use App\Models\BusinessProfile;
use App\Models\BusinessService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessProfileController extends Controller
{
    use ApiResponse;

    public function store(Request $request){
        DB::beginTransaction();
        try{
            $rules = [
                'business_name' => 'required|string',
                'team_size' => 'required|integer',
                'website_url' => 'required|string',
                'longitude' => 'required|string',
                'latitude' => 'required|string',
                'calendly' => 'required|string',
                'trade_license' => 'required|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx|max:5000',
                'vat_registration_certificate' => 'required|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx|max:5000',
                'passport_copy' => 'required|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx|max:5000',
                'account_statement' => 'required|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx|max:5000',
                'service_id' => 'required|array',
                'terms_and_condition' => 'required|boolean',
            ];

            // Add conditional rule
            if ($request->do_not_business_adders == false) {
                $rules['address'] = 'required|string';
            } else {
                $rules['address'] = 'nullable|string';
            }

            if(count($request->service_id) > 3 ){
                return $this->error([],'You can not add more than three services.',422);
            }

            // Now validate once
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }



            $user_id=auth()->user()->id;

            $businessProfile=BusinessProfile::updateOrCreate(['user_id'=>$user_id],[
                'business_name'=>$request->business_name,
                'address'=>$request->address,
                'team_size'=>$request->team_size,
                'website_url'=>$request->website_url,
                'longitude'=>$request->longitude,
                'latitude'=>$request->latitude,
                'calendly'=>$request->calendly,
                'do_not_business_adders'=>$request->do_not_business_adders,
            ]);


            $trade_license_name = null;
            if ($request->hasFile('trade_license')) {
                    $trade_license_name = uploadImage($request->file('trade_license'), 'business_document');
            }

            $vat_registration_certificate_name = null;
            if ($request->hasFile('vat_registration_certificate')) {
                    $vat_registration_certificate_name = uploadImage($request->file('vat_registration_certificate'), 'business_document');
            }

            $passport_copy_name = null;
            if ($request->hasFile('passport_copy')) {
                    $passport_copy_name = uploadImage($request->file('passport_copy'), 'business_document');
            }

            $account_statement_name = null;
            if ($request->hasFile('account_statement')) {
                    $account_statement_name = uploadImage($request->file('account_statement'), 'business_document');
            }

            $businessDocument = BusinessDocument::updateOrCreate(['business_profile_id'=>$businessProfile->id],[
                'business_profile_id'=>$businessProfile->id,
                'trade_license'=>$trade_license_name,
                'vat_registration_certificate'=>$vat_registration_certificate_name,
                'passport_copy'=>$passport_copy_name,
                'account_statement'=>$account_statement_name,
                'terms_and_condition'=>$request->terms_and_condition,
            ]);

            foreach($request->service_id as  $value){
                BusinessService::create([
                    'service_id' => $value,
                    'business_profile_id' =>$businessProfile->id,
                ]);
            }

            $businessProfile->load(['businessDocument','businessServices']);


            DB::commit();



            return $this->success($businessProfile,'Business Profile Created.',200);

        }catch(Exception $e){
            DB::rollBack();
            return $this->error([],$e->getMessage(),422);
        }
    }

}
