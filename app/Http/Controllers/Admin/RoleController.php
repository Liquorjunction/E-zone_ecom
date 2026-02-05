<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\DestroyRoleRequest;
use App\Http\Requests\Admin\Roles\EditRoleRequest;
use App\Http\Requests\Admin\Roles\ReadRoleRequest;
use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use App\Http\Requests\Admin\Roles\UpdateRoleRequest;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface as RoleRepo;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.pages.rolePermission.index');
    }

    public function show(ReadRoleRequest $request)
    {
        $id = $request->input('id');
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        $permissions = Permission::where('guard_name', 'admin')->get();
        $permissionGroups = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.pages.rolePermission.view', compact('role', 'permissionGroups'));
    }

    public function data(ReadRoleRequest $request, RoleRepo $roleRepo)
    {
        try {
            $roles = $roleRepo->getDataForDataTable($request->all());

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($role) use ($request) {
                    $data['id'] = $role->id;
                    $data['view_url'] = $request->user()->can('role.read') ? route('admin.roles-permissions.show', ['id' => $role->id]): '';
                    $data['edit_url'] = $request->user()->can('role.update') ? route('admin.roles-permissions.edit', ['id' => $role->id]): '';
                    $data['delete_url'] = $request->user()->can('role.delete') ? route('admin.roles-permissions.destroy', ['id' => $role->id]): '';
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
        return redirect()->route('admin.roles-permissions.index')->with('success', 'Role created successfully.');
    }

    public function edit(EditRoleRequest $request)
    {
        $id = $request->input('id');
        $role = Role::where('guard_name', 'admin')->findOrFail($id);

        $permissions = Permission::where('guard_name', 'admin')->get();
        $permissionGroups = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.pages.rolePermission.edit', compact('role', 'permissionGroups'));
    }

    public function update(UpdateRoleRequest $request, RoleRepo $roleRepo)
    {
        $id = $request->input('id');
        $roleRepo->updateRole($request, $id);

        return redirect()->route('admin.roles-permissions.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(DestroyRoleRequest $request, RoleRepo $roleRepo)
    {
        try {
            $id = $request->input('id');
            $role = Role::where('guard_name', 'admin')->findOrFail($id);

            if (in_array($role->name, ['super-admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this system role.',
                ], 422);
            }

            $roleRepo->deleteRole($id);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete role: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }
}
