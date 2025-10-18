@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-blue-100 shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600">Welcome to the Veterinary Clinic Management System</p>
    </div>

    <!-- Statistics Cards - Now Clickable -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.pets') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-paw text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Total Pets</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_pets'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.pet-owners') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Pet Owners</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_owners'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.doctors') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-md text-purple-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Doctors</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_doctors'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Total Appointments</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_appointments'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Additional Stats - Now Clickable -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Pending Appointments</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['pending_appointments'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Completed Appointments</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['completed_appointments'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.medical-records') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition cursor-pointer">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical text-red-600 text-xl"></i>
                    </div>
                    <div class="flex-1 text-center">
                        <dl>
                            <dt class="text-xs font-medium text-gray-500">Medical Records</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total_medical_records'] }}</dd>
                        </dl>
                    </div>
                    <div class="w-6"></div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
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
                                {{ $appointment->doctor->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                                @if($appointment->status == 'scheduled') text-yellow-600
                                @elseif($appointment->status == 'confirmed') text-blue-600
                                @elseif($appointment->status == 'in_progress') text-purple-600
                                @elseif($appointment->status == 'completed') text-green-600
                                @elseif($appointment->status == 'cancelled') text-red-600
                                @else text-gray-600
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
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
    <div class="bg-blue-100 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('pets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Pet
                </a>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Schedule Appointment
                </a>
                <a href="{{ route('admin.reports') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-chart-bar mr-2"></i>
                    View Reports
                </a>
                <a href="{{ route('admin.services') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                    <i class="fas fa-cogs mr-2"></i>
                    Manage Services
                </a>
            </div>
        </div>
    </div>
</div>
@endsection