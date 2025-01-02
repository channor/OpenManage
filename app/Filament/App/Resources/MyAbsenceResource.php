<?php

namespace App\Filament\App\Resources;

use App\Enums\AbsenceStatus;
use App\Filament\App\Resources\MyAbsenceResource\Pages;
use App\Filament\App\Resources\MyAbsenceResource\RelationManagers;
use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\MyAbsence;
use Filament\Forms;
use Filament\Forms\Form;
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
                                Forms\Components\DatePicker::make('start_date'),
                                Forms\Components\DatePicker::make('end_date'),
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
