<?php

namespace App\Filament\App\Resources\PersonResource\Pages;

use App\Filament\App\Resources\PersonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerson extends CreateRecord
{
    protected static string $resource = PersonResource::class;
}
