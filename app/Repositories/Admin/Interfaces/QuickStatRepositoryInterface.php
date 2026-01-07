<?php

namespace App\Repositories\Admin\Interfaces;

interface QuickStatRepositoryInterface
{
    public function getAllQuickStats();
    public function getActiveQuickStats();
    public function getQuickStatById($id);
    public function createQuickStat($request, $data = null);
    public function updateQuickStat($request, $id, $data = null);
    public function deleteQuickStat($id);
    public function updateQuickStatStatus($id);
    public function getDataforDataTable();
}
