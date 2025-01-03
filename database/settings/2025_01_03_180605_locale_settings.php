<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('locale.default_locale', 'nb');
        $this->migrator->add('locale.default_datetime_format', 'd.m.Y H:i');
        $this->migrator->add('locale.default_date_format', 'd.m.Y');
    }
};
