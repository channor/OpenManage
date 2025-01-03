<?php

namespace App\Filament\App\Pages;

use App\Settings\LocaleSettings;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageLocaleSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Locale Settings';
    protected static ?string $slug = 'locale-settings';
    protected static ?string $navigationGroup = 'Setting and administration';
    protected static string $view = 'filament.app.pages.manage-locale-settings';

    // Instead of storing the entire AppSettings object, store each field individually.
    public string $default_locale;
    public string $default_datetime_format;
    public string $default_date_format;
    public bool $use_24_hour_clock;

    public function mount()
    {
        // Load settings into public properties
        $settings = app(LocaleSettings::class);

        $this->default_locale = $settings->default_locale;
        $this->default_date_format = $settings->default_date_format;
        $this->default_datetime_format = $settings->default_datetime_format;
        $this->use_24_hour_clock = $settings->use_24_hour_clock;
    }

    /**
     * Define the form schema with these properties.
     */
    public function form(Forms\Form $form): Forms\Form
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $timezoneOptions = array_combine($timezones, $timezones);

        return $form->schema([
            Forms\Components\TextInput::make('default_locale')
                ->label('Default locale 2-letter')
                ->maxLength(2)
                ->required(),
            Forms\Components\TextInput::make('default_date_format')
                ->label('Default date format')
                ->required(),
            Forms\Components\TextInput::make('default_datetime_format')
                ->label('Default datetime format')
                ->required(),
            Forms\Components\Toggle::make('use_24_hour_clock')
                ->label('Use 24-hour clock')
                ->required(),
        ]);
    }

    /**
     * Save the updated values back to AppSettings.
     */
    public function save()
    {
        $settings = app(LocaleSettings::class);
        $settings->default_locale = $this->default_locale;
        $settings->default_date_format    = $this->default_date_format;
        $settings->default_datetime_format = $this->default_datetime_format;
        $settings->use_24_hour_clock = $this->use_24_hour_clock;
        $settings->save();

        Notification::make()
            ->title('Settings saved.')
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('manage_settings');
    }
}
