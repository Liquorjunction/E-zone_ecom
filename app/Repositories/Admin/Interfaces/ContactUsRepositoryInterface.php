<?php

namespace App\Repositories\Admin\Interfaces;

interface ContactUsRepositoryInterface
{
    public function getAllContactUsEntries(array $filters = []);
    public function getContactUsEntryById(int $id);
    public function updateContactUsEnquiryStatus(int $id, string $status);
    public function addAdminNotesToContactUsEntry(int $id, string $notes);
    public function getSubjectList();
    public function getStatusList();
    public function getDataForDataTable($request);
    public function replayToEnquiry(int $id, string $admin_message);
}
