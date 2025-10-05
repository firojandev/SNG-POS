<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load general settings from database dynamically
        try {
            $connection = DB::connection()->getPdo();
            if ($connection) {
                $allOptions = [];
                $allOptions['general_settings'] = Option::all()->pluck('value', 'key')->toArray();
                config($allOptions);
            }
        } catch (\Exception $e) {
            //
        }
    }
}
