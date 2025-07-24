<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplainSubmitted;

class ComplainController extends Controller
{
    use ApiResponse;

    public function submit(Request $request)
    {

        $request->validate([
            'store_id' => 'required|exists:online_stores,id',
            'message' => 'required|string|max:1000',
        ]);

        
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error('Unauthorized.', 401);
            }

            $complain = Complain::create([
                'user_id' => $user->id,
                'store_id' => $request->store_id,
                'message' => $request->message,
            ]);

            // Notify admin
            $adminEmail = config('mail.admin_address', 'admin@example.com');
            Mail::to($adminEmail)->send(new ComplainSubmitted($complain));

            return $this->success($complain, 'Complain submitted successfully.', 201);

        } catch (\Exception $e) {
            return $this->error('Failed to submit complain. ' . $e->getMessage(), 500);
        }
    }
}
