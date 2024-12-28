<?php

namespace App\Filament\App\Pages;

use App\Models\Absence;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $person = $user?->person;
        // 'person' is a hasOne relationship on User => Person
        // 'person_id' is on the absences table

        // If no person, return an empty query
        if (! $person) {
            return Absence::query()->whereRaw('1=0');
        }

        return Absence::query()->where('person_id', $person->id);
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

            Tables\Columns\TextColumn::make('notes')
                ->label('Notes')
                ->limit(50)
                ->toggleable(),

            // Example booleans:
            Tables\Columns\IconColumn::make('is_medically_certified')
                ->label('Doctor’s Note?')
                ->boolean()
                ->toggleable(),

            Tables\Columns\IconColumn::make('occupational')
                ->label('Work-related?')
                ->boolean()
                ->toggleable(),
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
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make(),
        ];
    }
}
