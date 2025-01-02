<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Resources\AbsenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListAbsences extends ListRecords
{
    protected static string $resource = AbsenceResource::class;

    /**
     * @return MaxWidth
     */
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__("Register absence"))
                ->icon('heroicon-o-plus'),
        ];
    }
}
