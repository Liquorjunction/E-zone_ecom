<?php

namespace App\Repositories\Admin\Interfaces;

interface NewsletterRepositoryInterface
{
    public function getAll($request);
    public function getActiveNewsletters();
    public function findById($id);
    public function toggleStatus($id);
    public function delete($id);
    public function sendNewsletter($id);
    public function getDataForDataTable($request);
}
