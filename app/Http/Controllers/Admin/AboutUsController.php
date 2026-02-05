<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AboutUs\DestroyAboutUsRequest;
use App\Http\Requests\Admin\AboutUs\EditAboutUsRequest;
use App\Http\Requests\Admin\AboutUs\ReadAboutUsRequest;
use App\Http\Requests\Admin\AboutUs\StoreAboutUsRequest;
use App\Http\Requests\Admin\AboutUs\UpdateAboutUsRequest;
use App\Models\AboutUs;
use App\Repositories\Admin\Interfaces\AboutUsRepositoryInterface as AboutUsRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AboutUsController extends Controller
{
    public function index()
    {
        return view('admin.pages.about_us.index');
    }

    public function create()
    {
        return view('admin.pages.about_us.create');
    }

    public function store(StoreAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'description'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }

                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // ---------------------------------

            $aboutUsRepo->storeAboutUsData($request, $data); // Pass request and data array

            return redirect()->route('admin.about_us.index')->with('success', 'About Us created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating About Us: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating About Us.');
        }
    }

    public function edit(EditAboutUsRequest $request)
    {
        try {
            $id = $request->validated()['id'];
            $aboutUs = AboutUs::findOrFail($id);

            return view('admin.pages.about_us.edit', compact('aboutUs'));
        } catch (\Exception $e) {
            Log::error('Error fetching About Us for edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching About Us details.');
        }
    }

    public function update(UpdateAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'description'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans_html($data[$field]['en'], 'fr');
                }

                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans_html($data[$field]['en'], 'ar');
                }
            }
            // ---------------------------------

            $aboutUsRepo->updateAboutUsData($id, $request, $data); // Pass id, request and data array

            return redirect()->route('admin.about_us.index')->with('success', 'About Us updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating About Us: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating About Us details.');
        }
    }

    public function destroy(DestroyAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $aboutUsRepo->destroyAboutUsData($id);

            return response()->json([
                'success' => true,
                'message' => 'About Us deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting About Us: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting About Us.',
            ], 500);
        }
    }

    public function toggleStatus(EditAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $id = $request->validated()['id'];
            $aboutUsRepo->toggleAboutUsStatus($id);

            return redirect()->route('admin.about_us.index')->with('success', 'About Us status toggled successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling About Us status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while toggling About Us status.');
        }
    }

    public function data(ReadAboutUsRequest $request, AboutUsRepo $aboutUsRepo)
    {
        try {
            $data = $aboutUsRepo->getDataForDataTable($request);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    // Fetch the title in english
                    $title = $data->getTranslation('title', 'en');
                    return $title;
                })
                ->addColumn('description', function ($data) {
                    // Fetch the description in english
                    $description = $data->getTranslation('description', 'en');
                    // Limit the description to 50 characters and its in html
                    return strlen(strip_tags($description)) > 50 ? substr(strip_tags($description), 0, 50) . '...' : strip_tags($description);
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('status', function ($data) use ($request) {
                    if ($request->user()->can('about_us.update')) {
                       $data['toggle_url'] = $request->user()->can('about_us.update') ? route('admin.about_us.toggleStatus', ['id' => $data->id]) : '';
                        $data['status'] = $data->status;
                        $data['id'] = $data->id;
                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $data->status])->render();
                    }
                })
                ->addColumn('actions', function ($data) use ($request) {
                    $data['id'] = $data->id;
                    $data['edit_url'] = $request->user()->can('about_us.update') ? route('admin.about_us.edit', ['id' => $data->id]) : '';
                    $data['delete_url'] = $request->user()->can('about_us.delete') ? route('admin.about_us.destroy', ['id' => $data->id]) : '';
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['description', 'status', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fetching About Us data for DataTable: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }
}
