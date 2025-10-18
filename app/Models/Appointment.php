<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $fillable = [
        'pet_id',
        'doctor_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'status',
        'cancellation_status',
        'cancellation_requested_at',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'cancellation_requested_at' => 'datetime',
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }
}