<?php

namespace App\Http\Controllers\Web\Backend\Service;

use App\Models\Service;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {

            $data = Service::orderBy('id','asc')->get();

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
                              <a href="' . route('admin.service.edit', ['id' => $data->id]) . '" class="text-white btn btn-primary" title="Edit">
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
        return view('backend.layouts.service.index');
    }

    public function create() {
        return view('backend.layouts.service.create');
    }

    public function store(Request $request) {



        try {
            $validator=Validator::make($request->all(),[
                'service_name' => 'required|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5000'
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }
            $icon_name= null;
            if ($request->hasFile('icon')) {
                    $icon_name = uploadImage($request->file('icon'), 'service');
            }

            Service::create([
                'service_name'=> $request->input('service_name'),
                'icon'=> $icon_name
            ]);

            return to_route('admin.service.index')->with('t-success', 'service Created');

        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    public function edit($id) {
        $data = Service::findOrFail($id);
        return view('backend.layouts.service.edit', compact('data'));
    }

    public function update(Request $request,  $id) {


        try {
            $validator=Validator::make($request->all(),[
                'service_name' => 'required|string',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5000'
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }
            $data=Service::findOrFail($id);
            $icon_name=$data->icon?? null;
            if ($request->hasFile('icon')) {
                    $icon_name = uploadImage($request->file('icon'), 'service');
                    if ($data && $data->icon) {
                        $previousImagePath = public_path($data->icon);
                        if (file_exists($previousImagePath)) {
                            unlink($previousImagePath);
                        }
                    }
            }

            $data->update([
               'service_name'=> $request->input('service_name'),
                'icon'=> $icon_name
            ]);

            return to_route('admin.service.index')->with('t-success', 'Service Updated');

        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    public function status(int $id) {
        $data = Service::findOrFail($id);
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

    public function destroy(int $id) {
        $data = Service::findOrFail($id);
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
}
