@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-blue-100 shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Doctor Dashboard</h1>
        <p class="text-gray-600">Welcome, Dr. {{ Auth::user()->name }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Appointments Card -->
        <a href="{{ route('doctor.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_appointments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Pending Appointments Card -->
        <a href="{{ route('doctor.appointments', ['status' => 'scheduled']) }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_appointments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Completed Appointments Card -->
        <a href="{{ route('doctor.appointments', ['status' => 'completed']) }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_appointments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Medical Records Card -->
        <a href="{{ route('doctor.medical-records') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Medical Records</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_medical_records'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Appointments -->
    <div class="bg-blue-100 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Appointments</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-blue-100 divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $appointment->pet->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->pet->owner->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block
                                    @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                                    @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                                    @elseif($appointment->status === 'pending') bg-purple-200 text-purple-800
                                    @elseif($appointment->status === 'cancelled') bg-red-200 text-red-800
                                    @else bg-gray-200 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No recent appointments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-blue-100 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Medical Record
                </a>
                <a href="{{ route('doctor.appointments') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    View Appointments
                </a>
                <a href="{{ route('doctor.patients') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-paw mr-2"></i>
                    View Patients
                </a>
                <a href="{{ route('doctor.medical-records') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                    <i class="fas fa-file-medical mr-2"></i>
                    Medical Records
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ensure status badges stay visible and maintain their styling */
    .status-badge {
        display: inline-block !important;
        white-space: nowrap !important;
        min-width: fit-content !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Prevent table cells from affecting badge visibility */
    tbody td {
        vertical-align: middle;
        overflow: visible;
    }
    
    /* Force background colors to stay */
    .bg-yellow-200 {
        background-color: #fef3c7 !important;
    }
    
    .bg-green-200 {
        background-color: #bbf7d0 !important;
    }
    
    .bg-purple-200 {
        background-color: #e9d5ff !important;
    }
    
    .bg-red-200 {
        background-color: #fecaca !important;
    }
    
    .bg-gray-200 {
        background-color: #e5e7eb !important;
    }
    
    /* Force text colors to stay */
    .text-yellow-800 {
        color: #854d0e !important;
    }
    
    .text-green-800 {
        color: #166534 !important;
    }
    
    .text-purple-800 {
        color: #6b21a8 !important;
    }
    
    .text-red-800 {
        color: #991b1b !important;
    }
    
    .text-gray-800 {
        color: #1f2937 !important;
    }
</style>
@endsection