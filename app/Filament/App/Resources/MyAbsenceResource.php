<?php

namespace App\Filament\App\Resources;

use App\Enums\AbsenceCategory;
use App\Enums\AbsenceStatus;
use App\Filament\App\Resources\MyAbsenceResource\Pages;
use App\Filament\App\Resources\MyAbsenceResource\RelationManagers;
use App\Models\AbsenceType;
use App\Models\MyAbsence;
use App\Settings\LocaleSettings;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyAbsenceResource extends Resource
{
    protected static ?string $model = MyAbsence::class;

    protected static ?int $navigationSort = -3;

    public static function getNavigationLabel(): string
    {
        return __("My absences");
    }

    public static function getNavigationGroup(): string
    {
        return 'Absence & Holidays';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->byPerson();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('absence_type_id')
                                    ->label(__('Absence type'))
                                    ->options(AbsenceType::getAvailableOptionsForEmployees())
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\Textarea::make('notice'),
                            ]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->required()
                                    ->rules(['date']),
                                Forms\Components\DatePicker::make('end_date')
                                    ->required()
                                    ->afterOrEqual('start_date'),
                                Forms\Components\Select::make('status')
                                    ->options(AbsenceStatus::class)
                                    ->visibleOn(['view', 'edit'])
                                    ->disabled()
                            ]),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('absenceType.name')
                    ->label('Type')
                    ->icon(fn (MyAbsence $record) => $record->absenceType->icon ?? null)
                    ->iconColor(fn (MyAbsence $record) => $record->absenceType->color ? Color::hex($record->absenceType->color) : null)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->translateLabel()
                    ->badge(),

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
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('occupational')
                    ->label('Work-related?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AbsenceStatus::class),
                Tables\Filters\SelectFilter::make('absence_type_id')
                    ->label(__("Absence type"))
                    ->options(AbsenceType::pluck('name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->authorize('update', MyAbsence::class),
                Tables\Actions\DeleteAction::make()
                    ->authorize('delete', MyAbsence::class)
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        // Fetch your Spatie settings class
        $settings = app(\App\Settings\LocaleSettings::class);

        return $infolist
            ->schema([
                // 1) High-level overview section
                Components\Section::make(__('Absence Overview'))
                    ->description(__('A quick summary of this absence request.'))
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('absenceType.name')
                            ->label(__('Absence Type'))
                            ->icon(fn (MyAbsence $record) => $record->absenceType->icon ?? null)
                            ->iconColor(fn (MyAbsence $record) =>
                            $record->absenceType->color ? Color::hex($record->absenceType->color) : null
                            ),
                        Components\TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge(),
                    ]),

                Components\Section::make(__('Dates'))
                    ->columns(3)
                    ->schema([
                        Components\TextEntry::make('start_date')
                            ->label(__('Start Date'))
                            ->formatStateUsing(function (MyAbsence $record) use ($settings) {
                                // If absence tracks hours, use date+time format from settings
                                if ($record->absenceType->has_hours) {
                                    $format = $settings->getDateTimeFormatWithClock();
                                } else {
                                    // Otherwise, just the date format
                                    $format = $settings->default_date_format;
                                }

                                return $record->start_date
                                    ? $record->start_date->format($format)
                                    : null;
                            }),

                        Components\TextEntry::make('end_date')
                            ->label(__('End Date'))
                            ->formatStateUsing(function (MyAbsence $record) use ($settings) {
                                if ($record->absenceType->has_hours) {
                                    $format = $settings->getDateTimeFormatWithClock();
                                } else {
                                    $format = $settings->default_date_format;
                                }

                                return $record->end_date
                                    ? $record->end_date->format($format)
                                    : null;
                            }),

                        Components\TextEntry::make('estimated_end_date')
                            ->label(__('Estimated End Date'))
                            ->formatStateUsing(function (MyAbsence $record) use ($settings) {
                                if ($record->absenceType->has_hours) {
                                    $format = $settings->getDateTimeFormatWithClock();
                                } else {
                                    $format = $settings->default_date_format;
                                }

                                return $record->estimated_end_date
                                    ? $record->estimated_end_date->format($format)
                                    : null;
                            })
                            // Only show if no end date but an estimated date is set:
                            ->visible(fn (MyAbsence $record) =>
                                empty($record->end_date) && $record->estimated_end_date
                            ),
                    ]),

                // 3) Additional details
                Components\Section::make(__('Details'))
                    ->columns(1)
                    ->schema([
                        Components\TextEntry::make('note')
                            ->label(__('Note')),

                        // Only show these if category == SICK_LEAVES
                        Components\Fieldset::make(__('Sick Leave Details'))
                            ->schema([
                                Components\IconEntry::make('is_medically_certified')
                                    ->boolean()
                                    ->label(__('Medically Certified?'))
                                    ->trueIcon('heroicon-s-check-circle')
                                    ->falseIcon('heroicon-s-x-circle'),

                                Components\IconEntry::make('occupational')
                                    ->boolean()
                                    ->label(__('Work-related?'))
                                    ->trueIcon('heroicon-s-check-circle')
                                    ->falseIcon('heroicon-s-x-circle'),
                            ])
                            ->visible(fn (MyAbsence $record) =>
                                $record->absenceType->category === AbsenceCategory::SICK_LEAVES
                            )
                            ->columns(2),
                    ]),

                // 4) Meta information (created/updated)
                Components\Section::make(__('Meta'))
                    ->collapsible()
                    ->collapsed()
                    ->description(__('Technical info about this absence record.'))
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->formatStateUsing(fn (Carbon $state) => $state->format(app(LocaleSettings::class)->getDateTimeFormatWithClock()))
                            ->inlineLabel(),

                        Components\TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->formatStateUsing(fn (Carbon $state) => $state->format(app(LocaleSettings::class)->getDateTimeFormatWithClock()))
                            ->inlineLabel(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAbsences::route('/'),
            'create' => Pages\CreateMyAbsence::route('/create'),
            'view' => Pages\ViewMyAbsence::route('/{record}'),
            'edit' => Pages\EditMyAbsence::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('employee');
    }
}
