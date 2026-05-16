<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\QuickStat;
use App\Repositories\Admin\Interfaces\QuickStatRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class QuickStatRepository implements QuickStatRepositoryInterface
{
    public function getAllQuickStats()
    {
        return QuickStat::all();
    }

    public function getActiveQuickStats()
    {
        return QuickStat::where('status', 'active')->get();
    }

    public function getQuickStatById($id)
    {
        return QuickStat::find($id);
    }

    public function createQuickStat($request, $data = null)
    {
        $data = $data ?? $request->validated();

        //Hande image upload if exists
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('quick_stats', 'public');
            $data['icon'] = $iconPath;
        }

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return QuickStat::create($data);
    }

    public function updateQuickStat($request, $id, $data = null)
    {
        $data = $data ?? $request->validated();

        //Hande image upload if exists and replace(delete) old image
        if ($request->hasFile('icon')) {
            $quickStat = QuickStat::find($id);
            if ($quickStat && $quickStat->icon && Storage::disk('public')->exists($quickStat->icon)) {
                Storage::disk('public')->delete($quickStat->icon);
            }
            $iconPath = $request->file('icon')->store('quick_stats', 'public');
            $data['icon'] = $iconPath;
        }

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        return QuickStat::where('id', $id)->update($data);
    }

    public function deleteQuickStat($id)
    {
        // Delete associated icon image if exists
        $quickStat = QuickStat::find($id);
        if ($quickStat && $quickStat->icon && Storage::disk('public')->exists($quickStat->icon)) {
            Storage::disk('public')->delete($quickStat->icon);
        }
        return QuickStat::destroy($id);
    }

    public function updateQuickStatStatus($id)
    {
        $quickStat = QuickStat::find($id);
        if ($quickStat) {
            $quickStat->status = $quickStat->status === 'active' ? 'inactive' : 'active';
            return $quickStat->save();
        }
        return false;
    }

    public function getDataforDataTable()
    {
        return QuickStat::select(['id', 'text1', 'text2', 'icon', 'status', 'created_at', 'updated_at']);
    }
}
