<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AbsenceCategory: string implements HasLabel
{
    case SICK_LEAVES = "sick_leaves";
    case HOLIDAYS = "holidays";
    case AGREED_LEAVES = "agreed_leave";
    case UNAUTHORIZED_LEAVES = "unauthorized_leave";


    public function getLabel(): ?string
    {
        return match($this) {
            self::SICK_LEAVES => __("Sick leaves"),
            self::HOLIDAYS => __("Holidays"),
            self::AGREED_LEAVES => __("Agreed leaves"),
            self::UNAUTHORIZED_LEAVES => __("Unauthorized leaves"),
        };
    }
}
