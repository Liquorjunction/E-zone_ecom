<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\BatchDestroySliderRequest;
use App\Http\Requests\Admin\Slider\BatchUpdateStatusSliderRequest;
use App\Http\Requests\Admin\Slider\DestroySliderRequest;
use App\Http\Requests\Admin\Slider\EditSliderRequest;
use App\Http\Requests\Admin\Slider\ReadSliderRequest;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Models\HeroSlider;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface as SliderRepo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    public function index()
    {
        return view('admin.pages.slider.index');
    }

    public function create()
    {
        return view('admin.pages.slider.create');
    }

    public function store(StoreSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            // --- Debug: Check if file present ---
            if (!$request->hasFile('image')) {
                Log::error('Slider upload: No file uploaded');
                return redirect()->back()->withErrors(['error' => 'No file uploaded'])->withInput();
            }
            $image = $request->file('image');
            if (!$image) {
                Log::error('Slider upload: File object is null');
                return redirect()->back()->withErrors(['error' => 'File object is null'])->withInput();
            }
            // --- Debug: File info ---
            Log::info('Slider image upload debug', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime' => $image->getMimeType(),
                'isValid' => $image->isValid(),
                'error' => $image->getError(),
                'errorMessage' => $image->getErrorMessage(),
                'extension' => $image->getClientOriginalExtension(),
                'request_files' => $request->allFiles(),
                'request_data' => $request->all(),
            ]);
            if (!$image->isValid()) {
                Log::error('Slider upload: File upload error: ' . $image->getErrorMessage());
                return redirect()->back()->withErrors(['error' => 'File upload error: ' . $image->getErrorMessage()])->withInput();
            }

            $data = $request->validated();
            Log::info('Slider validated data', $data);

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'subtitle', 'button_text'] as $field) {
                // Skip if English value is missing (should be caught by validation, but safe check)
                if (! isset($data[$field]['en'])) {
                    Log::warning('Slider upload: Missing EN value for ' . $field);
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
            Log::info('Slider data after translation', $data);
            // ----------------------------------

            // Pass modified data (with translations) to Repository
            $result = $sliderRepo->storeSlider($request, $data);
            Log::info('SliderRepo storeSlider result', ['result' => $result]);

            return redirect()->route('admin.hero-slider.index')->with('success', 'Slider created successfully.');
        } catch (\Exception $e) {
            Log::error('Slider upload exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(EditSliderRequest $request)
    {
        $id = $request->input('id');
        $slider = HeroSlider::findOrFail($id);

        return view('admin.pages.slider.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'subtitle', 'button_text'] as $field) {
                if (! isset($data[$field]['en'])) {
                    continue;
                }

                if (empty($data[$field]['fr'])) {
                    $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
                }
                if (empty($data[$field]['ar'])) {
                    $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
                }
            }
            // ----------------------------------

            $sliderRepo->updateSlider($request, $id, $data);

            return redirect()->route('admin.hero-slider.index')->with('success', 'Slider updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroySliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $id = $request->input('id');
            $sliderRepo->deleteSlider($id);

            return response()->json([
                'success' => true,
                'message' => 'Slider deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting slider: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete',
            ], 500);
        }
    }

    public function batchDestroy(BatchDestroySliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $ids = $request->input('ids');
            $sliderRepo->batchDeleteSliders($ids);

            // Check if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Selected sliders deleted successfully.',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Selected sliders deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(EditSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $id = $request->input('id');
            $sliderRepo->updateStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'Slider status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function batchUpdateStatus(BatchUpdateStatusSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $ids = $request->input('ids');
            $isActive = (bool) $request->input('is_active');
            $sliderRepo->batchUpdateStatus($ids, $isActive);

            return response()->json([
                'success' => true,
                'message' => 'Selected sliders status updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function data(ReadSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $sliders = $sliderRepo->getDataForDataTable($request->all());
            $activeCount = HeroSlider::where('is_active', true)->count();

            return DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('batch_checkbox', function ($slider) {
                    return '<input type="checkbox" class="slider-checkbox" value="' . $slider->id . '">';
                })
                ->addColumn('image', function ($slider) {
                    if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                        return asset('storage/' . $slider->image);
                    }

                    return 'No Image';
                })
                ->addColumn('title', function ($slider) {
                    // Helper to show English title in the list
                    return $slider->getTranslation('title', 'en');
                })
                ->addColumn('is_active', function ($slider) use ($request, $activeCount) {
                    if ($request->user()->can('slider.update')) {
                        $data['id'] = $slider->id;
                        $data['toggle_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.toggleStatus', ['id' => $slider->id]) : '';
                        $data['status'] = (bool) $slider->is_active;
                        $data['is_only_active'] = ($activeCount === 1 && (bool) $slider->is_active);

                        return view('admin.partials.sliderdatatable-status', $data)->render();
                    } else {
                        return view('components.datatable-status-badge', ['status' => $slider->is_active])->render();
                    }
                })
                ->addColumn('action', function ($slider) use ($request) {
                    $data = ['id' => $slider->id];
                    $data['edit_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.edit', ['id' => $slider->id]) : '';
                    $data['delete_url'] = $request->user()->can('slider.delete') ? route('admin.hero-slider.destroy', ['id' => $slider->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['batch_checkbox', 'action', 'image', 'is_active'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in SliderController@getDataForDataTable: ' . $e->getMessage());
            throw $e;
        }
    }
}
