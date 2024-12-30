<?php

namespace App\Providers;

use App\Settings\AppSettings;
use Illuminate\Support\ServiceProvider;

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
        $settings = app(AppSettings::class);

        // This actually sets the 'app.timezone' config for the current request/CLI run.
//        config(['app.timezone' => $settings->default_timezone]);

        // This tells PHP itself to use the new timezone.
//        date_default_timezone_set($settings->default_timezone);
    }
}
