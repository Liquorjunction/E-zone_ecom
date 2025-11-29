<?php

namespace App\Repositories\Admin\Interfaces;

interface TncRepositoryInterface
{
    public function getAllTncs();
    public function getTncById($id);
    public function createTnc($request);
    public function updateTnc($id, $request);
    public function deleteTnc($id);
    public function updateTncStatus($id);
    public function getDataforDataTable();
}
