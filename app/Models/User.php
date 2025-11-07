<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function petOwner()
    {
        return $this->hasOne(PetOwner::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isPetOwner()
    {
        return $this->role === 'pet_owner';
    }

    public function notifications()
    {
    return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
    return $this->notifications()->where('is_read', false);
    }

    /**
 * Get all messages sent by this user
 */
public function sentMessages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

/**
 * Get all messages received by this user
 */
public function receivedMessages()
{
    return $this->hasMany(Message::class, 'receiver_id');
}

/**
 * Get all unread messages for this user
 */
public function unreadMessages()
{
    return $this->receivedMessages()->where('is_read', false);
}
}
