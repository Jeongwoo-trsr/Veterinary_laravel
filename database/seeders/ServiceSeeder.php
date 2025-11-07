<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Grooming',
                'description' => 'Professional pet grooming services including bathing, brushing, nail trimming, and styling.',
                'duration_minutes' => 60,
                'price' => 50.00,
                'is_active' => true,
            ],
            [
                'name' => 'Surgery',
                'description' => 'Surgical procedures including spaying, neutering, and emergency surgeries.',
                'duration_minutes' => 120,
                'price' => 300.00,
                'is_active' => true,
            ],
            [
                'name' => 'Therapy',
                'description' => 'Physical therapy and rehabilitation services for pets recovering from injuries.',
                'duration_minutes' => 45,
                'price' => 80.00,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Care',
                'description' => '24/7 emergency veterinary care for critical situations.',
                'duration_minutes' => 90,
                'price' => 150.00,
                'is_active' => true,
            ],
            [
                'name' => 'Vaccination',
                'description' => 'Vaccination services including routine immunizations and health checkups.',
                'duration_minutes' => 30,
                'price' => 40.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::create($service);
        }
    }
}