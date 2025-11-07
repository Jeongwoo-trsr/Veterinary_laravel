<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\User;

trait SendsNotifications
{
    /**
     * Create a notification for a specific user
     */
    protected function createNotification($userId, $type, $title, $message, $appointmentId = null)
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'appointment_id' => $appointmentId,
        ]);
    }

    /**
     * Notify all admins and doctors
     */
    protected function notifyAdminsAndDoctors($type, $title, $message, $appointmentId = null)
    {
        $users = User::whereIn('role', ['admin', 'doctor'])->get();
        
        foreach ($users as $user) {
            $this->createNotification($user->id, $type, $title, $message, $appointmentId);
        }
    }
}