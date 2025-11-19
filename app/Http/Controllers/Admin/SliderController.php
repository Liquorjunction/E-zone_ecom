<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
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
        return view('admin.slider.index');
    }

    public function create()
    {
        return view('admin.slider.create');
    }

    public function store(StoreSliderRequest $request, SliderRepo $sliderRepo)
    {
        try {
            $sliderRepo->storeSlider($request);
            return redirect()->route('admin.slider.index')->with('success', 'Slider created successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $slider = HeroSlider::findOrFail($id);
        return view('admin.slider.edit', compact('slider'));
    }

    public function update(StoreSliderRequest $request, $id, SliderRepo $sliderRepo)
    {
        try {
            $sliderRepo->updateSlider($request, $id);
            return redirect()->route('admin.slider.index')->with('success', 'Slider updated successfully.');
        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id, SliderRepo $sliderRepo)
    {
        $sliderRepo->deleteSlider($id);
        return redirect()->route('admin.slider.index')->with('success', 'Slider deleted successfully.');
    }

    public function updateStatus($id, SliderRepo $sliderRepo)
    {
        $sliderRepo->updateStatus($id);
        return redirect()->route('admin.slider.index')->with('success', 'Slider status updated successfully.');
    }

    public function getDataForDataTable(SliderRepo $sliderRepo)
    {
        try {
            $sliders = $sliderRepo->getDataForDataTable();
            $getTable = DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image', function ($slider) {
                    if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                        return asset('storage/' . $slider->image);
                    }
                })
                ->addColumn('is_active', function ($slider) {
                    $data['toggle_url'] = route('admin.slider.toggleStatus', $slider->id);
                    $data['is_active'] = $slider->is_active;
                    return view('admin.partials.datatable-status', $data)->render();
                })
                ->addColumn('action', function ($slider) {
                    $data['show_url'] = route('admin.slider.show', $slider->id);
                    $data['edit_url'] = route('admin.slider.edit', $slider->id);
                    $data['delete_url'] = route('admin.slider.destroy', $slider->id);
                    return view('admin.partials.datatable-actions', $data)->render();
                })
                ->rawColumns(['action'])
                ->make(true);

            return $getTable;
        } catch (\Exception $e) {
            Log::error('Error in SliderController@getDataForDataTable: '.$e->getMessage());
            throw $e;
        }
    }
}
