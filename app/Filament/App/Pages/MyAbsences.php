<?php

namespace App\Filament\App\Pages;

use App\Enums\AbsenceStatus;
use App\Filament\App\Resources\AbsenceResource\Pages\ViewAbsence;
use App\Models\Absence;
use App\Models\AbsenceType;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

class MyAbsences extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'My Absences';
//    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Absence & Holidays';
    protected static ?string $slug = 'my-absences';

    /**
     * The Blade view for this page
     * (We'll create `resources/views/filament/pages/my-absences.blade.php`)
     */
    protected static string $view = 'filament.app.pages.my-absences';

    /**
     * If you want to show a stats widget at the top, you can define:
     */
    public function getHeaderWidgets(): array
    {
        // We'll create a MyAbsencesStats widget below
        return [
            \App\Filament\App\Widgets\MyAbsencesStats::class,
        ];
    }

    /**
     * This is the query for the table to show only the logged-in user's absences.
     */
    protected function getTableQuery(): Builder
    {
        return Absence::byPerson()
            ->orderBy('start_date', 'desc');
    }

    /**
     * Define columns to display in the table.
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('absenceType.name')
                ->label('Type')
                ->searchable(),

            Tables\Columns\TextColumn::make('start_date')
                ->label('Start')
                ->dateTime()
                ->sortable(),

            Tables\Columns\TextColumn::make('end_date')
                ->label('End')
                ->dateTime()
                ->sortable(),

            // Example booleans:
            Tables\Columns\IconColumn::make('is_medically_certified')
                ->label('Doctor’s Note?')
                ->boolean()
                ->toggleable(),

            Tables\Columns\IconColumn::make('occupational')
                ->label('Work-related?')
                ->boolean()
                ->toggleable(),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->translateLabel()
                ->badge()
        ];
    }

    /**
     * (Optional) If you want search, sorting, or filters, you can define them:
     */
    protected function getTableFilters(): array
    {
        return [
            // For instance, a date range filter or "is paid?" filter
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make()
                ->url(fn (Absence $record) => ViewMyAbsence::getUrl([$record])),
            Tables\Actions\EditAction::make()
                ->visible(fn ($record) => $record->status === AbsenceStatus::Requested )
                ->form(
                    fn (Form $form) => $form->schema([
                        Components\DatePicker::make('start_date'),
                        Components\DatePicker::make('end_date'),
                    ])->columns()
                ),
            Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->status === AbsenceStatus::Requested ),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            // Tables\Actions\DeleteBulkAction::make(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('requestHoliday')
                ->label('Request Holiday')
                ->button() // or ->icon('heroicon-o-sun'), etc.
                ->color('success')
                // Build a form inside the modal
                ->form([
                    Components\DatePicker::make('start_date')
                        ->required()
                        ->label('Start Date'),

                    Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->default(null),

                    Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2),
                ])
                ->modalHeading('Request Holiday')
                ->modalSubmitActionLabel('Submit Request')
                ->action(function (array $data): void {
                    if(\auth()->user()->can('absence_request')) {
                        $holidayTypeId = AbsenceType::getDefaultHolidaysType()->id;

                        $user = Auth::user();
                        $person = $user?->person;

                        $absence = Absence::create([
                            'person_id' => $person->id,
                            'absence_type_id' => $holidayTypeId,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'] ?? null,
                            'notes' => $data['notes'] ?? null,
                            'status' => AbsenceStatus::Requested->value,
                            'is_paid' => true,
                            'is_medically_certified' => false,
                        ]);

                        Notification::make()->title('Holiday request submitted successfully!')->send();
                        $notifiedManager = Absence::getNotificationRecipient();
                        $notifiedManager->notify(
                            Notification::make()
                                ->title('Holiday request')
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('View')
                                        ->url(ViewAbsence::getUrl(['record' => $absence]))
                                        ->button()
                                        ->markAsRead()
                                ])
                                ->toDatabase()
                        );
                    }
                })
                ->visible(fn () => \auth()->user()->can('absence_request')),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('absence_view_own');
    }
}
