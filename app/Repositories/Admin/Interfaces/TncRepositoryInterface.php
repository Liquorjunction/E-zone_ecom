<?php

namespace App\Repositories\Admin\Interfaces;

interface TncRepositoryInterface
{
    public function getAllTncs();
    public function getTncById($id);
    public function createTnc($request, $data = null);
    public function updateTnc($id, $request, $data = null);
    public function deleteTnc($id);
    public function updateTncStatus($id);
    public function getDataforDataTable();
}
