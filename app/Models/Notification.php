<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'appointment_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconAttribute()
    {
        return match($this->type) {
            'appointment_request' => 'fa-calendar-plus',
            'cancellation_request' => 'fa-calendar-times',
            'appointment_approved' => 'fa-check-circle',
            'appointment_rejected' => 'fa-times-circle',
            'cancellation_approved' => 'fa-check',
            'cancellation_declined' => 'fa-ban',
            default => 'fa-bell',
        };
    }

    public function getColorAttribute()
    {
        return match($this->type) {
            'appointment_request' => 'blue',
            'cancellation_request' => 'purple',
            'appointment_approved' => 'green',
            'appointment_rejected' => 'red',
            'cancellation_approved' => 'green',
            'cancellation_declined' => 'red',
            default => 'gray',
        };
    }
}