<?php

namespace App\Repositories\Admin\Interfaces;

interface FaqCategoryRepositoryInterface
{
    public function getAllFaqCategories();
    public function getFaqCategoryById($id);
    public function createFaqCategory($request);
    public function updateFaqCategory($id, $request);
    public function deleteFaqCategory($id);
    public function updateFaqCategoryStatus($id);
    public function getDataforDataTable($request);
}
