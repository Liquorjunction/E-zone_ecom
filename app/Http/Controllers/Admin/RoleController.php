<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use App\Http\Requests\Admin\Roles\UpdateRoleRequest;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface as RoleRepo;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.pages.rolePermission.index');
    }

    public function data(Request $request, RoleRepo $roleRepo)
    {
        try {
            $roles = $roleRepo->getDataForDataTable($request);

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($role) {
                    $data['edit_url'] = route('admin.roles-permissions.edit', $role->id);

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        // Fetch all admin permissions
        $permissions = Permission::where('guard_name', 'admin')->get();

        // Group permissions by module (e.g., "admin.create" -> Key: "admin")
        $permissionGroups = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.pages.rolePermission.create', compact('permissionGroups'));
    }

    public function store(StoreRoleRequest $request, RoleRepo $roleRepo)
    {
        $roleRepo->storeRole($request);

        return redirect()->route('admin.roles-permissions.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        $permissions = Permission::where('guard_name', 'admin')->get();

        $permissionGroups = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.pages.rolePermission.edit', compact('role', 'permissionGroups'));
    }

    public function update(UpdateRoleRequest $request, $id, RoleRepo $roleRepo)
    {
        $roleRepo->updateRole($request, $id);

        return redirect()->route('admin.roles-permissions.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        // Prevent deleting super-admin or admin basic roles if needed
        if (in_array($role->name, ['super-admin'])) {
            return back()->with('error', 'Cannot delete this system role.');
        }

        $role->delete();

        return redirect()->route('admin.roles-permissions.index')
            ->with('success', 'Role deleted successfully.');
    }
}
