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
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ShowroomController extends Controller
{
    public function index()
    {
        return view('admin.pages.showroom.index');
    }

    public function show(ReadShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $id = $request->validated()['id'];
            $showroom = Showrooms::findOrFail($id);

            // Fetch Everything for Showroom->Id
            $showroom->images = ShowroomImages::where('showroom_id', $showroom->id)->get();

            return view('admin.pages.showroom.show', compact('showroom'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function create()
    {
        return view('admin.pages.showroom.create');
    }

    public function store(StoreShowroomRequest $request, ShowroomRepo $showroomRepo)
    {
        try {
            $data = $request->validated();

             // --- Smart Auto-Translate Logic ---
            $attributes = ['name', 'description', 'address', 'city', 'state' ,'country'];

            foreach ($attributes as $field)
            {
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
            $attributes = ['name', 'description', 'address', 'city', 'state' ,'country'];
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
            $id = $request->validated()['id'];
            $showroomRepo->deleteShowroom($id);

            return redirect()->route('admin.showrooms.index')->with('success', 'Showroom deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
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
                ->addColumn('action', function ($showroom) use ($request) {
                    $data['view_url'] = $request->user()->can('showrooms.read') ? route('admin.showrooms.show', ['id' => $showroom->id]) : '';
                    $data['edit_url'] = $request->user()->can('showrooms.update') ? route('admin.showrooms.edit', ['id' => $showroom->id]) : '';
                    $data['delete_url'] = $request->user()->can('showrooms.delete') ? route('admin.showrooms.destroy', ['id' => $showroom->id]) : '';
                    $data['id'] = $showroom->id;

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->addColumn('is_active', function ($showroom) use ($request) {
                    $data['toggle_url'] = $request->user()->can('showrooms.update') ? route('admin.showrooms.toggleStatus', ['id' => $showroom->id]) : '';
                    $data['status'] = $showroom->status;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
