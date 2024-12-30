<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Pages\MyAbsences;
use App\Filament\App\Pages\ViewMyAbsence;
use App\Filament\App\Resources\AbsenceResource;
use App\Models\Absence;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsence extends CreateRecord
{
    protected static string $resource = AbsenceResource::class;

    protected function afterCreate(): void
    {
        $user = $this->getRecord()->person->user;

        $user->notify(
            Notification::make()
                ->title('Absence registered')
                ->body('An absence has been registered for you.')
                ->actions([
                    Action::make('view')
                        ->label(__('View'))
                        ->url(ViewMyAbsence::getUrl([$this->getRecord()]))
                        ->markAsRead()
                ])
                ->toDatabase()
        );
    }
}
