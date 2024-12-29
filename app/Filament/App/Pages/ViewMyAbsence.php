<?php

namespace App\Filament\App\Pages;

use App\Models\Absence;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;

class ViewMyAbsence extends Page
{
    // Optional: title, navigation label/icon if you want the page in the sidebar
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // Where the Blade file for this page lives:
    protected static string $view = 'filament.app.pages.view-my-absence';

    // The record we’ll display
    public ?Model $record = null;

    protected static bool $isDiscovered = false;

    public function mount(Absence $record): void
    {
        $user = auth()->user();
        $person = $user?->person;

        if (! $person || $record->person_id !== $person->id) {
            abort(403, 'You cannot view this absence.');
        }

        $this->record = $record;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make($this->record->absenceType->name ?? 'My Absence')
                ->schema([
                    TextEntry::make('start_date')
                        ->label('Start Date')
                        ->state(fn () => $this->record->start_date?->format('Y-m-d')),

                    TextEntry::make('end_date')
                        ->label('End Date')
                        ->state(fn () => $this->record->end_date?->format('Y-m-d')),

                    TextEntry::make('estimated_end_date')
                        ->label('Estimated End')
                        ->state(fn () => $this->record->estimated_end_date?->format('Y-m-d')),

                    TextEntry::make('notes')
                        ->label('Notes')
                        ->state(fn () => $this->record->notes),
                ]),
        ]);
    }
}
