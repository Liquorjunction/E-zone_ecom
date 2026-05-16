<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showrooms;
use App\Models\ShowroomImages;
use App\Http\Requests\Admin\Showrooms\DestroyShowroomRequest;
use App\Http\Requests\Admin\Showrooms\StoreShowroomRequest;
use App\Http\Requests\Admin\Showrooms\UpdateShowroomRequest;
use App\Http\Requests\Admin\Showrooms\ReadShowroomRequest;
use App\Http\Requests\Admin\Showrooms\EditShowroomRequest;
use App\Repositories\Admin\Interfaces\ShowroomRepositoryInterface as ShowroomRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShowroomController extends Controller
{
    public function index()
    {
        $showroomCount = Showrooms::count();
        return view('admin.pages.showroom.index', compact('showroomCount'));
    }

    public function show(ReadShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $id = $request->validated()['id'];
            $showroom = Showrooms::findOrFail($id);

            // Fetch Everything for Showroom->Id
            $showroom->load('images');

            return view('admin.pages.showroom.show', compact('showroom'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function create()
    {
        // Check if a showroom already exists
        if (Showrooms::count() >= 1) {
            return redirect()->route('admin.showrooms.index')->withErrors(['error' => 'Maximum 1 showroom allowed. Please edit the existing showroom instead.']);
        }

        return view('admin.pages.showroom.create');
    }

    public function store(StoreShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            // Check if a showroom already exists
            if (Showrooms::count() >= 1) {
                return redirect()->back()->withErrors(['error' => 'Maximum 1 showroom allowed. Please edit the existing showroom instead.'])->withInput();
            }

            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $attributes = ['name', 'description', 'address', 'city', 'state', 'country'];

            foreach ($attributes as $field) {
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
            // ----------------------------------

            // Passed corrected arguments
            $showroomRepo->storeShowroom($request, $data);

            return redirect()->route('admin.showrooms.index')->with('success', 'Showroom created successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditShowroomRequest $request)
    {
        try {
            $id = $request->validated()['id'];
            $showroom = Showrooms::findOrFail($id);

            return view('admin.pages.showroom.edit', compact('showroom'));
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function update(UpdateShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $id = $request->validated()['id'];
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            $attributes = ['name', 'description', 'address', 'city', 'state', 'country'];
            foreach ($attributes as $field) {
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
            // ----------------------------------

            // Passed corrected arguments
            $showroomRepo->updateShowroom($request, $id, $data);

            return redirect()->route('admin.showrooms.index')->with('success', 'Showroom updated successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            // Check if there's only 1 showroom
            if (Showrooms::count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the only showroom. At least 1 showroom must exist.',
                ], 403);
            }

            $id = $request->validated()['id'];
            $showroomRepo->deleteShowroom($id);

            return response()->json([
                'success' => true,
                'message' => 'Showroom deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }

    public function toggleStatus(EditShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $id = $request->validated()['id'];
            $showroomRepo->updateShowroomStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Showroom status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $showrooms = $showroomRepo->getDataForDataTable($request->all());
            $showroomCount = Showrooms::count();

            return DataTables::of($showrooms)
                ->addIndexColumn()
                ->addColumn('cover_image', function ($showroom) {
                    return $showroom->cover_image ? asset('storage/' . $showroom->cover_image) : '';
                })
                ->addColumn('name', function ($showroom) {
                    return $showroom->getTranslation('name', 'en');
                })
                ->addColumn('description', function ($showroom) {
                    return Str::limit($showroom->getTranslation('description', 'en'), 50, '...');
                })
                ->addColumn('action', function ($showroom) use ($request, $showroomCount) {
                    $data['view_url'] = $request->user()->can('showrooms.read') ? route('admin.showrooms.show', ['id' => $showroom->id]) : '';
                    $data['edit_url'] = $request->user()->can('showrooms.update') ? route('admin.showrooms.edit', ['id' => $showroom->id]) : '';
                    // Only allow delete if there's more than 1 showroom
                    $data['delete_url'] = ($request->user()->can('showrooms.delete') && $showroomCount > 1) ? route('admin.showrooms.destroy', ['id' => $showroom->id]) : '';
                    $data['id'] = $showroom->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->addColumn('is_active', function ($showroom) use ($request) {
                    if ($request->user()->can('showrooms.update')) {
                        $data['id'] = $showroom->id;
                        $data['toggle_url'] = $request->user()->can('showrooms.update') ? route('admin.showrooms.toggleStatus', ['id' => $showroom->id]) : '';
                        $data['status'] = $showroom->status;

                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $showroom->status])->render();
                    }
                })
                ->rawColumns(['action', 'is_active', 'cover_image', 'description'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function deleteImage(ShowroomImages $image)
    {
        try {
            // Delete file from storage
            if ($image->image && Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }

            // Delete database record
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image.',
            ], 500);
        }
    }
}
