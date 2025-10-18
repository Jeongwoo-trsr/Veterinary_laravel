@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Medical Record Details</h1>
            <p class="text-gray-600 mt-1">{{ $medicalRecord->pet->name }} - {{ $medicalRecord->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <!-- Back button for all users -->
            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </button>

            <!-- Edit and Delete buttons ONLY for Admins and Doctors
            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
            <a href="{{ route('medical-records.edit', $medicalRecord->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-edit mr-2"></i>
                Edit Record
            </a>
            <form action="{{ route('medical-records.destroy', $medicalRecord->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this medical record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>
                    Delete
                </button>
            </form>
            @endif -->
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Patient Information</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Pet Name</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->pet->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Species</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->pet->species ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Breed</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->pet->breed ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Owner</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->pet->owner->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Doctor</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->doctor->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Record Date</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Diagnosis and Treatment -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 shadow rounded-lg">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Diagnosis</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $medicalRecord->diagnosis }}</p>
            </div>
        </div>

        <div class="bg-gray-50 shadow rounded-lg">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Treatment</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $medicalRecord->treatment }}</p>
            </div>
        </div>
    </div>

    <!-- Prescription -->
    @if($medicalRecord->prescription)
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">General Prescription Notes</h2>
        </div>
        <div class="p-6">
            <p class="text-gray-700 whitespace-pre-wrap">{{ $medicalRecord->prescription }}</p>
        </div>
    </div>
    @endif

    <!-- Medications -->
    @if($medicalRecord->prescriptions->count() > 0)
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Medications</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Medication</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Dosage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Frequency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($medicalRecord->prescriptions as $prescription)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $prescription->medication_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->dosage ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->frequency ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->duration_days ? $prescription->duration_days . ' days' : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $prescription->instructions ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Follow-up Information -->
    @if($medicalRecord->followUpSchedules->count() > 0)
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Follow-up Schedule</h2>
        </div>
        <div class="p-6 space-y-4">
            @foreach($medicalRecord->followUpSchedules as $followUp)
            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Scheduled Date</p>
                        <p class="text-base font-medium text-gray-900">{{ $followUp->scheduled_date->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @if($followUp->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($followUp->status === 'completed') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($followUp->status) }}
                    </span>
                </div>
                @if($followUp->notes)
                <div class="mt-3">
                    <p class="text-sm text-gray-600">Notes</p>
                    <p class="text-gray-700">{{ $followUp->notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Appointment Link -->
    @if($medicalRecord->appointment)
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Related Appointment</h2>
        </div>
        <div class="p-6">
            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600">Service</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->appointment->service->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mt-2">Appointment Date</p>
                <p class="text-base font-medium text-gray-900">{{ $medicalRecord->appointment->appointment_date->format('M d, Y - h:i A') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Documents -->
    @if($medicalRecord->documents->count() > 0)
    <div class="bg-gray-50 shadow rounded-lg mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Attached Documents</h2>
        </div>
        <div class="p-6 space-y-3">
            @foreach($medicalRecord->documents as $document)
            <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg bg-white">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file text-gray-400 text-xl"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $document->file_name }}</p>
                        <p class="text-xs text-gray-600">{{ $document->description }}</p>
                    </div>
                </div>
                <a href="{{ route('documents.download', $document->id) }}" class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-download"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection