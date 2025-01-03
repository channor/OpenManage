<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Events\AbsenceCreatedEvent;
use App\Filament\App\Resources\AbsenceResource;
use App\Models\Absence;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsence extends CreateRecord
{
    protected static string $resource = AbsenceResource::class;



    protected function afterCreate(): void
    {
        if($this->record instanceof Absence) {
            event(new AbsenceCreatedEvent($this->record));
        }
    }
}
