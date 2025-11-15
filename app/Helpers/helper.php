<?php

if (!function_exists('get_option')) {
    /**
     * Get application setting value by key from config
     *
     * @param string $option_key
     * @return mixed
     */
    function get_option($option_key)
    {
        $system_settings = config('general_settings');

        if ($option_key && isset($system_settings[$option_key])) {
            return $system_settings[$option_key];
        } else {
            return '';
        }
    }
}

if (!function_exists('format_currency_for_pdf')) {
    /**
     * Format currency symbol for PDF rendering
     * Converts special Unicode characters to HTML entities for DomPDF compatibility
     *
     * @param string $currency
     * @return string
     */
    function format_currency_for_pdf($currency = null)
    {
        if ($currency === null) {
            $currency = get_option('app_currency');
        }

        // Map of currency symbols to HTML entities for PDF compatibility
        $currencyMap = [
            '৳' => '&#2547;',  // Bengali Taka
            '₹' => '&#8377;',  // Indian Rupee
            '€' => '&#8364;',  // Euro
            '£' => '&#163;',   // British Pound
            '¥' => '&#165;',   // Japanese Yen
            '₩' => '&#8361;',  // South Korean Won
            '₽' => '&#8381;',  // Russian Ruble
            '₪' => '&#8362;',  // Israeli Shekel
            '元' => '&#20803;', // Chinese Yuan
            '¢' => '&#162;',   // Cent
        ];

        return $currencyMap[$currency] ?? $currency;
    }
}
