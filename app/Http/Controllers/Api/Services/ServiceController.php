<?php

namespace App\Http\Controllers\Api\Services;

use App\Models\Service;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    use ApiResponse;


    public function index(){
        $servicesTypes= Service::select(['id','icon','status','service_name'])->orderBy('id','asc')->get();

        if($servicesTypes->isEmpty()){
            return $this->error([],'Service Types Not Found',404);
        }

        return $this->success($servicesTypes,'Service Types Found',200);
    }
}
