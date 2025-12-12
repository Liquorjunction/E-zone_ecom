<?php

namespace App\Repositories\Admin\Interfaces;

interface CategoryRepositoryInterface
{
    public function getAllCategories();
    public function getActiveCategories();
    public function getCategoryById($id);
    public function storeCategory($request);
    public function updateCategory($request, $id);
    public function deleteCategory($id);
    public function updateStatus($id);
    public function getDataForDataTable();
}
