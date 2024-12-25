<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PersonType: string implements HasLabel
{
    case Employee = "employee";
    case Contact = "contact";
    case Customer = "customer";
    case External = "external";


    public function getLabel(): ?string
    {
        return match($this) {
            self::Employee => __("Employee"),
            self::Contact => __("Contact"),
            self::Customer => __("Customer"),
            self::External => __("External"),
        };
    }
}
