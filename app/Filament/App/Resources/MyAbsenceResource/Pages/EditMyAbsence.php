<?php

namespace App\Filament\App\Resources\MyAbsenceResource\Pages;

use App\Filament\App\Resources\MyAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyAbsence extends EditRecord
{
    protected static string $resource = MyAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
