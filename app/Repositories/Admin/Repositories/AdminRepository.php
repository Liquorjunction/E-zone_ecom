<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Admin;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * Authenticate admin.
     */
    public function authenticate(string $login, string $password, bool $remember = false): bool
    {
        try {
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            return Auth::guard('admin')->attempt([
                $field => $login,
                'password' => $password,
            ], $remember);
        } catch (\Exception $e) {
            Log::error('Admin authentication failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get admin by ID.
     */
    public function getById($id)
    {
        try {
            return Admin::find($id);
        } catch (\Exception $e) {
            Log::error('Admin get failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get admin model for editing.
     */
    public function edit($id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();
            $adminToEdit = Admin::find($id);

            if (!$adminToEdit) {
                return null;
            }

            // Check new permission: user management.update
            if ($loggedInAdmin->can('user management.update')) {
                return $adminToEdit;
            }

            // Check new permission: profile.update (for own profile)
            if ($loggedInAdmin->id == $id) {
                return $adminToEdit;
            }

            return null; // Not authorized
        } catch (\Exception $e) {
            Log::error('Admin edit failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an admin's OWN profile.
     */
    public function update($request, $id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // Allow updating own profile by default
            if ($loggedInAdmin->id != $id) {
                return false;
            }

            $admin = $loggedInAdmin;
            $hasChanges = false;

            // Handle Profile Image
            if ($request->hasFile('profile_image')) {
                if ($admin->profile_image && $admin->profile_image !== '01.png') {
                    Storage::disk('public')->delete('avatars/' . $admin->profile_image);
                }
                $path = $request->file('profile_image')->store('avatars', 'public');
                $admin->profile_image = basename($path);
                $hasChanges = true;
            }

            // Handle Password
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $admin->password)) {
                    return false;
                }
                if ($request->password !== $request->password_confirmation) {
                    return false;
                }
                $admin->password = Hash::make($request->password);
                $hasChanges = true;
            }

            if ($hasChanges) {
                $admin->save();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Admin update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new admin.
     */
    public function superadminCreateAdmin($request)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // Check new permission: user management.create
            if (!$loggedInAdmin->can('user management.create')) {
                return false;
            }

            // 1. Create User
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email, // Temporary
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'remarks' => $request->remarks,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            // 2. Generate Username
            $baseUsername =  Str::slug($request->name, '');
            $paddedId = sprintf("%06d", $admin->id);
            $finalUsername = 'Ezn-' . strtolower($baseUsername) . '-' . $paddedId;

            $admin->username = $finalUsername;
            $admin->save();

            // Handle Profile Image
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('avatars', 'public');
                $admin->profile_image = basename($path);
                $admin->save();
            }

            $admin->assignRole($request->role);

            return $admin;
        } catch (\Exception $e) {
            Log::error('Superadmin create admin failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Superadmin updates another admin.
     */
    public function superadminUpdateAdmin($request, $id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // Check new permission: user management.update
            if (!$loggedInAdmin->can('user management.update')) {
                return false;
            }

            $admin = Admin::find($id);
            if (!$admin) return false;

            $dataToUpdate = $request->only(['name', 'email', 'phone', 'date_of_birth', 'remarks', 'status']);
            $admin->fill($dataToUpdate);

            // Handle Profile Image
            if ($request->hasFile('profile_image')) {
                if ($admin->profile_image && $admin->profile_image !== '01.png') {
                    Storage::disk('public')->delete('avatars/' . $admin->profile_image);
                }
                $path = $request->file('profile_image')->store('avatars', 'public');
                $admin->profile_image = basename($path);
            }

            if ($request->has('role')) {
                $admin->syncRoles([$request->role]);
            }

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();
            return true;
        } catch (\Exception $e) {
            Log::error('Superadmin update admin failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Superadmin toggles admin status.
     */
    public function superadminToggleAdminStatus($id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // Check new permission: user management.update (Toggling status is an update)
            if (!$loggedInAdmin->can('user management.update')) {
                return false;
            }

            $admin = Admin::findOrFail($id);

            // Toggle status
            $oldStatus = $admin->status;
            $admin->status = $admin->status === 'active' ? 'inactive' : 'active';

            // Save the changes
            if (!$admin->save()) {
                return false;
            }

            // Return fresh instance from database to confirm save
            $freshAdmin = Admin::findOrFail($id);
            return $freshAdmin;
        } catch (\Exception $e) {
            Log::error('Toggle Status - Exception caught', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function findByLoginCredentials(string $login, string $password)
    {
        try {
            $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $admin = Admin::where($field, $login)->first();

            if ($admin && Hash::check($password, $admin->password)) {
                return $admin;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Find by login credentials failed: ' . $e->getMessage());
            return null;
        }
    }

    public function logout(): void
    {
        try {
            Auth::guard('admin')->logout();
        } catch (\Exception $e) {
            Log::error('Admin logout failed: ' . $e->getMessage());
        }
    }

    public function getDataForDataTable($request)
    {
        return Admin::select([
            'id',
            'name',
            'email',
            'username',
            'status',
            'profile_image',
            'created_at'
        ]);
    }
}
