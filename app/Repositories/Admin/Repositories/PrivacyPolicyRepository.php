<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\PrivacyPolicy;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRepositoryInterface;

class PrivacyPolicyRepository implements PrivacyPolicyRepositoryInterface
{
    public function getPrivacyPolicy()
    {
        return PrivacyPolicy::first();
    }

    public function getActivePrivacyPolicy()
    {
        return PrivacyPolicy::where('status', 'active')->first();
    }

    public function storePrivacyPolicy($request, $data = null)
    {
        $data = $data ?? $request->validated();

        return PrivacyPolicy::create($data);
    }

    public function updatePrivacyPolicy($request, $id, $data = null)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        // IMPORTANT: Use the $data passed from controller (which has translations)
        $data = $data ?? $request->validated();

        $privacyPolicy->update($data);

        return $privacyPolicy;
    }

    public function destroyPrivacyPolicy($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        $privacyPolicy->delete();
    }

    public function updatePrivacyPolicyStatus($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        $privacyPolicy->status = $privacyPolicy->status === 'active' ? 'inactive' : 'active';
        $privacyPolicy->save();

        return $privacyPolicy;
    }

    public function getDataForDataTable($request)
    {
        $query = PrivacyPolicy::query()->latest();

        // Status filter
        $status = is_array($request) ? ($request['status'] ?? null) : $request->input('status');
        if (in_array($status, ['active', 'inactive'], true)) {
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

        return $query;
    }
}
