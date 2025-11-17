<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\admin;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * Authenticate admin with login field and password.
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
            Log::error('Admin authentication failed: '. $e->getMessage());
            return false;
        }
    }

    /**
     * Get admin model for editing.
     * Authorized for:
     * 1. Superadmin (can edit anyone)
     * 2. Admin (can edit themselves)
     */
    public function edit($id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();
            $adminToEdit = admin::find($id);

            if (!$adminToEdit) {
                return null;
            }

            if ($loggedInAdmin->can('edit any admin')) {
                return $adminToEdit;
            }

            if ($loggedInAdmin->can('update own profile') && $loggedInAdmin->id == $id) {
                return $adminToEdit;
            }

            return null; // Not authorized
        } catch (\Exception $e) {
            Log::error('Admin edit failed: '. $e->getMessage());
            return null;
        }
    }

    /**
     * Update an admin's OWN profile (profile pic and password only).
     * This action is for a regular admin managing their own account.
     */
    public function update($request, $id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // --- Authorization Check ---
            // You must be editing yourself AND have the 'update own profile' permission
            if ($loggedInAdmin->id != $id || !$loggedInAdmin->can('update own profile')) {
                return false; // Not authorized
            }

            $admin = $loggedInAdmin; // We already have the model
            $hasChanges = false;

            // --- Handle Profile Image Update ---
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($admin->profile_image && $admin->profile_image !== '01.png') {
                    $oldImagePath = public_path('admin_assets/images/avatars/' . $admin->profile_image);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
                // Handle file upload
                $file = $request->file('profile_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('admin_assets/images/avatars'), $filename);
                $admin->profile_image = $filename;
                $hasChanges = true;
            }

            // --- Handle Password Update ---
            if ($request->filled('password')) {

                // 1. Verify Current Password
                if (!Hash::check($request->current_password, $admin->password)) {
                    // Password does not match
                    Log::warning("Admin self-update failed: Incorrect current password for user {$admin->id}");
                    return false;
                }

                // 2. (Recommended) Verify Password Confirmation
                if ($request->password !== $request->password_confirmation) {
                     Log::warning("Admin self-update failed: Password confirmation does not match for user {$admin->id}");
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
            Log::error('Admin update failed: '. $e->getMessage());
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

            if (!$loggedInAdmin->can('create admins')) {
                return false; // Not authorized
            }

            // --- Create user in 2 steps to get the ID ---

            // 1. Create the user with a temporary unique username
            //    (We'll use the email since it's already validated as unique)
            $admin = admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->email, // <-- TEMPORARY value
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            // 2. Now that we have the $admin->id, generate the final username
            $baseUsername =  Str::slug($request->name, ''); // 'John Doe' -> 'johndoe'
            $paddedId = sprintf("%06d", $admin->id);
            $finalUsername = 'Ezn-' . strtolower($baseUsername) . '-' . $paddedId;

            // 3. Update the admin with the permanent username
            $admin->username = $finalUsername;
            $admin->save();

            // --- End Fix ---

            $admin->assignRole($request->role);

            return $admin;
        } catch (\Exception $e) {
            Log::error('Superadmin create admin failed: '. $e->getMessage());
            return false;
        }
    }

    /**
     * Superadmin updates another admin's details.
     * Can update everything EXCEPT username.
     */
    public function superadminUpdateAdmin($request, $id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            // --- Authorization Check ---
            if (!$loggedInAdmin->can('edit any admin')) {
                return false; // Not authorized
            }

            $admin = admin::find($id);
            if (!$admin) {
                return false;
            }

            // --- Update Fields (Rule: Everything EXCEPT username) ---
            $dataToUpdate = $request->only(['name', 'email', 'profile_image', 'status']);
            $admin->fill($dataToUpdate);

            // --- Update Role ---
            if ($request->has('role')) {
                $admin->syncRoles([$request->role]);
            }

            // --- Optional Password Reset (no current_password check) ---
            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save(); // Save all changes
            return true;

        } catch (\Exception $e) {
            Log::error('Superadmin update admin failed: '. $e->getMessage());
            return false;
        }
    }

    /**
     * Superadmin toggles admin status (active/inactive).
     */
    public function superadminToggleAdminStatus($id)
    {
        try {
            $loggedInAdmin = Auth::guard('admin')->user();

            if (!$loggedInAdmin->can('toggle admin status')) {
                return false; // Not authorized
            }

            $admin = admin::find($id);
            if (!$admin) {
                return false;
            }

            $admin->status = $admin->status === 'active' ? 'inactive' : 'active';
            $admin->save();
            return true;
        } catch (\Exception $e) {
            Log::error('Superadmin toggle admin status failed: '. $e->getMessage());
            return false;
        }
    }

    /**
     * Logout the admin.
     */
    public function logout(): void
    {
        try {
            Auth::guard('admin')->logout();
        } catch (\Exception $e) {
            Log::error('Admin logout failed: '. $e->getMessage());
        }
    }

    /**
     * Get Datatable Data
     */
    public function getDataForDataTable($request)
    {
        return admin::select('*');
    }
}
