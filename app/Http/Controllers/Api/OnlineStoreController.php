<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OnlineStore;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Appointment;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\RecentlyViewedStore;


class OnlineStoreController extends Controller
{
    use ApiResponse;


    public function createOrUpdate(Request $request, $id = null)
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

            'day_name' => 'required|array|min:1',
            'day_name.*' => 'required|string',
            'morning_start_time' => 'required|string',
            'morning_end_time' => 'required|string',
            'evening_start_time' => 'required|string',
            'evening_end_time' => 'required|string',

            'images' => 'nullable|array|min:1',
            'images.*' => 'image|max:2048',

            'amenities' => 'required|array|min:1',
            'amenities.*' => 'required|integer|exists:amenities,id',

            'highlights' => 'required|array|min:1',
            'highlights.*' => 'required|integer|exists:highlights,id',

            'values' => 'required|array|min:1',
            'values.*' => 'required|integer|exists:values,id',

            'teams' => 'nullable|array',
            'teams.*' => 'nullable|integer|exists:teams,id',

            'services' => 'required|array|min:1',
            'services.*' => 'required|integer|exists:services,id',
        ]);

        DB::beginTransaction();

        try {
            $store = $id
                ? OnlineStore::findOrFail($id)
                : OnlineStore::updateOrCreate(
                    ['business_profile_id' => $request->business_profile_id],
                    $request->only(['name', 'about', 'phone', 'email', 'address', 'latitude', 'longitude'])
                );

            if ($id) {
                $store->update($request->only(['name', 'about', 'phone', 'email', 'address', 'latitude', 'longitude']));
            }

            // Opening Hours
            $store->openingHours()->delete();
            foreach ($request->day_name as $day) {
                $store->openingHours()->create([
                    'day_name' => $day,
                    'morning_start_time' => $request->morning_start_time,
                    'morning_end_time' => $request->morning_end_time,
                    'evening_start_time' => $request->evening_start_time,
                    'evening_end_time' => $request->evening_end_time,
                ]);
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
            $store->storeAmenities()->delete();
            foreach ($request->amenities as $amenity_id) {
                $store->storeAmenities()->create(['amenity_id' => $amenity_id]);
            }

            // Highlights
            $store->storeHighlights()->delete();
            foreach ($request->highlights as $highlight_id) {
                $store->storeHighlights()->create(['highlight_id' => $highlight_id]);
            }

            // Values
            $store->storeValues()->delete();
            foreach ($request->values as $value_id) {
                $store->storeValues()->create(['value_id' => $value_id]);
            }

            // Teams
            $store->storeTeams()->delete();
            if ($request->filled('teams')) {
                foreach ($request->teams as $team_id) {
                    $store->storeTeams()->create(['team_id' => $team_id]);
                }
            }

            // Services
            $store->storeServices()->delete();
            foreach ($request->services as $service_id) {
                $store->storeServices()->create(['catalog_service_id' => $service_id]);
            }

            DB::commit();

            $store->load([
                'openingHours',
                'storeImages',
                'storeAmenities.amenity',
                'storeHighlights.highlight',
                'storeValues.value',
                'storeTeams.team',
                'storeServices.catalogService',
            ]);

            $message = $id ? 'Store updated successfully.' : 'Store created successfully.';
            return $this->success($store, $message, 200);
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
                'storeAmenities.amenity',

                'storeHighlights:id,online_store_id,highlight_id',
                'storeHighlights.highlight',

                'storeValues:id,online_store_id,value_id',
                'storeValues.value',

                'storeTeams:id,online_store_id,team_id',
                'storeTeams.team',

                'storeServices:id,online_store_id,catalog_service_id',
                'storeServices.catalogService'
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
                'storeServices.catalogService',
            ]);

            if ($request->filled('service_id')) {
                $query->whereHas('storeServices', function ($q) use ($request) {
                    $q->where('catalog_service_id', $request->service_id);
                });
            }

            if ($request->filled(['latitude', 'longitude'])) {
                $latitude = $request->latitude;
                $longitude = $request->longitude;
                $radius = $request->radius ?? 1000;

                $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude))))";

                $query->whereRaw("$haversine <= ?", [$radius]);
            }

            $stores = $query->latest()->paginate(4);

            return $this->success($stores, 'Stores fetched successfully.', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function showOnlineStoreDetails($id)
    {
        try {
            $store = OnlineStore::with([
                'businessProfile:id,user_id',
                'businessProfile.user:id,first_name,last_name,avatar',
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
                ->with('storeImages')
                ->where('id', '!=', $store->id)
                ->whereRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
                + sin(radians(?)) * sin(radians(latitude)))) < ?',
                    [$latitude, $longitude, $latitude, $radius]
                )
                ->get();

            $store->nearby_stores = $nearbyStores;

            // recently viewed logic
            if(auth()->user()) {
                $recentlyView = RecentlyViewedStore::createOrUpdate([
                    'user_id' => auth()->user()->id,
                    'online_store_id' => $store->id
                ]);
            }

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


    public function getOnlineStoreIdByBusinessProfile($businessProfileId)
    {
        try {
            $store = OnlineStore::select('id')
                ->where('business_profile_id', $businessProfileId)
                ->first();

            if (!$store) {
                return $this->error([], 'Online store not found for this business profile.', 404);
            }

            return $this->success(['online_store_id' => $store->id], 'Online store ID fetched successfully.', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function showTrendingStores(Request $request)
    {
        try {
            $query = OnlineStore::withCount('reviews')
                ->with([
                    'storeImages:id,online_store_id,images',
                    'storeServices.catalogService',
                ])
                ->orderByDesc('reviews_count'); // Sort by review count

            if ($request->filled('service_id')) {
                $query->whereHas('storeServices', function ($q) use ($request) {
                    $q->where('catalog_service_id', $request->service_id);
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

            return $this->success($stores, 'Trending stores fetched successfully.', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function myBookingStores(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->success([], 'Guest user – no booking-based stores available.', 200);
        }

        $storeIds = Appointment::where('user_id', $user->id)
            ->pluck('online_store_id')
            ->unique();

        if ($storeIds->isEmpty()) {
            return $this->success([], 'No previous bookings found.', 200);
        }

        $stores = OnlineStore::with([
                'storeImages:id,online_store_id,images',
                'storeServices.catalogService'
            ])
            ->whereIn('id', $storeIds)
            ->latest()
            ->get();

        return $this->success($stores, 'Stores based on your booking history fetched successfully.', 200);
    }


    public function recentlyViewedStores(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->success(null, 'No recently viewed stores for guests.', 200);
        }

        $stores = $user->recentlyViewedStores()
            ->with(['storeImages', 'storeServices.catalogService'])
            ->take(10)
            ->get();

        return $this->success($stores, 'Recently viewed stores fetched successfully.', 200);
    }



    public function recentlyViewedStoresGuest()
    {
        $storeIds = session()->get('recently_viewed_stores', []);

        if (empty($storeIds)) {
            return $this->success([], 'No recently viewed stores found for guests.', 200);
        }

        $stores = OnlineStore::with(['storeImages', 'storeServices.catalogService'])
            ->whereIn('id', $storeIds)
            ->get();

        return $this->success($stores, 'Recently viewed stores fetched successfully.', 200);
    }

}
