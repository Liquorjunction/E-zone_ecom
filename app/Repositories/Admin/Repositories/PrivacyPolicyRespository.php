<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\PrivacyPolicy;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRespositoryInterface;

class PrivacyPolicyRespository implements PrivacyPolicyRespositoryInterface
{
    public function getPrivacyPolicy()
    {
        return PrivacyPolicy::first();
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

    public function destroyPrivacyPolicy($request)
    {
        $privacyPolicy = PrivacyPolicy::find($request->input('id'));
        if ($privacyPolicy) {
            $privacyPolicy->delete();
        }

        return $privacyPolicy;
    }

    public function getDataForDataTable()
    {
        return PrivacyPolicy::select(['id', 'description', 'created_at', 'updated_at'])->get();
    }
}
