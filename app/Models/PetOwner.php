<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PetOwner extends Model
{
    protected $fillable = [
        'user_id',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }
}
