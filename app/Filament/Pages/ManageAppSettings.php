<?php

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageAppSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'App Settings';
    protected static ?string $slug = 'app-settings';
    protected static ?string $navigationGroup = 'System';
    protected static string $view = 'filament.pages.manage-app-settings';

    // Instead of storing the entire AppSettings object, store each field individually.
    public string $company_name;
    public ?string $company_logo_path = null;
    public string $default_timezone;

    public function mount()
    {
        // Load settings into public properties
        $settings = app(AppSettings::class);

        $this->company_name = $settings->company_name;
        $this->company_logo_path    = $settings->company_logo_path;
        $this->default_timezone = $settings->default_timezone;
    }

    /**
     * Define the form schema with these properties.
     */
    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('company_name')
                ->label('Company Name')
                ->required(),

            Forms\Components\FileUpload::make('company_logo_path')
                ->label('Logo')
                ->directory('logos')
                ->image(),

            Forms\Components\TextInput::make('default_timezone')
                ->label('Default Timezone')
                ->default('UTC'),
        ]);
    }

    /**
     * Save the updated values back to AppSettings.
     */
    public function save()
    {
        $settings = app(AppSettings::class);
        $settings->company_name = $this->company_name;
        $settings->company_logo_path    = $this->company_logo_path;
        $settings->default_timezone = $this->default_timezone;
        $settings->save();

        Notification::make()
            ->title('Settings saved.')
            ->send();
    }
}
