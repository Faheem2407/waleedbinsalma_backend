<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponse;

class CustomerDashboardController extends Controller
{
    use ApiResponse;

    public function showProfile()
    {
        $user = auth()->user()->load('addresses');
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        return $this->success($user, 'User data fetched successfully', 200);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $validator = validator($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'number' => 'nullable|string',
            'country' => 'nullable|string',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = uploadImage($image, 'User/Avatar');
            $user->avatar = $imageName;
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->country = $request->country;
        $user->save();

        return $this->success($user, 'User data updated successfully', 200);
    }

    public function addAddress(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        $user->addresses()->create([
            'user_id' => $user->id,
            'address' => $request->address
        ]);
        $data = $user->addresses()->where('user_id', $user->id)->get();
        return $this->success($data, 'Address added successfully', 200);
    }

    public function editAddress(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        $user->addresses()->where('id', $request->id)->update([
            'address' => $request->address
        ]);
        $data = $user->addresses()->where('user_id', $user->id)->get();
        return $this->success($data, 'Address updated successfully', 200);
    }

    public function deleteAddress(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        $user->addresses()->where('id', $request->id)->delete();
        $data = $user->addresses()->where('user_id', $user->id)->get();
        return $this->success($data, 'Address deleted successfully', 200);
    }

    public function myFavorites()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        
        $data = $user->favorites()->where('user_id', $user->id)->get();

        return $this->success($data, 'My Favorites', 200);
    }

    public function myAppointments()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        $data = $user->appointments()->where('user_id', $user->id)->get();
        return $this->success($data, 'My Appointments', 200);
    }

    public function myProducts()
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }
        $data = $user->products()->with('items')->where('user_id', $user->id)->get();
        return $this->success($data, 'My Products', 200);
    }
}
