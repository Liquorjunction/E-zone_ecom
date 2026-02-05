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

        if ($request->has('search') && !empty($request->input('search.value'))) {
            $searchValue = $request->input('search.value');
            $query->where('email', 'like', '%' . $searchValue . '%');
        }

        return $query->select(['id', 'email', 'status', 'created_at']);
    }
}
