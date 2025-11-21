<?php

namespace App\Repositories\Admin\Interfaces;

interface CardRepositoryInterface
{
    public function getAllCards();
    public function getCardById($id);
    public function storeCard($request);
    public function updateCard($request, $id);
    public function deleteCard($id);
    public function updateStatus($id);
    public function getDataForDataTable();
}
