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

if (!function_exists('update_mail_config')) {
    /**
     * Update mail configuration from database SMTP settings
     * Loads active SMTP settings and applies them to Laravel's mail config
     *
     * @return void
     */
    function update_mail_config()
    {
        $smtpSetting = \App\Models\SmtpSetting::where('is_active', true)->first();

        if ($smtpSetting) {
            config([
                'mail.default' => $smtpSetting->mail_driver,
                'mail.mailers.smtp.host' => $smtpSetting->mail_host,
                'mail.mailers.smtp.port' => $smtpSetting->mail_port,
                'mail.mailers.smtp.username' => $smtpSetting->mail_username,
                'mail.mailers.smtp.password' => $smtpSetting->mail_password,
                'mail.mailers.smtp.encryption' => $smtpSetting->mail_encryption,
                'mail.from.address' => $smtpSetting->mail_from_address,
                'mail.from.name' => $smtpSetting->mail_from_name,
            ]);
        }
    }
}
