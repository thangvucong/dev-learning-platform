<?php

use Carbon\Carbon;

if (! function_exists('format_price')) {
    /**
     * Format price value for UI display.
     *
     * @param  mixed  $amount
     * @param  string  $currencySymbol
     * @param  int  $decimals
     * @return string|null
     */
    function format_price($amount, string $currencySymbol = 'đ', int $decimals = 0): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $numericAmount = (float) $amount;
        $formattedAmount = number_format($numericAmount, $decimals, ',', '.');

        return trim($formattedAmount . ' ' . $currencySymbol);
    }
}

if (! function_exists('time_ago')) {
    /**
     * Format datetime value to human-readable diff (e.g. "6 phút trước").
     *
     * @param  mixed  $datetime
     * @return string|null
     */
    function time_ago($datetime): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        return Carbon::parse($datetime)->locale('vi')->diffForHumans();
    }
}
