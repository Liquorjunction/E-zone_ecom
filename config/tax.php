<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    |
    | Configure tax settings for the application
    |
    */

    /**
     * Tax rate percentage (Morocco VAT)
     */
    'rate' => env('TAX_RATE', 20.0),

    /**
     * Tax is included in product prices
     * When true, tax is extracted from the price (reverse calculation)
     * When false, tax is added on top of the price
     */
    'included_in_price' => env('TAX_INCLUDED_IN_PRICE', true),

    /**
     * Tax label for display
     */
    'label' => env('TAX_LABEL', 'VAT'),

];
