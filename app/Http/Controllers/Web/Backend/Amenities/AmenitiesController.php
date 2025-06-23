<?php

namespace App\Http\Controllers\Web\Backend\Amenities;

use App\Models\Amenities;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AmenitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data =Amenities::orderBy('id','asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('icon', function ($data) {
                    if ($data->icon) {
                        $url = asset($data->icon);
                        return '<img src="' . $url . '" alt="image" width="50px" height="50px" style="margin-left:20px;">';
                    } else {
                        return '<span>No Image Available</span>';
                    }
                })
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                    if ($data->status == "active") {
                        $status .= "checked";
                    }
                    $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="text-center"><div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                              <a href="' . route('admin.amenities.edit',  $data->id) . '" class="text-white btn btn-primary" title="Edit">
                              <i class="bi bi-pencil"></i>
                              </a>
                              <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" type="button" class="text-white btn btn-danger" title="Delete">
                              <i class="bi bi-trash"></i>
                            </a>
                            </div></div>';
                })
                ->rawColumns(['action', 'status','icon'])
                ->make(true);
        }
        return view('backend.layouts.amenities.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.amenities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'name' => 'required|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5000'
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }
            $icon_name= null;
            if ($request->hasFile('icon')) {
                    $icon_name = uploadImage($request->file('icon'), 'amenities');
            }

            Amenities::create([
                'name'=> $request->input('name'),
                'icon'=> $icon_name
            ]);

            return to_route('admin.amenities.index')->with('t-success', 'Amenities Created');

        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Amenities::findOrFail($id);
        return view('backend.layouts.amenities.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          try {
            $validator=Validator::make($request->all(),[
                'name' => 'required|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5000'
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }
            $data=Amenities::findOrFail($id);
            $icon_name=$data->icon?? null;
            if ($request->hasFile('icon')) {
                    $icon_name = uploadImage($request->file('icon'), 'amenities');
                    if ($data && $data->icon) {
                    $previousImagePath = public_path($data->icon);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
            }

            $data->update([
               'name'=> $request->input('name'),
                'icon'=> $icon_name
            ]);

            return to_route('admin.amenities.index')->with('t-success', 'Amenities Updated');

        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Amenities::findOrFail($id);
         if ($data && $data->icon) {
            $previousImagePath = public_path($data->icon);
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }
        $data->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }

     public function status(int $id) {
        $data = Amenities::findOrFail($id);
        if ($data->status == 'inactive') {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        }
    }
}
