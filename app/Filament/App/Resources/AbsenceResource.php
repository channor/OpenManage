<?php

namespace App\Filament\App\Resources;

use App\Enums\AbsenceStatus;
use App\Filament\App\Resources\AbsenceResource\Pages;
use App\Filament\App\Resources\AbsenceResource\RelationManagers;
use App\Models\Absence;
use App\Models\AbsenceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Traits\Conditionable;
use PHPUnit\Metadata\Group;

class AbsenceResource extends Resource
{
    protected static ?string $model = Absence::class;

    protected static ?string $navigationGroup = 'Absence & Holidays';

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __("Absence list");
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('absence_type_id')
                        ->relationship('absenceType', 'name')
                        ->required(),

                    Forms\Components\Select::make('person_id')
                        ->relationship('person', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start (Date Only)')
                        ->required(),

                    // DatePicker shown ONLY if has_hours = false
                    Forms\Components\DatePicker::make('end_date')
                        ->label('End (Date Only)')
                        ->nullable(),

                    // DatePicker shown ONLY if has_hours = false
                    Forms\Components\DatePicker::make('estimated_end_date')
                        ->label('Estimated end (Date Only)')
                        ->nullable(),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
                    Forms\Components\Group::make()->schema([
                        Forms\Components\Toggle::make('is_medically_certified')
                            ->required(),
                        Forms\Components\Toggle::make('occupational')
                            ->helperText(__("Related to the working place or environment."))
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(AbsenceStatus::class)
                            ->required()
                            ->inlineLabel()
                            ->default('approved'),
                    ]),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.name')
                    ->label('Employee')
                    ->translateLabel()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('absenceType.name')
                    ->label('Type')
                    ->translateLabel()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->translateLabel()
                    ->dateTime()
                    ->formatStateUsing(
                        fn ($record) => $record->absenceType->has_hours
                        ? $record->start_date?->format('Y-m-d H:i')
                        : $record->start_date?->format('Y-m-d')
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End')
                    ->translateLabel()
                    ->dateTime()
                    ->toggleable()
                    ->formatStateUsing(
                        fn ($record) => $record->absenceType->has_hours
                            ? $record->end_date?->format('Y-m-d H:i')
                            : $record->end_date?->format('Y-m-d')
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_end_date')
                    ->label('Estimated end')
                    ->translateLabel()
                    ->toggleable()
                    ->dateTime()
                    ->formatStateUsing(
                        fn ($record) => $record->absenceType->has_hours
                            ? $record->estimated_end_date?->format('Y-m-d H:i')
                            : $record->estimated_end_date?->format('Y-m-d')
                    )
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_medically_certified')
                    ->toggleable()
                    ->label('Medically certified')
                    ->translateLabel()
                    ->boolean(),
                Tables\Columns\IconColumn::make('occupational')
                    ->label('Occupational')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->translateLabel()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->label('Approved by')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved at')
                    ->translateLabel()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->translateLabel()
                    ->toggleable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->translateLabel()
                    ->options(AbsenceStatus::class),
                Tables\Filters\Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start')
                            ->translateLabel()
                            ->label('From'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End')
                            ->translateLabel()
                            ->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['start_date'], fn (Builder $query, $date) =>
                            $query->whereDate('start_date', '>=', $date)
                            )
                            ->when($data['end_date'], fn (Builder $query, $date) =>
                            $query->whereDate('start_date', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAbsences::route('/'),
            'create' => Pages\CreateAbsence::route('/create'),
            'view' => Pages\ViewAbsence::route('/{record}'),
            'edit' => Pages\EditAbsence::route('/{record}/edit'),
        ];
    }
}
