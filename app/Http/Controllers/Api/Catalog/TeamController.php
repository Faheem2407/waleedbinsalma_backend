<?php

namespace App\Http\Controllers\Api\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = $request->query('query');
        $businessProfileId = auth()->user()->businessProfile->id;

        $teams = Team::with(['teamAddresses', 'teamServices'])
            ->where('business_profile_id', $businessProfileId)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('first_name', 'like', '%' . $query . '%')
                        ->orWhere('last_name', 'like', '%' . $query . '%');
                });
            })
            ->get();

        if ($teams->isEmpty()) {
            return $this->error([], 'No team members found.', 404);
        }

        return $this->success($teams, 'Team Members fetched successfully.',200);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_profile_id' => 'required|exists:business_profiles,id',
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'email' => 'required|email|unique:teams',
            'phone' => 'required',
            'country' => 'required',
            'birthday' => 'required|date',
            'job_title' => 'required|string',
            'start_date' => 'required',
            'employment_type' => 'required|in:full_time,part_time,contract,internship,volunteer',
            'employee_id' => 'required',
            'photo' => 'nullable|image',
            'teamAddresses' => 'array',
            'teamAddresses.*.address_name' => 'required|string',
            'teamAddresses.*.address' => 'required|string',
            'teamServices' => 'array',
            'teamServices.*' => 'exists:catalog_teamServices,id'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $data = $request->except('photo', 'teamAddresses', 'teamServices');

        $photo_name = null;
        if ($request->hasFile('photo')) {
            $photo_name = uploadImage($request->file('photo'), 'team_photos');
        }

        $data['photo'] = $photo_name;


        $team = Team::create($data);

        if ($request->has('teamAddresses')) {
            foreach ($request->teamAddresses as $address) {
                $team->teamAddresses()->create($address);
            }
        }


        if ($request->has('teamServices')) {
            $team->teamServices()->sync($request->teamServices);
        }

        return $this->success($team->load('teamAddresses', 'teamServices'), 'Team Member created successfully.',201);
    }

    // Show
    public function show($id)
    {
        $team = Team::with(['teamAddresses', 'teamServices'])->find($id);
        if (!$team)
            return $this->error([], 'Team Member not found', 404);

        return $this->success($team, 'Team Member fetched successfully.',200);
    }

    // Update
    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        if (!$team)
            return $this->error([], 'Team Member not found', 404);

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'photo' => 'nullable|image|max:2048',
            'teamAddresses' => 'array',
            'teamAddresses.*.address_name' => 'required|string',
            'teamAddresses.*.address' => 'required|string',
            'teamServices' => 'array',
            'teamServices.*' => 'exists:catalog_teamServices,id'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $data = $request->except('photo', 'teamAddresses', 'teamServices');

        $photo_name = null;
        if ($request->hasFile('photo')) {
            $photo_name = uploadImage($request->file('photo'), 'team_photos');
            $data['photo'] = $photo_name;
        }


        $team->update($data);

        // Replace Addresses
        if ($request->has('teamAddresses')) {
            $team->teamAddresses()->delete();
            foreach ($request->teamAddresses as $address) {
                $team->teamAddresses()->create($address);
            }
        }

        // Sync Services
        if ($request->has('teamServices')) {
            $team->teamServices()->sync($request->teamServices);
        }

        return $this->success($team->load('teamAddresses', 'teamServices'), 'Team Member updated successfully.',200);
    }

    // Delete
    public function destroy($id)
    {
        $team = Team::find($id);
        if (!$team)
            return $this->error([], 'Team Member not found', 404);

        $team->delete();
        return $this->success([], 'Team Member deleted successfully.',200);
    }
}
