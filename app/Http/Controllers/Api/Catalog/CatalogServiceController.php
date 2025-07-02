<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\CatalogService;
use Illuminate\Support\Facades\Validator;


class CatalogServiceController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $businessProfileId = auth()->user()->businessProfile->id;

        $services = CatalogService::with(['category', 'businessProfile', 'service'])
            ->where('business_profile_id', $businessProfileId)
            ->get();

        if($services->isEmpty()){
            return $this->error([],'Catalog Services not found',404);
        }

        return $this->success($services, 'Catalog Services fetched successfully', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'catalog_service_category_id' => 'required|exists:catalog_service_categories,id',
            'business_profile_id' => 'required|exists:business_profiles,id',
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|string',
            'price_type' => 'required|string',
            'price' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first() , 422);
        }

        $service = CatalogService::create($request->only([
            'catalog_service_category_id',
            'business_profile_id',
            'service_id',
            'name',
            'description',
            'duration',
            'price_type',
            'price',
        ]));

        return $this->success($service, 'Catalog Service created successfully!', 201);
    }


    public function update(Request $request, $id)
    {
        $service = CatalogService::find($id);

        if (!$service) {
            return $this->error([], 'Catalog Service not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'catalog_service_category_id' => 'sometimes|exists:catalog_service_categories,id',
            'business_profile_id' => 'sometimes|exists:business_profiles,id',
            'service_id' => 'sometimes|exists:services,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration' => 'sometimes|string',
            'price_type' => 'sometimes|string',
            'price' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first() , 422);
        }

        $service->update($request->only([
            'catalog_service_category_id',
            'business_profile_id',
            'service_id',
            'name',
            'description',
            'duration',
            'price_type',
            'price',
        ]));

        return $this->success($service, 'Catalog Service updated successfully!', 200);
    }

    public function show($id)
    {
        $service = CatalogService::with(['category', 'businessProfile', 'service'])->find($id);

        if (!$service) {
            return $this->error([], 'Catalog Service not found', 404);
        }

        return $this->success($service, 'Catalog Service fetched successfully', 200);
    }

    public function destroy($id)
    {
        $service = CatalogService::find($id);

        if (!$service) {
            return $this->error([], 'Catalog Service not found', 404);
        }

        $service->delete();

        return $this->success([], 'Catalog Service deleted successfully', 200);
    }

    public function updateTeamMembers(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $catalogService = CatalogService::find($id);

        if (!$catalogService) {
            return $this->error([], 'Catalog Service not found', 404);
        }

        $catalogService->teams()->sync($request->team_ids);

        return $this->success([], 'Catalog Service team members updated successfully', 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $services = CatalogService::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();

        if ($services->isEmpty()) {
            return $this->error([], 'No catalog services found.', 404);
        }

        return $this->success($services, 'Catalog services search result.', 200);
    }

    public function filter(Request $request)
    {
        $teamId = $request->team_member;
        $catalog_service_category_Id = $request->catalog_service_category_id;

        $services = CatalogService::with(['teams', 'service'])
            ->when($teamId, function ($query) use ($teamId) {
                $query->whereHas('teams', function ($q) use ($teamId) {
                    $q->where('teams.id', $teamId);
                });
            })
            ->when($catalog_service_category_Id, function ($query) use ($catalog_service_category_Id) {
                $query->where('catalog_service_category_id', $catalog_service_category_Id);
            })->get();

        if ($services->isEmpty()) {
            return $this->error([], 'No catalog services found.', 404);
        }

        return $this->success($services, 'Catalog services filtered successfully.',200);
    }
}
