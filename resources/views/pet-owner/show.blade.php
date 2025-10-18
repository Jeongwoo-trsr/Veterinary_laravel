@extends('layouts.app')

@section('title', 'Pet Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pet Details</h1>
        <div class="flex gap-2">
            <a href="{{ route('pet-owner.pets') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
       <!-- Header with gradient -->
        <div style="background: linear-gradient(135deg, #4663e4ff 0%, #fcff54ff 100%); padding: 32px; color: white;">
    <div>
        <h2 class="text-4xl font-bold mb-2">{{ $pet->name }}</h2>
        <p class="text-purple-100 text-lg">{{ $pet->species }} - {{ $pet->breed ?? 'Mixed' }}</p>
    </div>
        </div>


        <!-- Pet Information -->
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Basic Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Species</label>
                            <p class="font-medium">{{ $pet->species }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Breed</label>
                            <p class="font-medium">{{ $pet->breed ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Age</label>
                            <p class="font-medium">{{ $pet->age }} years old</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Gender</label>
                            <p class="font-medium">{{ ucfirst($pet->gender) }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Physical Details</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Weight</label>
                            <p class="font-medium">{{ $pet->weight ? $pet->weight . ' kg' : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Color</label>
                            <p class="font-medium">{{ $pet->color ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Microchip ID</label>
                            <p class="font-medium">{{ $pet->microchip_id ?? 'Not microchipped' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Notes -->
            @if($pet->medical_notes)
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Medical Notes</h3>
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                    <p class="text-gray-700">{{ $pet->medical_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Appointments History (if available) -->
            @if($pet->appointments && $pet->appointments->count() > 0)
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Recent Appointments</h3>
                <div class="space-y-2">
                    @foreach($pet->appointments->take(5) as $appointment)
                    <div class="bg-gray-50 p-3 rounded flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $appointment->service->name ?? 'General Checkup' }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->appointment_date->format('M d, Y') }} - Dr. {{ $appointment->doctor->user->name }}</p>
                        </div>
                        <span class="px-3 py-1 rounded text-xs font-semibold
                            @if($appointment->status === 'completed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'scheduled') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection