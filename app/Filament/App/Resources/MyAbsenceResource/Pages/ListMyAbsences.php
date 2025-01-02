<?php

namespace App\Filament\App\Resources\MyAbsenceResource\Pages;

use App\Filament\App\Resources\MyAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMyAbsences extends ListRecords
{
    protected static string $resource = MyAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Request time-off"),
        ];
    }
}
