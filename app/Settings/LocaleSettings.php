<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LocaleSettings extends Settings
{
    public string $default_locale;
    public string $default_datetime_format;
    public string $default_date_format;
    public bool $use_24_hour_clock;

    public static function group(): string
    {
        return 'locale';
    }

    public function getDateTimeFormatWithClock(): string
    {
        return $this->use_24_hour_clock
            ? $this->default_date_format . ' H:i'
            : $this->default_date_format . ' h:i A';
    }

}
