<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsenceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_creation',
        'has_hours',
    ];

    protected $casts = [
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
}
