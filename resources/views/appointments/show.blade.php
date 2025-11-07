@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Appointment Details</h1>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @elseif(Auth::user()->isDoctor())
            <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @elseif(Auth::user()->isPetOwner())
            <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @endif
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Appointment Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Date & Time</label>
                        <p class="font-medium">{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Status</label>
                        <p class="font-medium">
                            <span class="px-3 py-1 text-sm font-bold rounded-md
                                @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                                @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                                @elseif($appointment->status === 'pending') bg-purple-200 text-purple-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Service</label>
                        <p class="font-medium">{{ $appointment->service->name }}</p>
                    </div>
                    @if($appointment->notes)
                    <div>
                        <label class="text-sm text-gray-600">Notes</label>
                        <p class="font-medium">{{ $appointment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Pet Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Pet Name</label>
                        <p class="font-medium">{{ $appointment->pet->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Species</label>
                        <p class="font-medium">{{ $appointment->pet->species }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Owner</label>
                        <p class="font-medium">{{ $appointment->pet->owner->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 border-t pt-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Doctor Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-600">Doctor Name</label>
                    <p class="font-medium">{{ $appointment->doctor->user->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection