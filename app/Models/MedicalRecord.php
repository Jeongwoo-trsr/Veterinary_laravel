<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'appointment_id',
        'diagnosis',
        'treatment',
        'prescription',
        'follow_up_date',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    // Relationships
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function followUpSchedules(): HasMany
    {
        return $this->hasMany(FollowUpSchedule::class);
    }
}
