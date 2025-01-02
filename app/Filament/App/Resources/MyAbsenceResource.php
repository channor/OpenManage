<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MyAbsenceResource\Pages;
use App\Filament\App\Resources\MyAbsenceResource\RelationManagers;
use App\Models\AbsenceType;
use App\Models\MyAbsence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MyAbsenceResource extends Resource
{
    protected static ?string $model = MyAbsence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->byPerson();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Apply for')
                        ->schema([
                            Forms\Components\Radio::make('absence_type_id')
                                ->options(AbsenceType::all()->where('employee_creation', true)->pluck('name', 'id'))
                                ->inline()
                                ->hiddenLabel()
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('absenceType.name'),
                Columns\TextColumn::make('person.name')
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
            'index' => Pages\ListMyAbsences::route('/'),
            'create' => Pages\CreateMyAbsence::route('/create'),
            'edit' => Pages\EditMyAbsence::route('/{record}/edit'),
        ];
    }
}
