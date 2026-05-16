<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Categories;
use Illuminate\Support\Facades\Cache;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $sidebarCategories = Cache::remember('sidebar_categories', 60, function () {
            return Categories::with(['subcategories' => function($query) {
                                $query->where('status', 'active');
                            }])
                            ->where('status', 'active')
                            ->get();
        });

        $view->with('sidebarCategories', $sidebarCategories);
    }
}
