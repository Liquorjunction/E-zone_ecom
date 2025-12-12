<?php

namespace App\Repositories\Admin\Interfaces;

interface FaqRepositoryInterface
{
    public function getAllFaqs();
    public function getActiveFaqs();
    public function getFaqById($id);
    public function getFaqsByCategory($categoryId);
    public function createFaq($request);
    public function updateFaq($id, $request);
    public function deleteFaq($id);
    public function updateFaqStatus($id);
    public function getDataforDataTable();
}
