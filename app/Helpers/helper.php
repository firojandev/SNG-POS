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
