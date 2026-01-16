<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Http\Requests\Admin\Auth\EditAdminRequest;
use App\Http\Requests\Admin\Auth\ReadProfileRequest;
use App\Http\Requests\Admin\Auth\StoreAdminRequest;
use App\Http\Requests\Admin\Auth\UpdateAdminRequest;
use App\Http\Requests\Admin\Auth\ViewAdminRequest;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();

        return view('admin.pages.home', compact('admin'));
    }

    public function index()
    {
        try {
            return view('admin.pages.userManagement.index');
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@index: '.$e->getMessage());
            throw $e;
        }
    }

    public function data(AdminRepositoryInterface $adminRepository, ReadProfileRequest $request)
    {
        try {
            $admins = $adminRepository->getDataForDataTable($request->all());

            return DataTables::of($admins)
                ->addIndexColumn()
                ->addColumn('role', function ($admin) {
                    return $admin->roles->pluck('name')->join(', ');
                })
                ->addColumn('profile_image', function ($admin) {
                    $data['image'] = $admin->profile_image ? asset('storage/avatars/'.$admin->profile_image) : asset('admin_assets/images/avatars/01.png');

                    return view('admin.partials.image', $data)->render();
                })
                ->addColumn('status', function ($admin) use ($request) {
                    $data['status'] = $admin->status;
                    $data['toggle_url'] = $request->user()->can('user management.update')
                        ? route('admin.user-management.toggleStatus', ['id' => $admin->id]): '';

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('created_at', function ($admin) {
                    return $admin->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($admin) use ($request) {
                    $data['edit_url'] = $request->user()->can('user management.update')
                        ? route('admin.user-management.edit', ['id' => $admin->id]): '';
                    $data['view_url'] = $request->user()->can('user management.read')
                        ? route('admin.user-management.view', ['id' => $admin->id]): '';
                    $data['model'] = $admin;
                    $data['routePrefix'] = 'admin.user-management';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['profile_image', 'status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@data: '.$e->getMessage());
            throw $e;
        }
    }

    public function login(AdminLoginRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $login = $request->input('login');
            $password = $request->input('password');

            $admin = $adminRepository->findByLoginCredentials($login, $password);

            if ($admin && $admin->status === 'inactive') {
                throw ValidationException::withMessages([
                    'login' => ['Your account has been disabled. Please contact support.'],
                ]);
            }

            if ($adminRepository->authenticate($login, $password, $request->filled('remember'))) {
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'));
            }

            throw ValidationException::withMessages([
                'login' => ['The provided credentials do not match our records.'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@login: '.$e->getMessage());
            throw $e;
        }
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.pages.userManagement.create', compact('roles'));
    }

    public function show(ViewAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $id = $request->input('id');
            $admin = $adminRepository->getById($id);

            if (! $admin) {
                abort(403, 'You are not authorized to view this user.');
            }

            return view('admin.pages.userManagement.view', compact('admin'));
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@show: '.$e->getMessage());
            throw $e;
        }
    }

    public function store(StoreAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $adminRepository->superadminCreateAdmin($request);

            return redirect()->route('admin.user-management.index')->with('success', 'Admin created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@store: '.$e->getMessage());
            throw $e;
        }
    }

    public function edit(EditAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $id = $request->input('id');
            $admin = $adminRepository->edit($id);

            if (! $admin) {
                abort(403, 'You are not authorized to edit this user.');
            }

            $roles = Role::where('guard_name', 'admin')->get();

            return view('admin.pages.userManagement.edit', compact('admin', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@edit: '.$e->getMessage());
            throw $e;
        }
    }

    public function update(UpdateAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $id = $request->input('id');
            $adminRepository->superadminUpdateAdmin($request, $id);

            return redirect()->route('admin.user-management.index')->with('success', 'Admin updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@update: '.$e->getMessage());
            throw $e;
        }
    }

    public function toggleStatus(EditAdminRequest $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $id = $request->input('id');

            // Call repository to toggle status
            $result = $adminRepository->superadminToggleAdminStatus($id);

            // Check if update was successful
            if ($result === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update admin status. Please check permissions.',
                ], 403);
            }
            // Return success with updated status
            return response()->json([
                'success' => true,
                'message' => 'Admin status updated successfully.',
                'data' => [
                    'id' => $result->id,
                    'status' => $result->status,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Controller - Exception in toggleStatus', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status.',
            ], 500);
        }
    }

    public function logout(Request $request, AdminRepositoryInterface $adminRepository)
    {
        try {
            $adminRepository->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login');
        } catch (\Exception $e) {
            Log::error('Error in AdminAuthController@logout: '.$e->getMessage());
            throw $e;
        }
    }
}
