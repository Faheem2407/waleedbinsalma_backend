<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Complain;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ComplainController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $complains = Complain::with(['user', 'store'])->latest()->get();

            return DataTables::of($complains)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return $row->user->first_name.' '.$row->user->last_name ?? 'N/A';
                })
                ->addColumn('store', function ($row) {
                    return $row->store->name ?? 'N/A';
                })
                ->addColumn('message', function ($row) {
                    return Str::limit($row->message, 50);
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.complain.show', $row->id) . '" class="btn btn-sm btn-info">View</a>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.layouts.complains.index');
    }

    public function show($id)
    {
        $complain = Complain::with(['user', 'store'])->findOrFail($id);

        return view('backend.layouts.complains.show', compact('complain'));
    }



}
