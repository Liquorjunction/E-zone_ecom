<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            $data = $request->validated();

            // --- Smart Auto-Translate Logic ---
            foreach (['title', 'subtitle', 'button_text'] as $field) {
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

            // Pass modified data (with translations) to Repository
            // We merge it back into request or pass explicitly depending on repo design.
            // Since we updated the Repo to accept a second $data argument:
            $sliderRepo->storeSlider($request, $data);

            return redirect()->route('admin.hero-slider.index')->with('success', 'Slider created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

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
        $id = $request->input('id');
        $sliderRepo->deleteSlider($id);

        return redirect()->route('admin.hero-slider.index')->with('success', 'Slider deleted successfully.');
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

    public function data(ReadSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $sliders = $sliderRepo->getDataForDataTable($request->all());

            return DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image', function ($slider) {
                    if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                        return asset('storage/'.$slider->image);
                    }

                    return 'No Image';
                })
                ->addColumn('title', function ($slider) {
                    // Helper to show English title in the list
                    return $slider->getTranslation('title', 'en');
                })
                ->addColumn('is_active', function ($slider) use ($request) {
                    $data['toggle_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.toggleStatus', ['id' => $slider->id]) : '';
                    $data['status'] = $slider->is_active;

                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($slider) use ($request) {
                    $data['id'] = $slider->id;
                    $data['edit_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.edit', ['id' => $slider->id]) : '';
                    $data['delete_url'] = $request->user()->can('slider.delete') ? route('admin.hero-slider.destroy', ['id' => $slider->id]) : '';

                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'is_active'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in SliderController@getDataForDataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
