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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Pending Approvals Section (Admin Only) -->
    @if(Auth::user()->isAdmin())
        @if($pendingAppointments->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-yellow-800">
                <i class="fas fa-clock mr-2"></i>Pending Approval ({{ $pendingAppointments->count() }})
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingAppointments as $appt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $appt->appointment_date->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-500">{{ $appt->appointment_time }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appt->pet->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->pet->owner->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->doctor->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-4">
                                    <form action="{{ route('admin.appointments.approve', $appt->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition font-medium">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.appointments.reject', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this appointment?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition font-medium">
                                            <i class="fas fa-times mr-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Cancellation Requests Section -->
        @php
            $cancellationRequests = $appointments->getCollection()->where('cancellation_status', 'pending');
        @endphp
        @if($cancellationRequests->count() > 0)
        <div class="bg-purple-50 border border-purple-200 shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-800">
                <i class="fas fa-exclamation-circle mr-2"></i>Cancellation Requests ({{ $cancellationRequests->count() }})
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cancellationRequests as $appt)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $appt->appointment_date->format('M d, Y') }}<br>
                                <span class="text-xs text-gray-500">{{ $appt->appointment_time }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appt->pet->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->pet->owner->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->doctor->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $appt->cancellation_requested_at ? $appt->cancellation_requested_at->diffForHumans() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-4">
                                    <form action="{{ route('appointments.approve-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this cancellation request?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition font-medium">
                                            <i class="fas fa-check mr-1"></i>Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('appointments.decline-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Decline this cancellation request?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition font-medium">
                                            <i class="fas fa-times mr-1"></i>Decline
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Status Filter Dropdown (Left) -->
        <form method="GET" action="{{ route('admin.appointments') }}" id="filterForm">
            <select name="status" id="statusFilter" class="px-4 py-2 bg-yellow-300 text-gray-900 font-bold rounded-lg cursor-pointer hover:bg-yellow-400 transition text-sm border-0">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>Today's Appointments</option>
            </select>
        </form>

        <!-- Search Input (Right) -->
        <form method="GET" action="{{ route('admin.appointments') }}" class="flex items-center gap-4 ml-auto">
            <input 
                type="text" 
                name="search" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
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
            <a href="{{ route('admin.appointments') }}" class="px-3 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            @if($appointment->cancellation_status === 'pending')
                                <span class="px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block bg-purple-200 text-purple-800">
                                    Cancellation Requested
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block
                                    @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-blue-200 text-blue-800
                                    @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                                    @elseif($appointment->status === 'pending') bg-purple-200 text-purple-800
                                    @else bg-red-200 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-6">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-900 transition font-medium" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($appointment->cancellation_status !== 'pending')
                                <a href="{{ route('appointments.edit', $appointment->id) }}" class="text-orange-500 hover:text-orange-700 transition font-medium" title="Edit Appointment">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition font-medium" title="Delete">
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
    #statusFilter {
        background-color: #FCD34D;
        color: #111827;
        font-weight: bold;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }
    
    #statusFilter:hover {
        background-color: #FBBF24;
    }

    /* Status badges stay visible */
    span[class*="bg-"] {
        display: inline-block !important;
        white-space: nowrap !important;
        min-width: fit-content;
    }
    
    /* Prevent text wrapping in table cells */
    tbody td {
        vertical-align: middle;
        overflow: visible;
    }
</style>
@endsection