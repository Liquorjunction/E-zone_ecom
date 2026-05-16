<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Newsletter;
use App\Repositories\Admin\Interfaces\NewsletterRepositoryInterface;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function getAll($request)
    {
        return Newsletter::all();
    }

    public function getActiveNewsletters()
    {
        return Newsletter::where('status', 'active')->get();
    }

    public function findById($id)
    {
        return Newsletter::findOrFail($id);
    }

    public function toggleStatus($id)
    {
        $newsletter = $this->findById($id);
        $newsletter->status = $newsletter->status === 'active' ? 'inactive' : 'active';
        $newsletter->save();

        return $newsletter;
    }

    public function delete($id)
    {
        $newsletter = $this->findById($id);
        return $newsletter->delete();
    }

    public function sendNewsletter($id)
    {
        $newsletter = $this->findById($id);
        // Logic to send the newsletter goes here
        // This is a placeholder and should be implemented according to your mailing system
        return true;
    }

    public function getDataForDataTable($request)
    {
        $query = Newsletter::query();

        // Status filter
        if (isset($request['status']) && in_array($request['status'], ['active', 'inactive'])) {
            $query->where('status', $request['status']);
        }

        // Date range filter
        $startDate = $request['start_date'] ?? $request['date_from'] ?? null;
        $endDate = $request['end_date'] ?? $request['date_to'] ?? null;
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->select(['id', 'email', 'status', 'created_at']);
    }
}
