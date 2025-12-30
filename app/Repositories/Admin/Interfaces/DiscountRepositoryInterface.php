<?php

namespace App\Repositories\Admin\Interfaces;

interface DiscountRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getDiscountById($id);
    public function updateStatus($id);
    public function getDiscountsForDataTable();
}
