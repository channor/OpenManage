<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Resources\AbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewAbsence extends ViewRecord
{
    protected static string $resource = AbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
