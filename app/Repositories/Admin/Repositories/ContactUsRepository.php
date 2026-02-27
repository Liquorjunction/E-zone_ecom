<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\ContactUs;
use App\Repositories\Admin\Interfaces\ContactUsRepositoryInterface;

class ContactUsRepository implements ContactUsRepositoryInterface
{
    public function getAllContactUsEntries(array $filters = [])
    {
        $query = ContactUs::with('attachments')->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    public function getContactUsEntryById(int $id): ?ContactUs
    {
        return ContactUs::find($id);
    }

    public function updateContactUsEnquiryStatus(int $id, string $status): bool
    {
        $entry = ContactUs::find($id);
        if (!$entry) {
            return false;
        }

        $entry->status = $status;
        return $entry->save();
    }

    public function addAdminNotesToContactUsEntry(int $id, string $notes): bool
    {
        $entry = ContactUs::find($id);
        if (!$entry) {
            return false;
        }

        $entry->admin_notes = $notes;
        return $entry->save();
    }

    public function getDataForDataTable(array $filters = [])
    {
        $query = ContactUs::latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Remove dd($query) for production
        return $query->get();
    }

    public function replayToEnquiry(int $id, string $admin_message): bool
    {
        $entry = ContactUs::find($id);
        if (!$entry) {
            return false;
        }

        $entry->admin_message = $admin_message;
        return $entry->save();
    }
}
