<?php

namespace App\Models;

use App\Enums\AbsenceCategory;
use App\Settings\AbsenceSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $name
 * @property mixed $id
 */
class AbsenceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'icon',
        'color',
        'employee_creation',
        'has_hours',
    ];

    protected $casts = [
        'category' => AbsenceCategory::class,
        'employee_creation' => 'boolean',
        'has_hours' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Each AbsenceType can be linked to many absences (foreign key: absence_type_id)
    public function absences(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Absence::class, 'absence_type_id');
    }

    public static function getDefaultHolidaysType(): ?self
    {
        $settings = app(AbsenceSettings::class);

        return static::where('name', $settings->default_holidays_name)->first();
    }

    public static function getDefaultOwnIllnessType(): ?self
    {
        $settings = app(AbsenceSettings::class);

        return static::where('name', $settings->default_own_illness_name)->first();
    }

    public static function getAvailableOptionsForEmployees()
    {
        return self::where('employee_creation', true)->pluck('name', 'id');
    }

}
