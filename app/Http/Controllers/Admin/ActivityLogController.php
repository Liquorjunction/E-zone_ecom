<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NapsApiLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('admin.pages.activityLog.index');
    }

    public function show($id)
    {
        $napsLog = NapsApiLog::findOrFail($id);

        // Decode the JSON response for display
        $apiResponse = json_decode($napsLog->api_response, true);

        return view('admin.pages.activityLog.show', compact('napsLog', 'apiResponse'));
    }

    public function datatable(Request $request)
    {
        try {
            $napsLogs = NapsApiLog::query()->orderBy('created_at', 'desc');

            return DataTables::of($napsLogs)
                ->addIndexColumn()
                ->addColumn('id', function ($log) {
                    return $log->id;
                })
                ->addColumn('order_id', function ($log) {
                    return $log->order_id ?? 'N/A';
                })
                ->addColumn('api_type', function ($log) {
                    return ucfirst($log->api_type ?? 'N/A');
                })
                ->addColumn('created_at', function ($log) {
                    return $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A';
                })
                ->addColumn('actions', function ($log) {
                    $actions = ['id' => $log->id];
                    $actions['view_url'] = route('admin.naps-log.show', ['id' => $log->id]);
                    return view('admin.partials.datatable-actions', $actions)->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}

