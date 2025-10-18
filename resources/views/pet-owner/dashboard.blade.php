@extends('layouts.app')

@section('title', 'Pet Owner Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-blue-100 shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Pet Owner Dashboard</h1>
        <p class="text-gray-600">Welcome, {{ Auth::user()->name }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- My Pets Card -->
        <a href="{{ route('pet-owner.pets') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-paw text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">My Pets</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_pets'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Total Appointments Card -->
        <a href="{{ route('pet-owner.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
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

        <!-- Upcoming Appointments Card -->
        <a href="{{ route('pet-owner.appointments', ['status' => 'scheduled']) }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['upcoming_appointments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <!-- Medical Records Card -->
        <a href="{{ route('pet-owner.medical-records') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
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
                <table class="min-w-full divide-y divide-blue-200">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Pet</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Doctor</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Service</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Date & Time</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-blue-100 divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $appointment->pet->name }}
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
                                <span class="text-sm font-semibold
                                    @if($appointment->status === 'scheduled') text-yellow-600
                                    @elseif($appointment->status === 'confirmed') text-blue-600
                                    @elseif($appointment->status === 'in_progress') text-purple-600
                                    @elseif($appointment->status === 'completed') text-green-600
                                    @elseif($appointment->status === 'pending') text-orange-600
                                    @else text-red-600
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
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('pets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add Pet
                </a>
                
                <a href="{{ route('pet-owner.pets') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-paw mr-2"></i>
                    My Pets
                </a>
                <a href="{{ route('pet-owner.appointments') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    My Appointments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection