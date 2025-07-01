<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatalogServiceCategory;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class CatalogServiceCategoryController extends Controller
{
    use ApiResponse;

	public function catalogServiceCategoriesCount(Request $request)
	{
	    $teamId = $request->team_member;
	    $catalog_service_category_Id = $request->catalog_service_category_id;
	    $searchQuery = $request->input('query');

	    $businessProfileId = auth()->user()->businessProfile->id;

	    $categories = CatalogServiceCategory::where('business_profile_id', $businessProfileId)
	        ->withCount([
	            'catalogServices as filtered_services_count' => function ($query) use ($teamId, $searchQuery) {
	                $query->when($teamId, function ($q) use ($teamId) {
	                    $q->whereHas('teams', function ($q2) use ($teamId) {
	                        $q2->where('teams.id', $teamId);
	                    });
	                })
	                ->when($searchQuery, function ($q) use ($searchQuery) {
	                    $q->where(function ($q2) use ($searchQuery) {
	                        $q2->where('name', 'like', "%{$searchQuery}%")
	                            ->orWhere('description', 'like', "%{$searchQuery}%");
	                    });
	                });
	            }
	        ])
	        ->with([
	            'catalogServices' => function ($query) use ($teamId, $searchQuery) {
	                $query->select('id', 'name', 'duration', 'price', 'catalog_service_category_id')
	                    ->when($teamId, function ($q) use ($teamId) {
	                        $q->whereHas('teams', function ($q2) use ($teamId) {
	                            $q2->where('teams.id', $teamId);
	                        });
	                    })
	                    ->when($searchQuery, function ($q) use ($searchQuery) {
	                        $q->where(function ($q2) use ($searchQuery) {
	                            $q2->where('name', 'like', "%{$searchQuery}%")
	                                ->orWhere('description', 'like', "%{$searchQuery}%");
	                        });
	                    });
	            }
	        ])
	        ->when($catalog_service_category_Id, function ($query) use ($catalog_service_category_Id) {
	            $query->where('id', $catalog_service_category_Id);
	        })
	        ->get();

	    if ($searchQuery || $teamId) {
	        $categories = $categories->filter(function ($category) {
	            return $category->filtered_services_count > 0;
	        })->values();
	    }

	    if ($categories->isEmpty()) {
	        return $this->error([], 'No catalog services found.', 404);
	    }

	    return $this->success($categories, 'Catalog Service Categories fetch Successful!', 200);
	}



    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_profile_id' => 'required|exists:business_profiles,id',
            'name' => 'required|string',
            'description' => 'required|string|max:320',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category = CatalogServiceCategory::create([
            'business_profile_id' => $request->business_profile_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        if (!$category) {
            return $this->error([], 'Catalog Service Category not creation failed', 500);
        }

        return $this->success($category, 'Catalog Service Category created successfully!', 201);
    }

    public function editCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string|max:320',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $category = CatalogServiceCategory::find($id);

        if (!$category) {
            return $this->error([], 'Catalog Service Category not found', 404);
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return $this->success($category, 'Catalog Service Category updated successfully!', 200);
    }

    public function showCategory($id)
	{
	    $category = CatalogServiceCategory::find($id);

	    if (!$category) {
	        return $this->error([], 'Catalog Service Category not found', 404);
	    }

	    return $this->success($category, 'Catalog Service Category details retrieved successfully!', 200);
	}


    public function deleteCategory($id)
	{
	    $category = CatalogServiceCategory::find($id);

	    if (!$category) {
	        return $this->error([], 'Catalog Service Category not found', 404);
	    }

	    if (!$category->delete()) {
	        return $this->error([], 'Failed to delete Catalog Service Category', 500);
	    }

	    return $this->success([], 'Catalog Service Category deleted successfully!', 200);
	}


}
