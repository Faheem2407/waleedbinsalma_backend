<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::with('category')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category', fn(Blog $blog) => $blog->category->name ?? '-')
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
                    <a href="' . route('blog.edit', $data->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                    <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.blog.index');
    }

    public function create()
    {
        $categories = BlogCategory::where('status', 'active')->get();
        return view('backend.layouts.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string',
            'image' => 'required|mimes:jpg,jpeg,png',
            'description' => 'required|string',
            'blog_creator' => 'required|string|max:255',
        ]);

        try {
            $imagePath = $request->hasFile('image')
                ? uploadImage($request->file('image'), 'blogs')
                : null;

            Blog::create([
                'blog_category_id' => $request->blog_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'blog_creator' => $request->blog_creator,
                'image' => $imagePath,
            ]);

            return redirect()->route('blog.index')->with('t-success', 'Blog created successfully.');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::where('status', 'active')->get();
        return view('backend.layouts.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string',
            'image' => 'nullable|mimes:jpg,jpeg,png',
            'description' => 'nullable|string',
            'blog_creator' => 'nullable|string|max:255',
        ]);

        try {
            $blog = Blog::findOrFail($id);


            if ($request->hasFile('image')) {
                if ($blog->image && file_exists(public_path($blog->image))) {
                    unlink(public_path($blog->image));
                }

                $blog->image = uploadImage($request->file('image'), 'blogs');
            }

            $blog->update([
                'blog_category_id' => $request->blog_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'blog_creator' => $request->blog_creator,
                'image' => $blog->image,
            ]);

            return redirect()->route('blog.index')->with('t-success', 'Blog updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('t-error', $th->getMessage());
        }
    }


    public function status($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found']);
        }

        $blog->status = $blog->status === 'active' ? 'inactive' : 'active';
        $blog->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found']);
        }

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(['success' => true, 'message' => 'Blog deleted successfully']);
    }
}
