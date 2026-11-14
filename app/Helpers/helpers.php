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

if (! function_exists('format_business_hours')) {
    /**
     * Format opening hours range localized by locale.
     *
     * @param  string|null  $openTime
     * @param  string|null  $closeTime
     * @param  string|null  $locale
     * @return string
     */
    function format_business_hours($openTime, $closeTime, $locale = null)
    {
        if (empty($openTime) || empty($closeTime)) {
            return '';
        }

        $locale = $locale ?: app()->getLocale();

        try {
            $openCarbon = \Carbon\Carbon::parse($openTime);
            $closeCarbon = \Carbon\Carbon::parse($closeTime);

            if ($locale === 'ar') {
                $openFormatted = $openCarbon->locale('ar')->translatedFormat('g:i A');
                $closeFormatted = $closeCarbon->locale('ar')->translatedFormat('g:i A');
                return "\u{200E}" . $openFormatted . "\u{200E} \u{200E}–\u{200E} \u{200E}" . $closeFormatted . "\u{200E}";
            }

            if ($locale === 'fr') {
                return $openCarbon->format('H:i') . ' – ' . $closeCarbon->format('H:i');
            }

            return $openCarbon->format('g:i A') . ' – ' . $closeCarbon->format('g:i A');
        } catch (\Throwable $e) {
            if ($locale === 'ar') {
                return "\u{200E}" . $openTime . "\u{200E} \u{200E}–\u{200E} \u{200E}" . $closeTime . "\u{200E}";
            }
            return $openTime . ' – ' . $closeTime;
        }
    }
}
