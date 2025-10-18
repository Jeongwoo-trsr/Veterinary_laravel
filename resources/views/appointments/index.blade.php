@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Appointments</h1>
            <p class="text-gray-600">Manage appointments and scheduling</p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isPetOwner())
        <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Schedule Appointment
        </a>
        @endif
    </div>

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Status Filter Dropdown (Left) -->
        <form method="GET" action="{{ route('appointments.index') }}" id="filterForm">
            <div class="relative inline-block">
                <select name="status" id="statusFilter" class="px-4 py-2 bg-yellow-300 text-gray-900 font-bold rounded-lg cursor-pointer hover:bg-yellow-400 transition text-sm">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </form>

        <!-- Search Input (Right) -->
        <form method="GET" action="{{ route('appointments.index') }}" class="flex items-center gap-4">
            <input 
                type="text" 
                name="search" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64" 
                placeholder="Search..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-3 py-2 bg-gray-200 text-gray-600 rounded-lg hover:bg-gray-300 transition"
            >
                <i class="fas fa-search"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('appointments.index') }}" class="px-3 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-paw text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $appointment->pet->species }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $appointment->pet->owner->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $appointment->doctor->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $appointment->service->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div>{{ $appointment->appointment_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $appointment->appointment_time }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block
                                @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                                @elseif($appointment->status === 'confirmed') bg-blue-200 text-blue-800
                                @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                                @elseif($appointment->status === 'pending') bg-purple-200 text-purple-800
                                @else bg-red-200 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-6">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-900 transition" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                <a href="{{ route('appointments.edit', $appointment->id) }}" class="text-orange-500 hover:text-orange-700 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-sm text-gray-500 text-center">
                            No appointments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Auto-submit form when status dropdown changes
    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>

<style>
    /* Yellow dropdown styling */
    #statusFilter {
        font-weight: bold;
        background-color: #FCD34D;
        color: #111827;
        padding-right: 36px;
    }
    
    #statusFilter:hover {
        background-color: #FBBF24;
    }
    
    /* Status badges stay visible */
    span[class*="bg-"] {
        display: inline-block !important;
        white-space: nowrap !important;
    }
    
    /* Remove default dropdown arrow for custom one */
    #statusFilter {
        background-image: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    
    /* Position dropdown arrow inside */
    #statusFilter ~ i {
        pointer-events: none;
    }
</style>
@endsection