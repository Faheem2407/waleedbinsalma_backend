<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\ClientReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ClientReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ClientReview::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('client_avatar', function ($data) {
                    $image = asset($data->client_avatar);
                    return "<img src='$image' width='50px' height='50px' />";
                })
                ->addColumn('review', fn($data) => '<p>' . Str::limit($data->review, 100) . '</p>')
                ->addColumn('shop_name', fn($data) => '<p>' . Str::limit($data->shop_name, 100) . '</p>')
                
                ->addColumn('status', function ($data) {
                    return '<div class="form-check form-switch">
                        <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" name="status"'
                        . ($data->status == "active" ? "checked" : "") . '>
                        <label class="form-check-label" for="customSwitch' . $data->id . '"></label>
                    </div>';
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm">
                        <a href="' . route('client_review.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                        <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                    </div>';
                })
                ->rawColumns(['client_avatar','review','shop_name','status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.client_review.index');
    }

    public function create()
    {
        return view('backend.layouts.client_review.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_avatar' => 'required|image|mimes:jpg,jpeg,png',
            'review' => 'required|string',
            'rating' => 'required|integer|between:1,5',
            'shop_name' => 'required|string|max:255',
            'shop_location' => 'required|string|max:255',
        ]);

        try {
            $avatarPath = uploadImage($request->file('client_avatar'), 'client_reviews');

            ClientReview::create([
                'client_avatar' => $avatarPath,
                'review' => $request->review,
                'rating' => $request->rating,
                'shop_name' => $request->shop_name,
                'shop_location' => $request->shop_location,
                'status' => 'active',
            ]);

            return redirect()->route('client_review.index')->with('t-success', 'Review created successfully.');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $review = ClientReview::findOrFail($id);
        return view('backend.layouts.client_review.edit', compact('review'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'client_avatar' => 'nullable|image|mimes:jpg,jpeg,png',
            'review' => 'required|string',
            'rating' => 'required|integer|between:1,5',
            'shop_name' => 'required|string|max:255',
            'shop_location' => 'required|string|max:255',
        ]);

        try {
            $review = ClientReview::findOrFail($id);

            if ($request->hasFile('client_avatar')) {
                if ($review->client_avatar && file_exists(public_path($review->client_avatar))) {
                    unlink(public_path($review->client_avatar));
                }
                $review->client_avatar = uploadImage($request->file('client_avatar'), 'client_reviews');
            }

            $review->update([
                'review' => $request->review,
                'rating' => $request->rating,
                'shop_name' => $request->shop_name,
                'shop_location' => $request->shop_location,
                'client_avatar' => $review->client_avatar,
            ]);

            return redirect()->route('client_review.index')->with('t-success', 'Review updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    public function status($id)
    {
        $review = ClientReview::find($id);

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Review not found']);
        }

        $review->status = $review->status === 'active' ? 'inactive' : 'active';
        $review->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $review = ClientReview::find($id);

        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Review not found']);
        }

        if ($review->client_avatar && Storage::disk('public')->exists($review->client_avatar)) {
            Storage::disk('public')->delete($review->client_avatar);
        }

        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review deleted successfully']);
    }
}
