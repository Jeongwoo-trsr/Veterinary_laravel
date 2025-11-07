<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = [
            [
                'pet_id' => 1, // Buddy
                'doctor_id' => 1, // Dr. Sarah Johnson
                'service_id' => 1, // Grooming
                'appointment_date' => now()->subDays(5),
                'appointment_time' => '10:00:00',
                'status' => 'completed',
                'notes' => 'Regular grooming appointment',
            ],
            [
                'pet_id' => 2, // Whiskers
                'doctor_id' => 1, // Dr. Sarah Johnson
                'service_id' => 5, // Vaccination
                'appointment_date' => now()->subDays(3),
                'appointment_time' => '14:00:00',
                'status' => 'completed',
                'notes' => 'Annual vaccination',
            ],
            [
                'pet_id' => 3, // Max
                'doctor_id' => 2, // Dr. Michael Brown
                'service_id' => 2, // Surgery
                'appointment_date' => now()->subDays(1),
                'appointment_time' => '09:00:00',
                'status' => 'completed',
                'notes' => 'Neutering surgery',
            ],
            [
                'pet_id' => 4, // Luna
                'doctor_id' => 1, // Dr. Sarah Johnson
                'service_id' => 1, // Grooming
                'appointment_date' => now()->addDays(2),
                'appointment_time' => '11:00:00',
                'status' => 'scheduled',
                'notes' => 'First grooming appointment',
            ],
            [
                'pet_id' => 1, // Buddy
                'doctor_id' => 2, // Dr. Michael Brown
                'service_id' => 3, // Therapy
                'appointment_date' => now()->addDays(5),
                'appointment_time' => '15:00:00',
                'status' => 'confirmed',
                'notes' => 'Physical therapy session',
            ],
        ];

        foreach ($appointments as $appointment) {
            \App\Models\Appointment::create($appointment);
        }
    }
}