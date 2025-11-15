<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\StoreRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface as RoleRepo;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        return view('admin.pages.userManagement.role-permission', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();
        return view('admin.pages.roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request, RoleRepo $roleRepo)
    {
        $role = $roleRepo->storeRole($request);
        return redirect()->route('admin.roles-permissions.index')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::where('guard_name', 'admin')->get();
        return view('admin.pages.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id, RoleRepo $roleRepo)
    {
        $role = $roleRepo->updateRole($request, $id);
        return redirect()->route('admin.roles-permissions.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('admin.roles-permissions.index')->with('success', 'Role deleted successfully.');
    }
}
