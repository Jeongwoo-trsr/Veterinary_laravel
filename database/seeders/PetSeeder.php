<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pets = [
            [
                'owner_id' => 1, // John Smith
                'name' => 'Buddy',
                'species' => 'dog',
                'breed' => 'Golden Retriever',
                'age' => 3,
                'weight' => 25.5,
                'color' => 'Golden',
                'gender' => 'male',
                'microchip_id' => 'CHIP001',
                'medical_notes' => 'Friendly and healthy dog. Regular checkups needed.',
            ],
            [
                'owner_id' => 1, // John Smith
                'name' => 'Whiskers',
                'species' => 'cat',
                'breed' => 'Persian',
                'age' => 2,
                'weight' => 4.2,
                'color' => 'White',
                'gender' => 'female',
                'microchip_id' => 'CHIP002',
                'medical_notes' => 'Indoor cat. Allergic to certain foods.',
            ],
            [
                'owner_id' => 2, // Jane Doe
                'name' => 'Max',
                'species' => 'dog',
                'breed' => 'German Shepherd',
                'age' => 5,
                'weight' => 30.0,
                'color' => 'Black and Tan',
                'gender' => 'male',
                'microchip_id' => 'CHIP003',
                'medical_notes' => 'Active dog. Needs regular exercise.',
            ],
            [
                'owner_id' => 2, // Jane Doe
                'name' => 'Luna',
                'species' => 'cat',
                'breed' => 'Siamese',
                'age' => 1,
                'weight' => 3.8,
                'color' => 'Seal Point',
                'gender' => 'female',
                'microchip_id' => 'CHIP004',
                'medical_notes' => 'Young cat. Very playful.',
            ],
        ];

        foreach ($pets as $pet) {
            \App\Models\Pet::create($pet);
        }
    }
}