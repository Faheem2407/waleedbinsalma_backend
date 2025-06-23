<?php

namespace App\Http\Controllers\Api\values;

use App\Http\Controllers\Controller;
use App\Models\Values;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ValuesController extends Controller
{
    use ApiResponse;

     public function index(){
        $values= Values::select(['id','status','name'])
            ->orderBy('id','asc')
            ->get();
        if($values->isEmpty()){
            return $this->error([],'values Not Found',404);
        }
        return $this->success($values,'values Found',200);
    }

}
