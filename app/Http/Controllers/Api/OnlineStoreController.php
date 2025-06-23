<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OnlineStore;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class OnlineStoreController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->validate([
            'business_profile_id' => 'required|integer|exists:business_profiles,id',
            'name' => 'required|string',
            'about' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'day_name' => 'required|string',
            'morning_start_time' => 'required|string',
            'morning_end_time' => 'required|string',
            'evening_start_time' => 'required|string',
            'evening_end_time' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|max:2048',
            'amenities' => 'required|array|min:1',
            'amenities.*' => 'required|integer|exists:amenities,id',
            'highlights' => 'required|array|min:1',
            'highlights.*' => 'required|integer|exists:highlights,id',
            'values' => 'required|array|min:1',
            'values.*' => 'required|integer|exists:values,id',
            'teams' => 'nullable|array|min:1',
            'teams.*' => 'nullable|integer|exists:teams,id',
            'services' => 'required|array|min:1',
            'services.*' => 'required|integer|exists:services,id',
        ]);

        DB::beginTransaction();

        try {
            $store = OnlineStore::updateOrCreate(
                ['business_profile_id' => $request->business_profile_id],
                [
                    'name' => $request->name,
                    'about' => $request->about,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]
            );

            $store->openingHours()->updateOrCreate(
                ['day_name' => $request->day_name],
                [
                    'morning_start_time' => $request->morning_start_time,
                    'morning_end_time' => $request->morning_end_time,
                    'evening_start_time' => $request->evening_start_time,
                    'evening_end_time' => $request->evening_end_time,
                ]
            );

            if ($request->hasFile('images')) {
                foreach ($store->storeImages as $image) {
                    $imagePath = public_path($image->images);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $store->storeImages()->delete();

                $images = [];
                foreach ($request->file('images') as $file) {
                    $path = uploadImage($file, 'store_images');
                    $images[] = ['images' => $path];
                }
                $store->storeImages()->createMany($images);
            }




            $store->storeAmenities()->delete();
            foreach ($request->amenities as $amenity_id) {
                $store->storeAmenities()->create(['amenity_id' => $amenity_id]);
            }

            $store->storeHighlights()->delete();
            foreach ($request->highlights as $highlight_id) {
                $store->storeHighlights()->create(['highlight_id' => $highlight_id]);
            }

            $store->storeValues()->delete();
            foreach ($request->values as $value_id) {
                $store->storeValues()->create(['value_id' => $value_id]);
            }

            $store->storeTeams()->delete();
            if ($request->teams) {
                foreach ($request->teams as $team_id) {
                    $store->storeTeams()->create(['team_id' => $team_id]);
                }
            }

            $store->storeServices()->delete();
            foreach ($request->services as $service_id) {
                $store->storeServices()->create(['service_id' => $service_id]);
            }

            DB::commit();


            $store->load([
                'openingHours',
                'storeImages',
                'storeAmenities.amenity',
                'storeHighlights.highlight',
                'storeValues.value',
                'storeTeams.team',
                'storeServices.service',
            ]);

            return $this->success($store, 'Store updated successfully.', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'about' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'address' => 'sometimes|required|string',
            'latitude' => 'sometimes|required|string',
            'longitude' => 'sometimes|required|string',
            'day_name' => 'sometimes|required|string',
            'morning_start_time' => 'sometimes|required|string',
            'morning_end_time' => 'sometimes|required|string',
            'evening_start_time' => 'sometimes|required|string',
            'evening_end_time' => 'sometimes|required|string',
            'images' => 'sometimes|array|min:1',
            'images.*' => 'sometimes|image|max:2048',
            'amenities' => 'sometimes|array|min:1',
            'amenities.*' => 'required_with:amenities|integer|exists:amenities,id',
            'highlights' => 'sometimes|array|min:1',
            'highlights.*' => 'required_with:highlights|integer|exists:highlights,id',
            'values' => 'sometimes|array|min:1',
            'values.*' => 'required_with:values|integer|exists:values,id',
            'teams' => 'nullable|array|min:1',
            'teams.*' => 'nullable|integer|exists:teams,id',
            'services' => 'sometimes|array|min:1',
            'services.*' => 'required_with:services|integer|exists:services,id',
        ]);

        DB::beginTransaction();

        try {
            $store = OnlineStore::findOrFail($id);

            // Update only if present
            $store->update($request->only([
                'name',
                'about',
                'phone',
                'email',
                'address',
                'latitude',
                'longitude'
            ]));

            // Opening Hours
            if ($request->has(['day_name', 'morning_start_time', 'morning_end_time', 'evening_start_time', 'evening_end_time'])) {
                $store->openingHours()->updateOrCreate(
                    ['day_name' => $request->day_name],
                    [
                        'morning_start_time' => $request->morning_start_time,
                        'morning_end_time' => $request->morning_end_time,
                        'evening_start_time' => $request->evening_start_time,
                        'evening_end_time' => $request->evening_end_time,
                    ]
                );
            }


            // Images
            if ($request->hasFile('images')) {
                foreach ($store->storeImages as $image) {
                    $imagePath = public_path($image->images);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $store->storeImages()->delete();

                $images = [];
                foreach ($request->file('images') as $file) {
                    $path = uploadImage($file, 'store_images');
                    $images[] = ['images' => $path];
                }
                $store->storeImages()->createMany($images);
            }


            // Amenities
            if ($request->filled('amenities')) {
                $store->storeAmenities()->delete();
                foreach ($request->amenities as $amenity_id) {
                    $store->storeAmenities()->create(['amenity_id' => $amenity_id]);
                }
            }

            // Highlights
            if ($request->filled('highlights')) {
                $store->storeHighlights()->delete();
                foreach ($request->highlights as $highlight_id) {
                    $store->storeHighlights()->create(['highlight_id' => $highlight_id]);
                }
            }

            // Values
            if ($request->filled('values')) {
                $store->storeValues()->delete();
                foreach ($request->values as $value_id) {
                    $store->storeValues()->create(['value_id' => $value_id]);
                }
            }

            // Teams (nullable)
            if ($request->has('teams')) {
                $store->storeTeams()->delete();
                if (!empty($request->teams)) {
                    foreach ($request->teams as $team_id) {
                        $store->storeTeams()->create(['team_id' => $team_id]);
                    }
                }
            }

            // Services
            if ($request->filled('services')) {
                $store->storeServices()->delete();
                foreach ($request->services as $service_id) {
                    $store->storeServices()->create(['service_id' => $service_id]);
                }
            }

            DB::commit();

            $store->load([
                'openingHours',
                'storeImages',
                'storeAmenities.amenity',
                'storeHighlights.highlight',
                'storeValues.value',
                'storeTeams.team',
                'storeServices.service',
            ]);

            return $this->success($store, 'Store updated successfully.', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }


    public function getRegister(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|integer|exists:online_stores,id'
        ]);

        try {
            $store = OnlineStore::with([
                'openingHours:id,online_store_id,day_name,morning_start_time,morning_end_time,evening_start_time,evening_end_time',

                'storeImages:id,online_store_id,images',

                'storeAmenities:id,online_store_id,amenity_id',
                'storeAmenities.amenity:id,name',

                'storeHighlights:id,online_store_id,highlight_id',
                'storeHighlights.highlight:id,name',

                'storeValues:id,online_store_id,value_id',
                'storeValues.value:id,name',

                'storeTeams:id,online_store_id,team_id',
                'storeTeams.team:id,first_name',

                'storeServices:id,online_store_id,service_id',
                'storeServices.service:id,service_name'
            ])
                ->select([
                    'id',
                    'business_profile_id',
                    'name',
                    'about',
                    'phone',
                    'email',
                    'address',
                    'latitude',
                    'longitude'
                ])
                ->findOrFail($request->online_store_id);

            return $this->success([
                'store' => $store
            ], 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    public function showAllOnlineStores(Request $request)
    {
        try {
            $query = OnlineStore::with([
                'storeImages:id,online_store_id,images',
                'storeServices.service:id,service_name',
                'storeServices.service.catalogService:id,service_id,duration'
            ]);

            // if ($request->filled('address')) {
            //     $query->where('address', 'like', '%' . $request->address . '%');
            // }

            if ($request->filled('service_id')) {
                $query->whereHas('storeServices', function ($q) use ($request) {
                    $q->where('service_id', $request->service_id);
                });
            }

            if ($request->filled(['latitude', 'longitude'])) {
                $latitude = $request->latitude;
                $longitude = $request->longitude;
                $radius = $request->radius ?? 1000;

                $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude))))";

                $query->whereRaw("$haversine <= ?", [$radius]);
            }

            $stores = $query->paginate(4);

            return $this->success($stores, 'Stores fetched successfully.', 200);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function showOnlineStoreDetails($id)
    {
        try {
            $store = OnlineStore::with([
                'openingHours:id,online_store_id,day_name,morning_start_time,morning_end_time,evening_start_time,evening_end_time',
                'storeImages:id,online_store_id,images',
                'storeAmenities.amenity',
                'storeHighlights.highlight',
                'storeValues.value',
                'storeTeams.team',
                'storeServices.catalogService'
            ])->find($id);

            if (!$store) {
                return $this->error([], 'Store not found.', 404);
            }

            $products = Product::where('business_profile_id', $store->business_profile_id)->get();

            $store->products = $products;

            $latitude = $store->latitude;
            $longitude = $store->longitude;
            $radius = 10;

            $nearbyStores = OnlineStore::select('id', 'business_profile_id', 'name', 'address', 'latitude', 'longitude')
                ->where('id', '!=', $store->id)
                ->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) * sin(radians(latitude)))) < ?',
                    [$latitude, $longitude, $latitude, $radius]
                )
                ->get();

            $store->nearby_stores = $nearbyStores;

            return $this->success($store, 'Store details fetched successfully.', 200);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function viewProduct($id)
    {
        try {
            $product = Product::with('category:id,name')
                ->find($id);

            if (!$product) {
                return $this->error([], 'Product not found.', 404);
            }


            $otherProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(10)
                ->get();

            $product->other_products = $otherProducts;

            return $this->success($product, 'Product details fetched successfully.', 200);

        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



}






