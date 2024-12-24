<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.company_name', 'My Company Name');
        $this->migrator->add('app.company_logo_path', '');
        $this->migrator->add('app.default_timezone', 'Europe/Oslo');
    }
};
