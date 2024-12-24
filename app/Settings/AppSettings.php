<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AppSettings extends Settings
{
    public string $company_name;
    public ?string $company_logo_path;
    public string $default_timezone;

    public static function group(): string
    {
        // The group name used in the `settings` table, e.g. 'app'
        return 'app';
    }
}
