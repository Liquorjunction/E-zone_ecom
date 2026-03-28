<?php

namespace App\Repositories\Admin\Interfaces;

interface SubCategoryRepositoryInterface
{
    public function getAllSubCategories();
    public function getActiveSubCategories();
    public function getSubCategoriesByCategoryId($categoryId);
    public function getSubCategoryById($id);
    public function storeSubCategory($request);
    public function updateSubCategory($request, $id);
    public function deleteSubCategory($id);
    public function updateStatus($id);
    public function getDataForDataTable($request = null);
}
