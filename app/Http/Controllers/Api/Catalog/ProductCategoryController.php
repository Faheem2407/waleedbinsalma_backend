<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $businessProfileId = auth()->user()->businessProfile->id;

        $categories = ProductCategory::with('businessProfile')
            ->where('business_profile_id', $businessProfileId)
            ->get();

        return $this->success($categories, 'Product categories fetched successfully.',200);
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

        $category = ProductCategory::create($request->all());

        return $this->success($category->load('businessProfile'), 'Product category created successfully.',200);
    }


    public function show($id)
    {
        $category = ProductCategory::with('businessProfile')->find($id);
        if (!$category) {
            return $this->error([], 'Product category not found', 404);
        }

        return $this->success($category, 'Product category fetched successfully.',200);
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return $this->error([], 'Product category not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first() , 422);
        }

        $category->update($request->all());

        return $this->success($category->load('businessProfile'), 'Product category updated successfully.',200);
    }

    public function destroy($id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return $this->error([], 'Product category not found', 404);
        }

        $category->delete();
        return $this->success([], 'Product category deleted successfully.',200);
    }
}
