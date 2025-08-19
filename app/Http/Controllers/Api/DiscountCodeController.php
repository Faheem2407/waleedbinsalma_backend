<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\OnlineStore;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DiscountCodeController extends Controller
{
    use ApiResponse;

    public function createDiscountCode(Request $request, $online_store_id)
    {
        $request->validate([
            'code' => 'required|string|min:3|max:50|unique:discount_codes,code',
            'discount_amount' => 'nullable|numeric|min:0|required_without:discount_percentage',
            'discount_percentage' => 'nullable|integer|min:1|max:100|required_without:discount_amount',
            'valid_from' => 'nullable|date|after_or_equal:today',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'minimum_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([], 'User not authenticated.', 401);
            }

            // Verify store ownership
            $onlineStore = OnlineStore::where('id', $online_store_id)
                ->whereHas('businessProfile', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->first();

            if (!$onlineStore) {
                return $this->error([], 'You are not authorized to create discount codes for this store.', 403);
            }

            // Ensure either discount_amount or discount_percentage is provided, not both
            if ($request->filled('discount_amount') && $request->filled('discount_percentage')) {
                return $this->error([], 'Please provide either a discount amount or percentage, not both.', 400);
            }

            $discountCode = DiscountCode::create([
                'online_store_id' => $online_store_id,
                'code' => $request->code,
                'discount_amount' => $request->discount_amount,
                'discount_percentage' => $request->discount_percentage,
                'valid_from' => $request->valid_from,
                'valid_until' => $request->valid_until,
                'usage_limit' => $request->usage_limit,
                'minimum_amount' => $request->minimum_amount,
                'is_active' => $request->input('is_active', true),
                'used_count' => 0,
            ]);

            return $this->success([
                'discount_code' => [
                    'id' => $discountCode->id,
                    'code' => $discountCode->code,
                    'discount_amount' => $discountCode->discount_amount,
                    'discount_percentage' => $discountCode->discount_percentage,
                    'valid_from' => $discountCode->valid_from,
                    'valid_until' => $discountCode->valid_until,
                    'usage_limit' => $discountCode->usage_limit,
                    'minimum_amount' => $discountCode->minimum_amount,
                    'is_active' => $discountCode->is_active,
                ]
            ], 'Discount code created successfully.', 201);

        } catch (\Exception $e) {
            Log::error('Failed to create discount code: ', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'online_store_id' => $online_store_id,
            ]);
            return $this->error($e->getMessage(), 'Failed to create discount code.', 500);
        }
    }
}