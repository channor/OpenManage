<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AbsenceStatus: string implements HasLabel, HasColor
{
    case Pending = "pending";
    case Approved = "approved";
    case Denied = "denied";
    case Cancelled = "cancelled";
    case Requested = "requested";


    public function getLabel(): ?string
    {
        return match($this) {
            self::Pending => __("Pending"),
            self::Approved => __("Approved"),
            self::Denied => __("Denied"),
            self::Cancelled => __("Cancelled"),
            self::Requested => __("Requested"),

        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::Pending => 'gray',
            self::Approved => 'success',
            self::Denied => 'danger',
            self::Cancelled => 'warning',
            self::Requested => Color::Yellow,
        };
    }
}
