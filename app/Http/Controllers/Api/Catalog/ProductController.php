<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $businessProfileId = auth()->user()->businessProfile->id;

        $query = Product::with(['businessProfile', 'category', 'brand'])
            ->where('business_profile_id', $businessProfileId); 
            
        // filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 5);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock_quantity', '<=', 5);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Add search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('short_description', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->get();

        return $this->success($products, 'Products fetched successfully.',200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_profile_id' => 'required|exists:business_profiles,id',
            'category_id' => 'required|exists:product_categories,id',
            'brand_id' => 'required|exists:product_brands,id',
            'name' => 'required|string',
            'barcode' => 'nullable|string',
            'measure' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'supply_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image_url' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(),$validator->errors()->first(), 422);
        }

        $data = $request->except('image_url');

        $image_path = null;
        if ($request->hasFile('image_url')) {
            $image_path = uploadImage($request->file('image_url'), 'product_images');
        }
        $data['image_url'] = $image_path;


        $product = Product::create($data);

        return $this->success($product->load(['businessProfile', 'category', 'brand']), 'Product created successfully.',201);
    }


    public function show($id)
    {
        $product = Product::with(['businessProfile', 'category', 'brand'])->find($id);
        if (!$product) {
            return $this->error([], 'Product not found', 404);
        }

        return $this->success($product, 'Product fetched successfully.',200);
    }


    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->error([], 'Product not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|exists:product_categories,id',
            'brand_id' => 'sometimes|required|exists:product_brands,id',
            'name' => 'sometimes|required|string',
            'barcode' => 'nullable|string',
            'measure' => 'sometimes|required|string',
            'amount' => 'sometimes|required|integer|min:0',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'supply_price' => 'sometimes|required|numeric|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'image_url' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first() , 422);
        }

        $data = $request->except('image_url');

        $image_path = null;
        if ($request->hasFile('image_url')) {
            $image_path = uploadImage($request->file('image_url'), 'product_images');
            $data['image_url'] = $image_path;
        }


        $product->update($data);

        return $this->success($product->load(['businessProfile', 'category', 'brand']), 'Product updated successfully.',200);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->error([], 'Product not found', 404);
        }

        $product->delete();
        return $this->success([], 'Product deleted successfully.',200);
    }
}
