<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductBrand;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ProductBrandController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $businessProfileId = auth()->user()->businessProfile->id;

        $brands = ProductBrand::with('businessProfile')
            ->where('business_profile_id', $businessProfileId)
            ->get();

        return $this->success($brands, 'Product brands fetched successfully.',200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_profile_id' => 'required|exists:business_profiles,id',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first() , 422);
        }

        $brand = ProductBrand::create($request->all());

        return $this->success($brand->load('businessProfile'), 'Product brand created successfully.',201);
    }


    public function show($id)
    {
        $brand = ProductBrand::with('businessProfile')->find($id);
        if (!$brand) {
            return $this->error([], 'Product brand not found', 404);
        }

        return $this->success($brand, 'Product brand fetched successfully.',200);
    }


    public function update(Request $request, $id)
    {
        $brand = ProductBrand::find($id);
        if (!$brand) {
            return $this->error([], 'Product brand not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $brand->update($request->all());

        return $this->success($brand->load('businessProfile'), 'Product brand updated successfully.',200);
    }

    public function destroy($id)
    {
        $brand = ProductBrand::find($id);
        if (!$brand) {
            return $this->error([], 'Product brand not found', 404);
        }

        $brand->delete();
        return $this->success([], 'Product brand deleted successfully.',200);
    }
}
