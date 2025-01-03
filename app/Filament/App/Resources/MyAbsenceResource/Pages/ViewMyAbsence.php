<?php

namespace App\Filament\App\Resources\MyAbsenceResource\Pages;

use App\Filament\App\Resources\MyAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyAbsence extends ViewRecord
{
    protected static string $resource = MyAbsenceResource::class;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable
     */
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Absence');
    }
}
