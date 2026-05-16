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

    public function getSubjectList()
    {
        return ContactUs::query()->select('subject')->distinct()->pluck('subject');
    }

    public function getStatusList()
    {
        return ContactUs::query()->select('status')->distinct()->pluck('status');
    }

    public function getDataForDataTable($request)
    {
        $query = ContactUs::latest();

        //  Status filter
        $status = is_array($request) ? ($request['status'] ?? null) : $request->input('status');
        if (in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
            $query->where('status', $status);
        }

        // Date range filter
        $dateFrom = is_array($request) ? ($request['date_from'] ?? null) : $request->input('date_from');
        $dateTo = is_array($request) ? ($request['date_to'] ?? null) : $request->input('date_to');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        // Subject filter
        $subject = is_array($request) ? ($request['subject'] ?? ($request['subject_id'] ?? null)) : ($request->input('subject') ?? $request->input('subject_id'));
        if ($subject) {
            $query->where('subject', $subject);
        }

        return $query;
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
