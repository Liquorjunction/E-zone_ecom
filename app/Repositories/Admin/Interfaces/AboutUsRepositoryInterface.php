<?php

namespace App\Repositories\Admin\Interfaces;

interface AboutUsRepositoryInterface
{
    public function getAboutUsData();
    public function getActiveAboutUsData();
    public function storeAboutUsData($request, $data = null);
    public function updateAboutUsData($id, $request, $data = null);
    public function destroyAboutUsData($id);
    public function toggleAboutUsStatus($id);
    public function getDataForDataTable();

}
