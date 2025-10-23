<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for appointments scheduled tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for appointments scheduled tomorrow...');

        // Get tomorrow's date
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Get all scheduled or pending appointments for tomorrow
        $appointments = Appointment::with(['pet.owner.user', 'doctor.user'])
            ->whereDate('appointment_date', $tomorrow)
            ->whereIn('status', ['scheduled', 'pending'])
            ->where(function($query) {
                $query->where('cancellation_status', '!=', 'pending')
                      ->orWhereNull('cancellation_status');
            })
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments scheduled for tomorrow.');
            return 0;
        }

        $sentCount = 0;

        foreach ($appointments as $appointment) {
            // Send reminder to pet owner
            $petOwnerUserId = $appointment->pet->owner->user_id;
            $petOwnerName = $appointment->pet->owner->user->name;
            $petName = $appointment->pet->name;
            $doctorName = $appointment->doctor->user->name;
            $appointmentTime = Carbon::parse($appointment->appointment_time)->format('g:i A');
            $appointmentDate = $appointment->appointment_date->format('M d, Y');

            // Check if reminder already sent today (avoid duplicates)
            $existingNotification = Notification::where('user_id', $petOwnerUserId)
                ->where('appointment_id', $appointment->id)
                ->where('type', 'appointment_reminder')
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$existingNotification) {
                Notification::create([
                    'user_id' => $petOwnerUserId,
                    'type' => 'appointment_reminder',
                    'title' => 'Appointment Reminder',
                    'message' => "Your appointment with Dr. {$doctorName} is tomorrow ({$appointmentDate}) at {$appointmentTime}.",
                    'appointment_id' => $appointment->id,
                ]);
                $sentCount++;
                $this->info("✓ Reminder sent to {$petOwnerName} for {$petName}'s appointment");
            }

            // Send reminder to doctor
            $doctorUserId = $appointment->doctor->user_id;
            
            $existingDoctorNotification = Notification::where('user_id', $doctorUserId)
                ->where('appointment_id', $appointment->id)
                ->where('type', 'appointment_reminder')
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$existingDoctorNotification) {
                Notification::create([
                    'user_id' => $doctorUserId,
                    'type' => 'appointment_reminder',
                    'title' => 'Appointment Reminder',
                    'message' => "You have an appointment with {$petName} (owner: {$petOwnerName}) tomorrow ({$appointmentDate}) at {$appointmentTime}.",
                    'appointment_id' => $appointment->id,
                ]);
                $sentCount++;
                $this->info("✓ Reminder sent to Dr. {$doctorName} for appointment with {$petName}");
            }
        }

        $this->info("Total reminders sent: {$sentCount}");
        $this->info('Appointment reminders sent successfully!');
        
        return 0;
    }
}
