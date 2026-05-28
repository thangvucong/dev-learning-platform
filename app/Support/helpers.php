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

        return trim($formattedAmount . $currencySymbol);
    }
}

if (! function_exists('media_url')) {
    /**
     * Normalize stored media paths for browser rendering.
     *
     * @param  mixed  $path
     * @param  string|null  $fallback
     * @return string|null
     */
    function media_url($path, ?string $fallback = null): ?string
    {
        $value = trim((string) $path);

        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^(https?:)?\/\//i', $value) || strpos($value, 'data:') === 0) {
            return $value;
        }

        if (strpos($value, '/storage/') === 0) {
            return url($value);
        }

        if (strpos($value, 'storage/') === 0) {
            return asset($value);
        }

        if (strpos($value, 'public/') === 0) {
            return asset(substr($value, 7));
        }

        if (preg_match('/^(avatars|posts|courses|uploads)\//', $value)) {
            return asset('storage/' . $value);
        }

        if (strpos($value, '/') === 0) {
            return url($value);
        }

        return asset($value);
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
