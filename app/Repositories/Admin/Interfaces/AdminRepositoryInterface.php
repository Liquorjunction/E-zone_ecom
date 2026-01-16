<?php

namespace App\Repositories\Admin\Interfaces;

interface AdminRepositoryInterface
{
    public function authenticate(string $login, string $password, bool $remember = false): bool;
    public function getById($id);
    public function edit($id);
    public function update($request, $id);
    public function superadminCreateAdmin($request);
    public function superadminUpdateAdmin($request, $id);
    public function superadminToggleAdminStatus($id);
    public function findByLoginCredentials(string $login, string $password);
    public function logout(): void;
    public function getDataForDataTable($request);
}
