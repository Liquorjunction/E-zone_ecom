<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\ProfileRepositoryInterface as ClientProfileRepo;
use App\Http\Requests\Client\Profile\UpdateProfileRequest;
use App\Http\Requests\Client\Profile\ReadProfileRequest;
use App\Http\Requests\Client\Profile\EditProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function readProfile(ReadProfileRequest $request, ClientProfileRepo $clientProfileRepo)
    {
        $profile = $clientProfileRepo->readProfile($request->user());
        return response()->json(['profile' => $profile], 200);
    }

    public function showProfile(ClientProfileRepo $clientProfileRepo)
    {
        $profile = $clientProfileRepo->readProfile(Auth::user());
        // $profile is an array: ['user' => ..., 'orderCount' => ..., 'rewardPoints' => ...]
        return view('client.pages.dashboard.profile', [
            'orderCount' => $profile['orderCount'],
            'rewardPoints' => $profile['rewardPoints'],
        ]);
    }

    public function editProfile(EditProfileRequest $request, ClientProfileRepo $clientProfileRepo)
    {
        $data = $request->validated();
        $profile = $clientProfileRepo->editProfile($data);
        return response()->json(['profile' => $profile], 200);
    }

    public function updateProfile(UpdateProfileRequest $request, ClientProfileRepo $clientProfileRepo)
    {
        $data = $request->validated();
        $updatedProfile = $clientProfileRepo->updateProfile($request->user(), $data);

        if (!$updatedProfile) {
            return response()->json(['message' => statictranslations('User not found')], 404);
        }

        return response()->json(['profile' => $updatedProfile], 200);
    }
}
