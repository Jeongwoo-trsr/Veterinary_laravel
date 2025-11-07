@extends('layouts.app')

@section('title', 'Medical Record Details')

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold" style="color: #2c3e50;">Medical Record Details</h1>
            <p class="mt-1" style="color: #5d6d7e;">{{ $medicalRecord->pet->name }} - {{ $medicalRecord->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <!-- Back button for all users -->
            <button type="button" onclick="history.back()" class="px-4 py-2 rounded-lg" style="background-color: #95a5a6; color: #ffffff;">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </button>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="shadow-lg rounded-lg mb-6 border-2" style="background-color: #ffffff; border-color: #0d5cb6;">
        <div class="px-6 py-3" style="background-color: #34495e;">
            <h2 class="text-lg font-semibold text-white">Patient Information</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4" style="background-color: #ffffff;">
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Pet Name</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->pet->name }}</p>
            </div>
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Species</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->pet->species ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Breed</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->pet->breed ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Owner</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->pet->owner->user->name }}</p>
            </div>
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Doctor</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->doctor->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm" style="color: #5d6d7e;">Record Date</p>
                <p class="text-base font-medium" style="color: #2c3e50;">{{ $medicalRecord->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Diagnosis and Treatment -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow-lg rounded-lg border-2" style="border-color: #0502a1ff;">
            <div class="px-6 py-3" style="background-color: #34495e;">
                <h2 class="text-lg font-semibold text-white">Diagnosis</h2>
            </div>
            <div class="p-6">
                <p class="whitespace-pre-wrap" style="color: #5d6d7e;">{{ $medicalRecord->diagnosis }}</p>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg border-2" style="border-color: #0502a1ff;">
            <div class="px-6 py-3" style="background-color: #34495e;">
                <h2 class="text-lg font-semibold text-white">Treatment</h2>
            </div>
            <div class="p-6">
                <p class="whitespace-pre-wrap" style="color: #5d6d7e;">{{ $medicalRecord->treatment }}</p>
            </div>
        </div>
    </div>

    <!-- Prescription -->
    @if($medicalRecord->prescription)
    <div class="bg-white shadow-lg rounded-lg mb-6 border-2" style="border-color: #0502a1ff;">
        <div class="px-6 py-3" style="background-color: #34495e;">
            <h2 class="text-lg font-semibold text-white">General Prescription Notes</h2>
        </div>
        <div class="p-6">
            <p class="whitespace-pre-wrap" style="color: #5d6d7e;">{{ $medicalRecord->prescription }}</p>
        </div>
    </div>
    @endif

    <!-- Medications -->
    @if($medicalRecord->prescriptions->count() > 0)
    <div class="bg-white shadow-lg rounded-lg mb-6 border-2" style="border-color: #0502a1ff;">
        <div class="px-6 py-3" style="background-color: #34495e;">
            <h2 class="text-lg font-semibold text-white">Medications</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                    <thead style="background-color: #ecf0f1;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #2c3e50;">Medication</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #2c3e50;">Dosage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #2c3e50;">Frequency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #2c3e50;">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #2c3e50;">Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: #e5e7eb;">
                        @foreach($medicalRecord->prescriptions as $prescription)
                        <tr>
                            <td class="px-6 py-4 text-sm" style="color: #2c3e50;">{{ $prescription->medication_name }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">{{ $prescription->dosage ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">{{ $prescription->frequency ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">{{ $prescription->duration_days ? $prescription->duration_days . ' days' : '-' }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">{{ $prescription->instructions ?? '-' }}</td>
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
    <div class="bg-white shadow-lg rounded-lg mb-6 border-2" style="border-color: #0d5cb6;">
        <div class="px-6 py-3" style="background-color: #34495e;">
            <h2 class="text-lg font-semibold text-white">Follow-up Schedule</h2>
        </div>
        <div class="p-6 space-y-4">
            @foreach($medicalRecord->followUpSchedules as $followUp)
            <div class="border rounded-lg p-4 bg-white" style="border-color: #e5e7eb;">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm" style="color: #5d6d7e;">Scheduled Date</p>
                        <p class="text-base font-medium" style="color: #2c3e50;">{{ $followUp->scheduled_date->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full" 
                        style="@if($followUp->status === 'pending') background-color: #f9a825; color: #ffffff;
                        @elseif($followUp->status === 'completed') background-color: #28a745; color: #ffffff;
                        @else background-color: #95a5a6; color: #ffffff; @endif">
                        {{ ucfirst($followUp->status) }}
                    </span>
                </div>
                @if($followUp->notes)
                <div class="mt-3">
                    <p class="text-sm" style="color: #5d6d7e;">Notes</p>
                    <p style="color: #5d6d7e;">{{ $followUp->notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
button:hover {
    opacity: 0.9;
}
</style>
@endsection