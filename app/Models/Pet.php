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
        'approval_status',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'approved_at' => 'datetime',
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

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }
}