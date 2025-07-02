<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Exception;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = BlogCategory::latest()->get();

            return datatables()->of($categories)
                ->addIndexColumn()
                ->addColumn('name', fn($category) => '<p>' . e($category->name) . '</p>')
                ->addColumn('status', function ($category) {
                    return '<div class="form-check form-switch">
                        <input onclick="showStatusChangeAlert(' . $category->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $category->id . '" name="status"'
                        . ($category->status === 'active' ? ' checked' : '') . '>
                        <label class="form-check-label" for="customSwitch' . $category->id . '"></label>
                    </div>';
                })
                ->addColumn('action', function ($category) {
                    return '<div class="btn-group btn-group-sm">
                        <a href="' . route('blogCategory.edit', $category->id) . '" class="btn btn-primary"><i class="bi bi-pencil"></i></a>
                        <a href="#" onclick="showDeleteConfirm(' . $category->id . ')" class="btn btn-danger"><i class="bi bi-trash"></i></a>
                    </div>';
                })
                ->rawColumns(['name', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.blog_categories.index');
    }

    public function create(): View
    {
        return view('backend.layouts.blog_categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);

        try {
            BlogCategory::create($request->only('name'));

            return redirect()->route('blogCategory.index')->with('t-success', 'Category created successfully.');
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $category = BlogCategory::findOrFail($id);
        return view('backend.layouts.blog_categories.edit', compact('category'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
        ]);

        try {
            $category->update($request->only('name'));

            return redirect()->route('blogCategory.index')->with('t-success', 'Category updated successfully.');
        } catch (Exception $e) {
            return back()->with('t-error', $e->getMessage());
        }
    }

    public function status(int $id): JsonResponse
    {
        $category = BlogCategory::findOrFail($id);

        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();

        return response()->json([
            't-success' => $category->status === 'active',
            'message' => $category->status === 'active' ? 'Published' : 'Unpublished',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $category = BlogCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                't-success' => true,
                'message' => 'Deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                't-success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
