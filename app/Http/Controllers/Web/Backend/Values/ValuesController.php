<?php

namespace App\Http\Controllers\Web\Backend\Values;

use App\Models\Values;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ValuesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data =Values::orderBy('id','asc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

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
                              <a href="' . route('admin.values.edit',  $data->id) . '" class="text-white btn btn-primary" title="Edit">
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
        $data =Values::orderBy('id','asc')->get();
        return view('backend.layouts.values.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.values.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'name' => 'required|string',
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }


            Values::create([
                'name'=> $request->input('name'),
            ]);

            return to_route('admin.values.index')->with('t-success', 'values Created');

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
        $data = Values::findOrFail($id);
        return view('backend.layouts.values.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          try {
            $validator=Validator::make($request->all(),[
                'name' => 'required|string',
            ]);

            if ($validator->fails()){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
            }
            $data=Values::findOrFail($id);


            $data->update([
               'name'=> $request->input('name'),
            ]);

            return to_route('admin.values.index')->with('t-success', 'values Updated');

        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Values::findOrFail($id);

        $data->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }

     public function status(int $id) {
        $data = Values::findOrFail($id);
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
