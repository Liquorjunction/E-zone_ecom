<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\DestroySliderRequest;
use App\Http\Requests\Admin\Slider\ReadSliderRequest;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Models\HeroSlider;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface as SliderRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Config\Exception\ValidationException;
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
            $sliderRepo->storeSlider($request);
            return redirect()->route('admin.hero-slider.index')->with('success', 'Slider created successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $slider = HeroSlider::findOrFail($id);
        return view('admin.pages.slider.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, $id, SliderRepo $sliderRepo)
    {
        try {
            $sliderRepo->updateSlider($request, $id);
            return redirect()->route('admin.hero-slider.index')->with('success', 'Slider updated successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroySliderRequest $request, $id, SliderRepo $sliderRepo)
    {
        $sliderRepo->deleteSlider($id);
        return redirect()->route('admin.hero-slider.index')->with('success', 'Slider deleted successfully.');
    }

    public function updateStatus($id, SliderRepo $sliderRepo)
    {
        $sliderRepo->updateStatus($id);
        return redirect()->route('admin.hero-slider.index')->with('success', 'Slider status updated successfully.');
    }

    public function data(ReadSliderRequest $request,SliderRepo $sliderRepo)
    {
        try {
            $sliders = $sliderRepo->getDataForDataTable($request->all());
            $getTable = DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image', function ($slider) {
                    if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                        return asset('storage/' . $slider->image);
                    }
                })
                ->addColumn('is_active', function ($slider) use ($request) {
                    $data['toggle_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.toggleStatus', ['id' => $slider->id]) : '';
                    $data['status'] = $slider->is_active;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($slider) use ($request) {
                    $data['edit_url'] = $request->user()->can('slider.update') ? route('admin.hero-slider.edit', ['id' => $slider->id]) : '';
                    $data['delete_url'] = $request->user()->can('slider.delete') ? route('admin.hero-slider.destroy', ['id' => $slider->id]) : '';
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action', 'image', 'is_active'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in SliderController@getDataForDataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
