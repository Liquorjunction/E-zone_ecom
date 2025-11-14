<?php

namespace App\Repositories\Admin\Interfaces;

interface AdminRepositoryInterface
{
    /**
     * Authenticate admin with login field and password.
     *
     * @param string $login
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function authenticate(string $login, string $password, bool $remember = false): bool;
    public function edit($id);
    public function update($request, $id);
    public function superadminCreateAdmin($request);
    public function superadminUpdateAdmin($request, $id);
    public function superadminToggleAdminStatus($id);
    public function logout(): void;
    public function getDataForDataTable($request);
}
