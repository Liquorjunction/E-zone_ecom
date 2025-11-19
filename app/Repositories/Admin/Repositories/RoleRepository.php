<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    protected $guardName = 'admin';
    public function getRoles()
    {
        return Role::where('guard_name', $this->guardName)->get();
    }

    public function getPermissions()
    {
        return Permission::where('guard_name', $this->guardName)->get();
    }

    public function getAdmin($id)
    {
        return Role::find($id);
    }

    public function getAllAdmins()
    {
        return Role::where('guard_name', $this->guardName)->get();
    }

    public function updateRole($request, $id)
    {
        $role = Role::findOrFail($id);

        // Ensure you only update the name
        $role->update(['name' => $request->input('name')]);

        // Sync permissions, ensuring they are also for the admin guard
        if ($request->has('permissions')) {
            $permissions = Permission::where('guard_name', $this->guardName)
                                     ->whereIn('id', $request->input('permissions', []))
                                     ->get();
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    public function storeRole($request)
    {
        //  1. Create the role with the CORRECT guard
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => $this->guardName // <-- This is the critical fix
        ]);

        // 2. Sync permissions (if you send them from your 'create' form)
        if ($request->has('permissions')) {
            $permissions = Permission::where('guard_name', $this->guardName)
                                     ->whereIn('id', $request->input('permissions', []))
                                     ->get();
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    public function getDataForDataTable($request)
    {
        // return a query builder for server-side processing
        return Role::where('guard_name', $this->guardName);
    }
}
