<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AbsenceSettings extends Settings
{
    public string $default_holidays_name;

    public string $default_own_illness_name;

    public static function group(): string
    {
        return 'absence';
    }
}
