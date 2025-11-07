@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-[#1e3a5f] shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
        <p class="text-gray-200">Welcome to the Veterinary Clinic Management System</p>
    </div>

    <!-- Statistics Cards - Now Clickable -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.pets') }}" class="bg-[#0d47a1] overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-paw text-[#ffd700] text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-200">Total Pets</dt>
                            <dd class="mt-1 text-2xl font-semibold text-white">{{ $stats['total_pets'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.pet-owners') }}" class="bg-[#d4911e] overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-100">Pet Owners</dt>
                            <dd class="mt-1 text-2xl font-semibold text-white">{{ $stats['total_owners'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.appointments') }}" class="bg-[#2c3e50] overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-[#ffd700] text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-200">Total Appointments</dt>
                            <dd class="mt-1 text-2xl font-semibold text-white">{{ $stats['total_appointments'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.medical-records') }}" class="bg-[#1e3a5f] overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical text-[#ffd700] text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-200">Medical Records</dt>
                            <dd class="mt-1 text-2xl font-semibold text-white">{{ $stats['total_medical_records'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Appointments -->
    <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f]">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Recent Appointments</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1e3a5f]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $appointment->pet->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->pet->owner->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->doctor->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold"
                                    style="color: 
                                    @if($appointment->status === 'scheduled') #d68910
                                    @elseif($appointment->status === 'completed') #52be80
                                    @elseif($appointment->status === 'confirmed') #2471a3
                                    @elseif($appointment->status === 'in_progress') #34495e
                                    @elseif($appointment->status === 'cancelled') #ec7063
                                    @else #5d6d7e
                                    @endif;">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
    <div class="bg-white shadow-lg rounded-lg border-t-4 border-[#1e3a5f]">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-[#1e3a5f] mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('pets.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#0d47a1] hover:bg-[#1565c0] transition">
                    <i class="fas fa-plus mr-2"></i>
                    Add Pet
                </a>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#d4911e] hover:bg-[#b8860b] transition">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Schedule Appointment
                </a>
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-900 bg-[#ffd700] hover:bg-[#ffc107] transition">
                    <i class="fas fa-chart-bar mr-2"></i>
                    View Reports
                </a>
            </div>
        </div>
    </div>
</div>
@endsection