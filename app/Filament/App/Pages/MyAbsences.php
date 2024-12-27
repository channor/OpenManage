<?php

namespace App\Filament\App\Pages;

use App\Models\Absence;
use App\Models\AbsenceType;
use Filament\Pages\Page;

class MyAbsences extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'My Absences';
    protected static ?string $slug = 'my-absences';
}
