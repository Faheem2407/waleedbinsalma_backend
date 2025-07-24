<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessBankDetails;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    use ApiResponse;

    public function socialLogin(Request $request)
    {
        // Validate the incoming request to ensure all necessary data is provided
        $request->validate([
            'token' => 'required|string',
            'provider' => 'required|in:google,facebook',
            'username' => 'required|string',
            'email' => 'nullable|email',
            'avatar' => 'nullable|url',
        ]);

        // Check if the user already exists in the database
        $user = User::where('email', $request->email)->first();

        // Initialize the path for storing the avatar
        $avatarPath = null;

        if ($request->avatar) {
            try {
                // Get image content from remote URL
                $response = Http::get($request->avatar);

                if ($response->successful()) {
                    $avatarContents = $response->body();

                    $imageName = Str::slug(time()) . '.jpg';
                    $folder = 'avatars';
                    $path = public_path('uploads/' . $folder);

                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }

                    file_put_contents($path . '/' . $imageName, $avatarContents);

                    $avatarPath = 'uploads/' . $folder . '/' . $imageName;
                } else {
                    return $this->error(['error' => 'Failed to download avatar.', 'message' => 'Invalid response'], 'Something went wrong', 500);
                }
            } catch (Exception $e) {
                return $this->error(['error' => 'Failed to download avatar.', 'message' => $e->getMessage()], 'Something went wrong', 500);
            }
        }

        if (!$user) {
            // If user does not exist, create a new user including the avatar
            $user = User::create([
                'first_name'           => $request->username,
                'email'          => $request->email,
                'avatar'         => $request->avatar, // Save avatar URL
                'provider'       => $request->provider,
                'password'       => bcrypt(Str::random(16)), // Generate a random password
                'role'         => $request->role,
                'agree_to_terms' => false,
            ]);
        } else {
            // Update user information if necessary (e.g., name, avatar)
            $user->update([
                'first_name'   => $request->username,
                'avatar' =>  $avatarPath ? $avatarPath : $user->avatar, // Update avatar URL if provided
            ]);
        }

        if ($user->role == 'business') {
            if ($user->businessProfile == null) {
                $user->setAttribute('flag', false);
            } else {
                $user->setAttribute('flag', true);
                $bankDetailsExist = BusinessBankDetails::where('business_profile_id', $user->businessProfile->id)->exists();

                if ($bankDetailsExist) {
                    // Bank details exist
                    $user->setAttribute('bank_connected', true);
                } else {
                    // Bank details do not exist
                    $user->setAttribute('bank_connected', false);
                }
            }
        }

        // Generate JWT token for the existing or newly created user
        $token = JWTAuth::fromUser($user);

        // Prepare response data
        $responseData = [              
            'id'       => $user->id,
            'name'     => $user->first_name,
            'email'    => $user->email,
            'avatar'   => $user->avatar,
            'provider' => $user->provider,
            'role'     => $user->role,
            'agree_to_terms' => $user->agree_to_terms,
            'flag'     => $user->flag ?? false,
            'bank_connected' => $user->bank_connected ?? false,
            'token'    => $token,
        ];

        return $this->success($responseData, 'User authenticated successfully', 200);
    }
}
