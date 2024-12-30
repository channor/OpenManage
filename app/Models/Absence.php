<?php

namespace App\Models;

use App\Enums\AbsenceStatus;
use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $person_id
 */
class Absence extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::saving(function ($absence) {
            // If has_hours is false, force 00:00 for the hours
            if (! $absence->absenceType->has_hours) {
                if ($absence->start_date) {
                    $absence->start_date->setTime(0, 0, 0);
                }
                if ($absence->end_date) {
                    $absence->end_date->setTime(0, 0, 0);
                }
                if ($absence->estimated_end_date) {
                    $absence->estimated_end_date->setTime(0, 0, 0);
                }
            }
        });
    }

    public function scopeByPerson(Builder $query, ?int $personId = null): Builder
    {
        // If no personId is provided, attempt to retrieve it from the authenticated user
        if (is_null($personId)) {
            $user = auth()->user();

            // Ensure the user has an associated person
            if ($user && $user->person) {
                $personId = $user->person->id;
            } else {
                return $query->whereRaw('1=0');
            }
        }

        return $query->where('person_id', $personId);
    }

    /**
     * By default, mass assignment protection is in place.
     * You can either whitelist (fillable) or blacklist (guarded) attributes.
     */
    // Option 1: Whitelist columns that can be mass assigned:
    protected $fillable = [
        'person_id',
        'start_date',
        'end_date',
        'estimated_end_date',
        'is_medically_certified',
        'occupational',
        'status',
        'approved_by',
        'approved_at',
        'absence_type_id',
        'is_paid',
        'notes',
    ];

    // OR Option 2: Use guarded to block certain columns (allowing everything else):
    // protected $guarded = ['id'];

    /**
     * Eloquent will automatically convert these fields to Carbon (date/time) objects
     * and booleans, so you can work with them more easily in PHP.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'estimated_end_date' => 'datetime',
        'approved_at' => 'datetime',

        'is_medically_certified' => 'boolean',
        'occupational' => 'boolean',
        'is_paid' => 'boolean',
        'status' => AbsenceStatus::class
    ];

    /**
     * Relationships
     */

    // The person who is absent (foreign key: person_id)
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // If your model for the 'people' table is App\Models\Person, reference it here:
        return $this->belongsTo(Person::class)->where('type', PersonType::Employee);
    }

    // The user who approved the absence (foreign key: approved_by in 'users' table)
    public function approvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // If your users table is bound to App\Models\User, reference it:
        return $this->belongsTo(User::class, 'approved_by');
    }

    // The type/category of the absence (foreign key: absence_type_id)
    public function absenceType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AbsenceType::class);
    }
}
