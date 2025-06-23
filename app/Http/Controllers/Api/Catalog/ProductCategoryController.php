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

    // GET /product-categories
    public function index()
    {
        $categories = ProductCategory::with('businessProfile')->get();
        return $this->success($categories, 'Product categories fetched successfully.');
    }

    // POST /product-categories
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_profile_id' => 'required|exists:business_profiles,id',
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category = ProductCategory::create($request->all());

        return $this->success($category->load('businessProfile'), 'Product category created successfully.');
    }

    // GET /product-categories/{id}
    public function show($id)
    {
        $category = ProductCategory::with('businessProfile')->find($id);
        if (!$category) {
            return $this->error([], 'Product category not found', 404);
        }

        return $this->success($category, 'Product category fetched successfully.');
    }

    // PUT /product-categories/{id}
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
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category->update($request->all());

        return $this->success($category->load('businessProfile'), 'Product category updated successfully.');
    }

    // DELETE /product-categories/{id}
    public function destroy($id)
    {
        $category = ProductCategory::find($id);
        if (!$category) {
            return $this->error([], 'Product category not found', 404);
        }

        $category->delete();
        return $this->success([], 'Product category deleted successfully.');
    }
}
