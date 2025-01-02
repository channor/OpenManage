<?php

namespace App\Filament\App\Resources\MyAbsenceResource\Pages;

use App\Enums\AbsenceStatus;
use App\Filament\App\Resources\MyAbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyAbsence extends CreateRecord
{
    protected static string $resource = MyAbsenceResource::class;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable
     */
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __("Request time off");
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['person_id'] = auth()->user()->person?->id;
        $data['status'] = AbsenceStatus::Requested;

        return $data;
    }
}
