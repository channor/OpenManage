<?php

namespace App\Filament\App\Resources\AbsenceTypeResource\Pages;

use App\Filament\App\Resources\AbsenceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsenceTypes extends ListRecords
{
    protected static string $resource = AbsenceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
