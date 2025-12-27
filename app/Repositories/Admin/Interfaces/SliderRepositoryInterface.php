<?php

namespace App\Repositories\Admin\Interfaces;

interface SliderRepositoryInterface
{
    public function getAllSliders();
    public function getActiveSliders();
    public function getSliderById($id);
    public function storeSlider($request);
    public function updateSlider($request, $id);
    public function deleteSlider($id);
    public function updateStatus($id);
    public function getDataForDataTable();
}
