<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application General Settings
    |--------------------------------------------------------------------------
    |
    | This file contains all the general settings for the application.
    | These settings are loaded from the database options table dynamically.
    | The actual values are loaded via the AppServiceProvider to avoid
    | database calls during config caching.
    |
    */

    // Default values - will be overridden by database values in AppServiceProvider
    'app_name' => 'SNG POS',
    'app_address' => '',
    'app_phone' => '',
    'date_format' => 'Y-m-d',
    'app_logo' => '',
    'app_favicon' => '',
    'app_currency' => '$',
    
    // Additional settings
    'timezone' => 'UTC',
    'language' => 'en',
    'items_per_page' => 10,
    'tax_calculation' => 'exclusive',
    'decimal_places' => 2,
    'currency_position' => 'before', // before or after
];
