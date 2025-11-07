@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="shadow rounded-lg p-6" style="background-color: #2c3e50; color: #ffffff;">
        <h1 class="text-2xl font-bold">Doctor Dashboard</h1>
        <p style="color: #ecf0f1;">Welcome, Dr. {{ Auth::user()->name }}</p>
    </div>

    <!-- Top Section: Statistics Cards + Upcoming Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Statistics Cards + Date Container (2 columns) -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Appointments Card -->
                <a href="{{ route('doctor.appointments') }}" class="overflow-hidden shadow rounded-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform" style="background-color: #2471a3;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-alt text-2xl" style="color: #ffffff;"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate" style="color: #ecf0f1;">Total Appointments</dt>
                                    <dd class="text-lg font-medium" style="color: #ffffff;">{{ $stats['total_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Pending Appointments Card -->
                <a href="{{ route('doctor.appointments', ['status' => 'scheduled']) }}" class="overflow-hidden shadow rounded-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform" style="background-color: #d68910;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-2xl" style="color: #ffffff;"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate" style="color: #ffffff;">Pending Appointments</dt>
                                    <dd class="text-lg font-medium" style="color: #ffffff;">{{ $stats['pending_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Completed Appointments Card -->
                <a href="{{ route('doctor.appointments', ['status' => 'completed']) }}" class="overflow-hidden shadow rounded-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform" style="background-color: #34495e;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-2xl" style="color: #ffffff;"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate" style="color: #ecf0f1;">Completed Appointments</dt>
                                    <dd class="text-lg font-medium" style="color: #ffffff;">{{ $stats['completed_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Medical Records Card -->
                <a href="{{ route('doctor.medical-records') }}" class="overflow-hidden shadow rounded-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform" style="background-color: #1e3a5f;">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-medical text-2xl" style="color: #ffffff;"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate" style="color: #ecf0f1;">Medical Records</dt>
                                    <dd class="text-lg font-medium" style="color: #ffffff;">{{ $stats['total_medical_records'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Date & Day Container (Full Width Below Cards) -->
            <div class="shadow-lg rounded-lg border border-gray-200" style="background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);">
                <div class="px-6 py-6">
                    <div class="flex flex-col space-y-4">
                        <!-- Date Display -->
                        <div class="flex items-center justify-center space-x-4">
                            <div class="text-center">
                                <p class="text-5xl font-bold" style="color: #ffffff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                                    {{ now()->format('d') }}
                                </p>
                                <p class="text-sm font-medium mt-1" style="color: #ecf0f1;">
                                    {{ now()->format('F') }}
                                </p>
                            </div>
                            <div class="border-l-2 border-gray-400 pl-4">
                                <p class="text-xl font-semibold" style="color: #ffffff;">
                                    {{ now()->format('l') }}
                                </p>
                                <p class="text-sm" style="color: #ecf0f1;">
                                    {{ now()->format('Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Divider -->
                        <div class="border-t border-gray-500"></div>
                        
                        <!-- Current Time -->
                        <div class="text-center">
                            <p class="text-xs uppercase tracking-wider font-semibold mb-2" style="color: #bdc3c7;">Current Time</p>
                            <p class="text-3xl font-bold" style="color: #ffffff; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);" id="currentTime">
                                {{ now()->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Upcoming Appointments Container -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg flex flex-col h-full border border-gray-200">
                <div class="px-4 py-3 rounded-t-lg" style="background-color: #2c3e50;">
                    <h3 class="text-base font-bold text-white">
                        <i class="fas fa-calendar-check mr-2" style="color: #f4d03f;"></i>Upcoming Appointments
                    </h3>
                </div>
                
                <div class="px-4 py-3 space-y-2 overflow-y-auto flex-1" style="max-height: 400px;">
                    @php
                        $upcomingAppointments = $recent_appointments->where('status', 'scheduled')->take(5);
                    @endphp
                    
                    @forelse($upcomingAppointments as $appointment)
                        <a href="{{ route('appointments.show', $appointment->id) }}" 
                           class="block bg-gray-50 hover:bg-blue-50 px-3 py-2.5 rounded border border-gray-200 hover:border-blue-400 transition-all duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-sm" style="color: #2c3e50;">
                                        <i class="fas fa-paw mr-1 text-xs" style="color: #2471a3;"></i>{{ $appointment->pet->name }}
                                    </h4>
                                    <p class="text-xs" style="color: #5d6d7e;">{{ $appointment->pet->owner->user->name }}</p>
                                    <p class="text-xs mt-1" style="color: #5d6d7e;">
                                        <i class="fas fa-stethoscope mr-1"></i>{{ $appointment->service->name }}
                                    </p>
                                </div>
                                <div class="text-right ml-2">
                                    <p class="text-xs font-semibold" style="color: #2c3e50;">
                                        {{ $appointment->appointment_date->format('M d') }}
                                    </p>
                                    <p class="text-xs" style="color: #5d6d7e;">
                                        {{ $appointment->appointment_time }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2 text-gray-300"></i>
                            <p class="text-xs">No upcoming appointments.</p>
                        </div>
                    @endforelse
                </div>
                
                @if($upcomingAppointments->count() > 0)
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <a href="{{ route('doctor.appointments', ['status' => 'scheduled']) }}" class="text-xs font-semibold hover:underline" style="color: #2471a3;">
                        View All Appointments <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium mb-4" style="color: #2c3e50;">Recent Appointments</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #34495e;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: #2c3e50;">
                                {{ $appointment->pet->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                {{ $appointment->pet->owner->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold"
                                    style="color: 
                                    @if($appointment->status === 'scheduled') #d68910
                                    @elseif($appointment->status === 'completed') #52be80
                                    @elseif($appointment->status === 'pending') #2471a3
                                    @elseif($appointment->status === 'cancelled') #ec7063
                                    @else #5d6d7e
                                    @endif;">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center" style="color: #5d6d7e;">
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
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium mb-4" style="color: #2c3e50;">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #2471a3;">
                    <i class="fas fa-plus mr-2"></i>
                    Add Medical Record
                </a>
                <a href="{{ route('doctor.appointments') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #52be80;">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    View Appointments
                </a>
                <a href="{{ route('doctor.patients') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #9b59b6;">
                    <i class="fas fa-paw mr-2"></i>
                    View Patients
                </a>
                <a href="{{ route('doctor.medical-records') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #d68910;">
                    <i class="fas fa-file-medical mr-2"></i>
                    Medical Records
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Prevent table cells from affecting text visibility */
    tbody td {
        vertical-align: middle;
        overflow: visible;
    }
    
    /* Hover effects for cards */
    a[href]:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }
    
    /* Hover effects for quick action buttons */
    .bg-white a:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
</style>

<script>
    // Update time every minute
    setInterval(function() {
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const displayHours = hours % 12 || 12;
        const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
        
        document.getElementById('currentTime').textContent = displayHours + ':' + displayMinutes + ' ' + ampm;
    }, 60000);
</script>
@endsection