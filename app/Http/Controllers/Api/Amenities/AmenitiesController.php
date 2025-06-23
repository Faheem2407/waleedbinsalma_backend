<?php

namespace App\Http\Controllers\Api\Amenities;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AmenitiesController extends Controller
{
    use ApiResponse;

    public function index(){
        $amenities= Amenities::select(['id','icon','status','name'])
            ->orderBy('id','asc')
            ->get();
        if($amenities->isEmpty()){
            return $this->error([],'Amenities Not Found',404);
        }
        return $this->success($amenities,'Amenities Found',200);
    }
}
