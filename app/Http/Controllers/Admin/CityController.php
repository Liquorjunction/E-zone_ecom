<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;
use App\Http\Requests\Admin\City\DestroyCityRequest;
use App\Http\Requests\Admin\City\EditCityRequest;
use App\Http\Requests\Admin\City\ReadCityRequest;
use App\Repositories\Admin\Interfaces\CitiesRepositoryInterface as CitiesRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
    public function index()
    {
        try {
            return view('admin.pages.cities.index');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Failed to load cities index view.');
        }
    }

    public function create()
    {
        try {
            return view('admin.pages.cities.create');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Failed to load cities create view.');
        }
    }

    public function store(StoreCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['city_name', 'country_name'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }
                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $cityRepo->createCity($request, $data);

            return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to create city.');
        }
    }

    public function edit(ReadCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $city = $cityRepo->getAllCities($request);

            return view('admin.pages.cities.edit', compact('city'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Failed to load city edit view.');
        }
    }

    public function update(UpdateCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['city_name', 'country_name'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                // Auto-fill French if empty
                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }
                // Auto-fill Arabic if empty
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $cityRepo->updateCity($request, $id, $data);

            return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to update city.');
        }
    }

    public function destroy(DestroyCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $id = $request->input('id');
            $cityRepo->deleteCity($id);

            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }

    public function toggleStatus(EditCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $id = $request->input('id'); // Get ID from the request body/query
            $cityRepo->toggleCityStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'City status updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadCityRequest $request, CitiesRepo $cityRepo)
    {
        try {
            $cities = $cityRepo->getDataforDataTable($request->all());

            return DataTables::of($cities)
                ->addIndexColumn()
                ->addColumn('city_name', function ($city) {
                    // Helper to show English title in the list
                    return $city->getTranslation('city_name', 'en');
                })
                ->addColumn('country_name', function ($city) {
                    // Helper to show English title in the list
                    return $city->getTranslation('country_name', 'en');
                })
                ->addColumn('status', function ($city) use ($request) {
                    if ($request->user()->can('cities.update')) {
                        $data['id'] = $city->id;
                        $data['toggle_url'] = $request->user()->can('cities.update') ? route('admin.cities.toggleStatus', ['id' => $city->id]) : '';
                        $data['status'] = $city->status;

                        return view('admin.partials.datatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $city->status])->render();
                    }
                })
                ->addColumn('action', function ($city) use ($request) {
                    $data['id'] = $city->id;
                    $data['edit_url'] = $request->user()->can('cities.update') ? route('admin.cities.edit', ['id' => $city->id]) : '';

                    if ($request->user()->can('cities.delete')) {
                        $data['delete_url'] = route('admin.cities.destroy', ['id' => $city->id]);
                    } else {
                        $data['delete_url'] = '';
                    }

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
