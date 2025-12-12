<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\TermsAndCondition;
use App\Repositories\Admin\Interfaces\TncRepositoryInterface;

class TncRepository implements TncRepositoryInterface
{
    public function getAllTncs()
    {
        return TermsAndCondition::all();
    }

    public function getTncById($id)
    {
        return TermsAndCondition::findOrFail($id);
    }

    // FIXED: Accept optional $data
    public function createTnc($request, $data = null)
    {
        // Use passed data (with translations) or fallback to validated request data
        $data = $data ?? $request->validated();

        return TermsAndCondition::create($data);
    }

    public function updateTnc($id, $request, $data = null)
    {
        $tnc = TermsAndCondition::findOrFail($id);
        $data = $data ?? $request->validated();

        $tnc->update($data);

        return $tnc;
    }

    public function updateTncStatus($id)
    {
        $tnc = TermsAndCondition::findOrFail($id);
        $tnc->status = $tnc->status === 'active' ? 'inactive' : 'active';
        $tnc->save();

        return $tnc;
    }

    public function getDataforDataTable()
    {
        return TermsAndCondition::query()->latest();
    }

    public function deleteTnc($id)
    {
        return TermsAndCondition::findOrFail($id)->delete();
    }
}
