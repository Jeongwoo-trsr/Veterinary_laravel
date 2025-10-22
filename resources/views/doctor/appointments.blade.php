@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Appointments</h1>
            <p class="text-gray-600">Manage your appointments</p>
        </div>
        <a href="{{ route('doctor.appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Schedule Appointment
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pending Approvals Section -->
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center gap-3">
                                <button onclick="viewAppointment({{ $appt->id }})" class="text-blue-600 hover:text-blue-800 transition" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('doctor.appointments.approve', $appt->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Approve">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('doctor.appointments.reject', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this appointment?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Reject">
                                        <i class="fas fa-times-circle text-lg"></i>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appt->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $appt->cancellation_requested_at ? $appt->cancellation_requested_at->diffForHumans() : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center gap-3">
                                <button onclick="viewAppointment({{ $appt->id }})" class="text-blue-600 hover:text-blue-800 transition" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('appointments.approve-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Approve Cancellation">
                                        <i class="fas fa-check-circle text-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('appointments.decline-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Decline this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Decline Cancellation">
                                        <i class="fas fa-times-circle text-lg"></i>
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

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Status Filter Dropdown (Left) -->
        <form method="GET" action="{{ route('doctor.appointments') }}" id="filterForm">
            <select name="status" id="statusFilter" class="px-4 py-2 bg-yellow-300 text-gray-900 font-bold rounded-lg cursor-pointer hover:bg-yellow-400 transition text-sm border-0">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>Today's Appointments</option>
            </select>
        </form>

        <!-- Search Input (Right) -->
        <form method="GET" action="{{ route('doctor.appointments') }}" class="flex items-center gap-4 ml-auto">
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
            <a href="{{ route('doctor.appointments') }}" class="px-3 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $appointment->appointment_date->format('M d, Y') }}<br>
                            <span class="text-xs text-gray-500">{{ $appointment->appointment_time }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->pet->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appointment->pet->owner->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $appointment->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($appointment->cancellation_status === 'pending')
                                <span class="px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block bg-purple-200 text-purple-800">
                                    Cancellation Requested
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-bold rounded-md whitespace-nowrap inline-block
                                    @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                                    @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                                    @else bg-red-200 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-3">
                                <!-- View - Always Available -->
                                <button onclick="viewAppointment({{ $appointment->id }})" class="text-blue-600 hover:text-blue-800 transition" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                
                                @if($appointment->cancellation_status !== 'pending')
                                    <!-- Edit - Only for Scheduled -->
                                    @if($appointment->status === 'scheduled')
                                        <button onclick="editAppointment({{ $appointment->id }})" class="text-orange-500 hover:text-orange-700 transition" title="Edit Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                    @elseif($appointment->status === 'completed')
                                        <button onclick="showCompletedDialog()" class="text-gray-400 cursor-not-allowed" title="Cannot Edit Completed" disabled>
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                    @elseif($appointment->status === 'cancelled')
                                        <button onclick="showCancelledDialog()" class="text-gray-400 cursor-not-allowed" title="Cannot Edit Cancelled" disabled>
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Delete - Only for Scheduled -->
                                    @if($appointment->status === 'scheduled')
                                        <form method="POST" action="{{ route('doctor.appointments.destroy', $appointment->id) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="text-gray-400 cursor-not-allowed" title="Cannot Delete" disabled>
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-sm text-gray-500 text-center">
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
            {{ $appointments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Completed Appointment Dialog Modal -->
<div id="completedDialog" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-5">Cannot Edit Completed Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Completed appointments cannot be edited. The appointment has already been finished and is part of the medical history record.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('completedDialog')" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancelled Appointment Dialog Modal -->
<div id="cancelledDialog" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-times-circle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-5">Cannot Edit Cancelled Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Cancelled appointments cannot be edited. If you need to reschedule, please create a new appointment instead.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('cancelledDialog')" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-submit form when status dropdown changes
    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // View appointment
    function viewAppointment(id) {
        window.location.href = '/appointments/' + id;
    }

    // Edit appointment
    function editAppointment(id) {
        window.location.href = '/appointments/' + id + '/edit';
    }

    // Show completed appointment dialog
    function showCompletedDialog() {
        document.getElementById('completedDialog').classList.remove('hidden');
    }

    // Show cancelled appointment dialog
    function showCancelledDialog() {
        document.getElementById('cancelledDialog').classList.remove('hidden');
    }

    // Close dialog
    function closeDialog(dialogId) {
        document.getElementById(dialogId).classList.add('hidden');
    }

    // Close dialog when clicking outside
    ['completedDialog', 'cancelledDialog'].forEach(dialogId => {
        document.getElementById(dialogId)?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDialog(dialogId);
            }
        });
    });

    // Close dialog with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDialog('completedDialog');
            closeDialog('cancelledDialog');
        }
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

    /* Consistent icon sizing */
    .fas {
        font-size: 1.125rem;
    }

    /* Disabled button styling */
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
    }
</style>
@endsection
