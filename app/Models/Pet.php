<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'species',
        'breed',
        'age',
        'weight',
        'color',
        'gender',
        'microchip_id',
        'medical_notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(PetOwner::class, 'owner_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
