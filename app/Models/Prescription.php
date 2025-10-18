<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    protected $fillable = [
        'medical_record_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
    ];

    // Relationships
    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
