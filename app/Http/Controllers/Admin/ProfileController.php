<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\UpdateProfileRequest;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show Profile Page
     */
    public function index()
    {
        // Admin data is now available globally via AdminHelper
        return view('admin.pages.profile');
    }
    /**
     * Show the form for editing the current admin's own profile.
     */
    public function showProfileForm()
    {
        // This method doesn't need the repository
        $admin = Auth::guard('admin')->user();
        return view('admin.pages.userManagement.edit', compact('admin'));
    }

    /**
     * Update the current admin's profile (pic & password).
     * Uses the standard update() method from the repo.
     */
    public function updateProfile(UpdateProfileRequest $request, AdminRepositoryInterface $adminRepository)
    {
        $adminId = Auth::guard('admin')->id();

        $success = $adminRepository->update($request, $adminId);

        if (!$success) {
            // The repo returns false if the current_password was incorrect
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Your current password does not match.'], 422);
            }
            return back()->withErrors(['current_password' => 'Your current password does not match.']);
        }

        if ($request->ajax()) {
            $admin = Auth::guard('admin')->user();
            $imageUrl = $admin->profile_image ? asset('admin_assets/images/avatars/' . $admin->profile_image) : asset('admin_assets/images/avatars/01.png');
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.', 'image_url' => $imageUrl]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
