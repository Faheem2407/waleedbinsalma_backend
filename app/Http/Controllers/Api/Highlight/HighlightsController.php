<?php

namespace App\Http\Controllers\Api\Highlight;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Highlights;

class HighlightsController extends Controller
{
    use ApiResponse;

    public function index(){
        $highlights= Highlights::select(['id','icon','status','name'])
            ->orderBy('id','asc')
            ->get();
        if($highlights->isEmpty()){
            return $this->error([],'Highlights Not Found',404);
        }
        return $this->success($highlights,'Highlights Found',200);
    }
}
