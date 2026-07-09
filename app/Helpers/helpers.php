<?php

if (! function_exists('money_format_number')) {
    /**
     * Format number as currency with optional symbol.
     *
     * @param  float|int|null  $amount
     * @param  int  $decimals
     * @param  string|null  $symbol
     * @return string
     */
    function money_format_number($amount, $decimals = 2, $symbol = null)
    {
        $amount = $amount ?? 0;
        $symbol = $symbol ?? env('CURRENCY_SYMBOL', config('app.CURRENCY_SYMBOL', '$'));

        $formatted = number_format((float) $amount, $decimals, '.', ',');
        return ($symbol !== null && $symbol !== '') ? $symbol . ' ' . $formatted : $formatted;
    }
}
