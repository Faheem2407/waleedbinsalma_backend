<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StoreBookmark;

class BookmarkController extends Controller
{
    use ApiResponse;

    public function add(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
        ]);

        try {
            $userId = Auth::id();

            $bookmark = StoreBookmark::firstOrCreate([
                'user_id' => $userId,
                'online_store_id' => $request->online_store_id,
            ]);

            return $this->success($bookmark, 'Store bookmarked successfully.', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to bookmark store. ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove a bookmark
     */
    public function remove(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
        ]);

        try {
            $userId = Auth::id();

            $bookmark = StoreBookmark::where('user_id', $userId)
                ->where('online_store_id', $request->online_store_id)
                ->first();

            if (!$bookmark) {
                return $this->error('Bookmark not found.', 404);
            }

            $bookmark->delete();

            return $this->success(null, 'Bookmark removed successfully.', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to remove bookmark. ' . $e->getMessage(), 500);
        }
    }
}
