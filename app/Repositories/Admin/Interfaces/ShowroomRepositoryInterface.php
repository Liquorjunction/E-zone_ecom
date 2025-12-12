<?php

namespace App\Repositories\Admin\Interfaces;

interface ShowroomRepositoryInterface
{
    public function getShowrooms();
    public function getShowroomById($id);
    public function storeShowroom($request, array $data);
    public function updateShowroom($request, $id, array $data);
    public function deleteShowroom($id);
    public function updateShowroomStatus($id);
    public function getShowroomBySlug($slug);
    public function getDataForDataTable();
}
