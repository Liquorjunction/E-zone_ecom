<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use App\Http\Requests\Admin\Auth\StoreAdminRequest;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.pages.home' ,compact('admin'));
    }

    public function login(AdminLoginRequest $request, AdminRepositoryInterface $adminRepository)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        if ($adminRepository->authenticate($login, $password, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'login' => ['The provided credentials do not match our records.'],
        ]);
    }
    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        // This method doesn't need the repository
        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name');
        return view('admin.pages.userManagement.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage.
     * Uses superadminCreateAdmin()
     */
    public function store(StoreAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        $adminRepository->superadminCreateAdmin($request);
        return redirect()->route('admin.user-management.index')->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit($id, AdminRepositoryInterface $adminRepository)
    {
        $admin = $adminRepository->edit($id); // Uses the edit() method for auth

        if (!$admin) {
            abort(403, 'You are not authorized to edit this user.');
        }

        $roles = Role::where('guard_name', 'admin')->pluck('name', 'name');
        return view('admin.pages.userManagement.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin in storage.
     * Uses superadminUpdateAdmin()
     */
    public function update(UpdateAdminRequest $request, $id, AdminRepositoryInterface $adminRepository)
    {
        $adminRepository->superadminUpdateAdmin($request, $id);
        return redirect()->route('admin.user-management.index', $id)->with('success', 'Admin updated successfully.');
    }

    /**
     * Toggles the status of the admin.
     * Uses superadminToggleAdminStatus()
     */
    public function toggleStatus($id, AdminRepositoryInterface $adminRepository)
    {
        $adminRepository->superadminToggleAdminStatus($id);
        return back()->with('success', 'Admin status updated.');
    }

    public function logout(Request $request, AdminRepositoryInterface $adminRepository)
    {
        $adminRepository->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
