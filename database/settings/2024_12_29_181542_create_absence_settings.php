<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('absence.default_holidays_name', 'Ferie');
        $this->migrator->add('absence.default_own_illness_name', 'Egen sykdom');
    }
};
