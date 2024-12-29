<?php

namespace App\Filament\App\Pages;

use App\Settings\AbsenceSettings;
use App\Settings\AppSettings;
use Carbon\CarbonTimeZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAbsenceSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Absence Settings';
    protected static ?string $slug = 'absence-settings';
    protected static ?string $navigationGroup = 'System';

    protected static string $view = 'filament.app.pages.manage-absence-settings';

    public string $default_holidays_name;

    public string $default_own_illness_name;

    public function mount()
    {
        $settings = app(AbsenceSettings::class);

        $this->default_holidays_name = $settings->default_holidays_name;
        $this->default_own_illness_name = $settings->default_own_illness_name;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('default_holidays_name')
                ->label('Default holidays name')
                ->required(),
            Forms\Components\TextInput::make('default_own_illness_name')
                ->label('Default own illness name')
                ->required(),
        ]);
    }

    public function save()
    {
        $settings = app(AbsenceSettings::class);
        $settings->default_own_illness_name = $this->default_own_illness_name;
        $settings->default_holidays_name = $this->default_holidays_name;
        $settings->save();

        Notification::make()
            ->title('Settings saved.')
            ->send();
    }
}
