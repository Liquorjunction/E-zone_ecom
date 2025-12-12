<?php

namespace App\View\Composers;

use App\Helpers\ClientHelper;
use Illuminate\View\View;

class ClientComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Fetch active brands and pass to all client views
        $activeBrands = ClientHelper::getActiveBrands();
        $view->with('activeBrands', $activeBrands);
    }
}
