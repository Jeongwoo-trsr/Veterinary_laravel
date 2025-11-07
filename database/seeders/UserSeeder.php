<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@veterinary.com',
            'password' => bcrypt('password'),
            'phone' => '+1234567890',
            'address' => '123 Admin Street, City, State',
            'role' => 'admin',
        ]);

        // Create Doctor Users
        $doctor1 = \App\Models\User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'sarah.johnson@veterinary.com',
            'password' => bcrypt('password'),
            'phone' => '+1234567891',
            'address' => '456 Doctor Lane, City, State',
            'role' => 'doctor',
        ]);

        $doctor2 = \App\Models\User::create([
            'name' => 'Dr. Michael Brown',
            'email' => 'michael.brown@veterinary.com',
            'password' => bcrypt('password'),
            'phone' => '+1234567892',
            'address' => '789 Vet Avenue, City, State',
            'role' => 'doctor',
        ]);

        // Create Doctor profiles
        \App\Models\Doctor::create([
            'user_id' => $doctor1->id,
            'specialization' => 'General Practice',
            'license_number' => 'VET001',
            'experience_years' => 8,
            'bio' => 'Dr. Sarah Johnson specializes in general veterinary practice with 8 years of experience.',
        ]);

        \App\Models\Doctor::create([
            'user_id' => $doctor2->id,
            'specialization' => 'Surgery',
            'license_number' => 'VET002',
            'experience_years' => 12,
            'bio' => 'Dr. Michael Brown is a skilled surgeon with 12 years of experience in veterinary surgery.',
        ]);

        // Create Pet Owner Users
        $owner1 = \App\Models\User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@email.com',
            'password' => bcrypt('password'),
            'phone' => '+1234567893',
            'address' => '321 Pet Owner Street, City, State',
            'role' => 'pet_owner',
        ]);

        $owner2 = \App\Models\User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@email.com',
            'password' => bcrypt('password'),
            'phone' => '+1234567894',
            'address' => '654 Animal Lane, City, State',
            'role' => 'pet_owner',
        ]);

        // Create Pet Owner profiles
        \App\Models\PetOwner::create([
            'user_id' => $owner1->id,
            'emergency_contact' => 'Mary Smith',
            'emergency_phone' => '+1234567895',
            'notes' => 'Prefers morning appointments',
        ]);

        \App\Models\PetOwner::create([
            'user_id' => $owner2->id,
            'emergency_contact' => 'Bob Doe',
            'emergency_phone' => '+1234567896',
            'notes' => 'Has multiple pets',
        ]);
    }
}