<?php

namespace App\Repositories\Admin\Interfaces;

interface RoleRepositoryInterface
{
    public function getAdmin($id);
    public function getAllAdmins();
    public function getPermissions();
    public function getRoles();
    public function updateRole($request, $id);
    public function storeRole($request);
    public function getDataForDataTable($request);
}
