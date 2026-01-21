<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\ProfileRepositoryInterface as ClientProfileRepo;
use App\Http\Requests\Client\Profile\UpdateProfileRequest;
use App\Http\Requests\Client\Profile\ReadProfileRequest;
use App\Http\Requests\Client\Profile\EditProfileRequest;


class ProfileController extends Controller
{
    public function readProfile(ReadProfileRequest $request, ClientProfileRepo $clientProfileRepo)
    {
        $profile = $clientProfileRepo->readProfile($request->user());
        return response()->json(['profile' => $profile], 200);
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
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['profile' => $updatedProfile], 200);
    }
}
