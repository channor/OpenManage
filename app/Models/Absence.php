<?php

namespace App\Models;

use App\Enums\AbsenceStatus;
use App\Enums\PersonType;
use App\Notifications\AbsenceCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class Absence
 *
 * @method static \Illuminate\Database\Eloquent\Builder byPerson()
 *
 * @property mixed $person_id
 * @property Person $person
 */
class Absence extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        static::saving(function (Absence $absence) {
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

        static::created(function (Absence $absence) {
            $user = $absence->person->user;

            $user->notify(new AbsenceCreated($absence));
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
     * Check if user is the right owner by checking User's person relation.
     */
    public function isOwnedBy(User $user = null): bool
    {
        if($user === null) {
            $user = auth()->user();
        }

        return $user->person?->id === $this->person_id;
    }

    /**
     * Check if the user is a manager of the current absence.
     *
     * Future function if an absence has a selected manager.
     */
    public function canBeManagedBy(User $user): bool
    {
        /*
         * return $this->manager_id === $user->id || $user->isAbsenceManager();
         */

        return $user->isAbsenceManager();
    }

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

    public static function getNotificationRecipient(): User
    {
        // Retrieve the first user with the 'super_admin' role
        $recipient = User::role('super_admin')->first();

        // If no super_admin exists, throw an exception
        if (!$recipient) {
            throw new \Exception('No user with the super_admin role found.');
        }

        return $recipient;
    }

    public static function getNotificationRecipients()
    {
        // Retrieve the first user with the 'super_admin' role
        $recipients = User::role('super_admin');

        // If no super_admin exists, throw an exception
        if (!$recipients->count()) {
            throw new \Exception('No users with the super_admin role found.');
        }

        return $recipients;
    }
}
