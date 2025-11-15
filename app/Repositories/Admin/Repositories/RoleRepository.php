<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function getRoles()
    {
        return Role::all();
    }

    public function getPermissions()
    {
        return Permission::all();
    }

    public function getAdmin($id)
    {
        return Role::find($id);
    }

    public function getAllAdmins()
    {
        return Role::all();
    }

    public function updateRole($request, $id)
    {
        $role = Role::find($id);
        $role->update($request->all());
        return $role;
    }

    public function storeRole($request)
    {
        $role = Role::create($request->all());
        return $role;
    }

    public function getDataForDataTable($request)
    {
        $roles = Role::all();
        return $roles;
    }
}
