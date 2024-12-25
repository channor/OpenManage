<?php

namespace App\Models;

use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'birthdate',
        'email',
        'phone_number',
        'address',
        'city',
        'postal_code',
        'type',
        'user_id',
    ];

    protected $casts = [
        'type' => PersonType::class,
        'birthdate' => 'date',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
