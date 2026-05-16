<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactUs\UpdateContactUsStatus;
use App\Http\Requests\Admin\ContactUs\AddAdminNotesRequest;
use App\Http\Requests\Admin\ContactUs\ReadContactUsRequest;
use App\Repositories\Admin\Interfaces\ContactUsRepositoryInterface as ContactUsRepo;
use Yajra\DataTables\Facades\DataTables;


class ContactUsController extends Controller
{
    public function index()
    {
        return view('admin.pages.contact_us.index');
    }

    public function show(ReadContactUsRequest $request, ContactUsRepo $contactUsRepo)
    {
        $id = $request->input('id');
        $enquiry = $contactUsRepo->getContactUsEntryById($id);

        return view('admin.pages.contact_us.show', compact('enquiry'));
    }

    public function AddAdminNotes(AddAdminNotesRequest $request, ContactUsRepo $contactUsRepo)
    {
        try {
            $data = $request->validated();
            $contactUsRepo->addAdminNotesToContactUsEntry($data['id'], $data['admin_reply']);
            return response()->json(['status' => true, 'message' => 'Admin notes added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus(UpdateContactUsStatus $request, ContactUsRepo $contactUsRepo)
    {
        try {
            $data = $request->validated();

            $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];

            $allowedTransitions = [
                'open' => ['in_progress', 'closed'],
                'in_progress' => ['resolved', 'closed'],
                'resolved' => ['closed'],
                'closed' => [],
            ];

            $currentEntry = $contactUsRepo->getContactUsEntryById($data['id']);
            $currentStatus = $currentEntry->status;

            if (!in_array($currentStatus, $allowedStatuses)) {
                return response()->json(['status' => false, 'message' => 'Invalid current status.']);
            }

            if (!in_array($data['status'], $allowedTransitions[$currentStatus])) {
                return response()->json(['status' => false, 'message' => 'Invalid status transition.']);
            }

            $contactUsRepo->updateContactUsEnquiryStatus($data['id'], $data['status']);
            return response()->json(['status' => true, 'message' => 'Contact Us entry status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSubjectList(ContactUsRepo $contactUsRepo)
    {
        try {
            $subjects = $contactUsRepo->getSubjectList();
            return response()->json(['status' => true, 'data' => $subjects]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getStatusList(ContactUsRepo $contactUsRepo)
    {
        try {
            $statuses = $contactUsRepo->getStatusList();
            return response()->json(['status' => true, 'data' => $statuses]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function datatable(ReadContactUsRequest $request, ContactUsRepo $contactUsRepo)
    {
        try {
            $statusLabels = [
                'open' => 'open',
                'in_progress' => 'In Progress',
                'resolved' => 'Resolved',
                'closed' => 'Closed',
            ];
            $contactUsData = $contactUsRepo->getDataForDataTable($request->all());
            return DataTables::of($contactUsData)
                ->addIndexColumn()
                ->filterColumn('TicketNumber', function ($query, $keyword) {
                    $query->where('ticket_number', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })
                ->addColumn('TicketNumber', function ($contactUs) {
                    return $contactUs->ticket_number ?? 'N/A';
                })
                ->addColumn('name', function ($contactUs) {
                    return $contactUs->name ?? 'N/A';
                })
                ->addColumn('email', function ($contactUs) {
                    return $contactUs->email ?? 'N/A';
                })
                ->addColumn('subject', function ($contactUs) {
                    $subject = $contactUs->subject ?? 'N/A';
                    return strtoupper(str_replace('_', ' ', $subject));
                })
                ->addColumn('status', function ($contactUs) use ($statusLabels, $request) {
                    $currentStatus = $contactUs->status;
                    $availableStatuses = array_keys($statusLabels);

                     // Only show dropdown for users with permission
                     if ($request->user()->can('contact_us.update')) {
                        return view('admin.partials.datatable-enquiryStatus', [
                            'enquiry' => $contactUs,
                            'statusLabels' => $statusLabels,
                            'availableStatuses' => $availableStatuses,
                            'canUpdate' => true,
                        ])->render();
                    }

                     // For users without permission, just show the status label
                     return $statusLabels[$currentStatus] ?? ucfirst($currentStatus);
                 })
                ->addColumn('created_at', function ($contactUs) {
                    return $contactUs->created_at ? $contactUs->created_at->format('Y-m-d H:i:s') : 'N/A';
                })
                ->addColumn('actions', function ($contactUs) {
                    $canRead = request()->user()->can('contact_us.read');

                    $actions = ['id' => $contactUs->id];

                    if ($canRead) {
                        $actions['view_url'] = route('admin.contact-us.show', ['id' => $contactUs->id]);
                    }

                    return view('admin.partials.datatable-actions', $actions)->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
