<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AbsenceTypeResource\Pages;
use App\Filament\App\Resources\AbsenceTypeResource\RelationManagers;
use App\Models\AbsenceType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbsenceTypeResource extends Resource
{
    protected static ?string $model = AbsenceType::class;

    protected static ?string $navigationGroup = 'Setting and administration';

    public static function getNavigationLabel(): string
    {
        return __('Absence types');
    }

    /**
     * @param bool $shouldRegisterNavigation
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150),
                Forms\Components\Toggle::make('employee_creation')
                    ->required(),
                Forms\Components\Toggle::make('has_hours')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('employee_creation')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_hours')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListAbsenceTypes::route('/'),
            'create' => Pages\CreateAbsenceType::route('/create'),
            'edit' => Pages\EditAbsenceType::route('/{record}/edit'),
        ];
    }
}
