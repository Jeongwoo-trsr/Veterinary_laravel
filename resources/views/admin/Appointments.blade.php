@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[#1e3a5f]">Appointments</h1>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isPetOwner())
        <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#0d47a1] hover:bg-[#1565c0] transition">
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

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pending Approvals Section (Collapsible) -->
    @if($pendingAppointments->count() > 0)
    <div class="bg-white border-l-4 border-[#d4911e] shadow-lg rounded-lg p-6">
        <button onclick="toggleSection('pendingSection')" class="w-full flex justify-between items-center text-left focus:outline-none hover:opacity-80 transition">
            <h2 class="text-xl font-semibold text-[#1e3a5f]">
                <i class="fas fa-clock mr-2"></i>Pending Approval ({{ $pendingAppointments->count() }})
            </h2>
            <i id="pendingIcon" class="fas fa-chevron-down text-[#1e3a5f] transition-transform duration-200 rotate-180"></i>
        </button>
        
        <div id="pendingSection" class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[#2c3e50]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Service</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Action</th>
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
                            <div class="flex justify-center gap-4">
                                <button onclick="viewAppointment({{ $appt->id }})" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('admin.appointments.approve', $appt->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-[#0d47a1] text-white text-sm font-medium rounded hover:bg-[#1565c0] transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.appointments.reject', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this appointment?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                                        Decline
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

    <!-- Cancellation Requests Section (Collapsible) -->
    @php
        $cancellationRequests = $appointments->getCollection()->where('cancellation_status', 'pending');
    @endphp
    @if($cancellationRequests->count() > 0)
    <div class="bg-white border-l-4 border-[#2c3e50] shadow-lg rounded-lg p-6">
        <button onclick="toggleSection('cancellationSection')" class="w-full flex justify-between items-center text-left focus:outline-none hover:opacity-80 transition">
            <h2 class="text-xl font-semibold text-[#1e3a5f]">
                <i class="fas fa-exclamation-circle mr-2"></i>Cancellation Requests ({{ $cancellationRequests->count() }})
            </h2>
            <i id="cancellationIcon" class="fas fa-chevron-down text-[#1e3a5f] transition-transform duration-200 rotate-180"></i>
        </button>
        
        <div id="cancellationSection" class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[#1e3a5f]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Requested</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Action</th>
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
                            <div class="flex justify-center gap-4">
                                <button onclick="viewAppointment({{ $appt->id }})" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('admin.appointments.approve-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-[#0d47a1] text-white text-sm font-medium rounded hover:bg-[#1565c0] transition">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.appointments.decline-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Decline this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition">
                                        Decline
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
        <form method="GET" action="{{ route('admin.appointments') }}" id="filterForm">
            <div class="relative inline-block">
                <select name="status" id="statusFilter" class="px-4 py-2 bg-[#ffd700] text-gray-900 font-bold rounded-lg cursor-pointer hover:bg-[#ffc107] transition text-sm">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>Today's Appointments</option>
                </select>
            </div>
        </form>

        <!-- Search Input (Right) -->
        <form method="GET" action="{{ route('admin.appointments') }}" class="flex items-center gap-2">
            <input 
                type="text" 
                name="search" 
                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1] w-48" 
                placeholder="Search..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition"
            >
                <i class="fas fa-search text-sm"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.appointments') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times text-sm"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border-t-4 border-[#1e3a5f]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#1e3a5f]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-[#0d47a1] bg-opacity-10 flex items-center justify-center">
                                        <i class="fas fa-paw text-[#0d47a1]"></i>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($appointment->cancellation_status === 'pending')
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-purple-100 text-purple-800 border-2 border-purple-300">
                                    <i class="fas fa-hourglass-half mr-1"></i>Cancellation Requested
                                </span>
                            @elseif($appointment->status === 'pending')
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-yellow-100 text-yellow-800 border-2 border-yellow-400">
                                    <i class="fas fa-clock mr-1"></i>Pending Approval
                                </span>
                            @elseif($appointment->status === 'scheduled')
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-blue-100 text-blue-800 border-2 border-blue-400">
                                    <i class="fas fa-check-circle mr-1"></i>Scheduled
                                </span>
                            @elseif($appointment->status === 'confirmed')
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-blue-100 text-blue-800 border-2 border-blue-400">
                                    <i class="fas fa-check-circle mr-1"></i>Confirmed
                                </span>
                            @elseif($appointment->status === 'completed')
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-green-100 text-green-800 border-2 border-green-400">
                                    <i class="fas fa-check-double mr-1"></i>Completed
                                </span>
                            @else
                                <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-red-100 text-red-800 border-2 border-red-300">
                                    <i class="fas fa-times-circle mr-1"></i>Cancelled
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-4">
                                <!-- View - Always show -->
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="text-[#0d47a1] hover:text-[#1565c0] transition inline-block" title="View">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                
                                @if($appointment->cancellation_status !== 'pending')
                                    <!-- Edit - Show for all statuses, but disabled for completed/cancelled -->
                                    @if($appointment->status === 'scheduled')
                                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="text-[#d4911e] hover:text-[#b8860b] transition inline-block" title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                    @elseif($appointment->status === 'completed')
                                        <span class="text-gray-400 cursor-not-allowed inline-block" onclick="showCompletedDialog()" title="Cannot Edit Completed Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="text-gray-400 cursor-not-allowed inline-block" onclick="showCancelledDialog()" title="Cannot Edit Cancelled Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </span>
                                    @endif
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
    // Toggle section visibility
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.getElementById(sectionId.replace('Section', 'Icon'));
        
        if (section.classList.contains('hidden')) {
            section.classList.remove('hidden');
            icon.classList.add('rotate-180');
            localStorage.setItem(sectionId + '_expanded', 'true');
        } else {
            section.classList.add('hidden');
            icon.classList.remove('rotate-180');
            localStorage.setItem(sectionId + '_expanded', 'false');
        }
    }

    // Restore toggle states on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('pendingSection_expanded') === 'true') {
            const pendingSection = document.getElementById('pendingSection');
            const pendingIcon = document.getElementById('pendingIcon');
            if (pendingSection) {
                pendingSection.classList.remove('hidden');
                pendingIcon.classList.add('rotate-180');
            }
        }
        
        if (localStorage.getItem('cancellationSection_expanded') === 'true') {
            const cancellationSection = document.getElementById('cancellationSection');
            const cancellationIcon = document.getElementById('cancellationIcon');
            if (cancellationSection) {
                cancellationSection.classList.remove('hidden');
                cancellationIcon.classList.add('rotate-180');
            }
        }
    });

    // Auto-submit form when status dropdown changes
    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // View appointment
    function viewAppointment(id) {
        window.location.href = '/appointments/' + id;
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
        font-weight: bold;
        background-color: #ffd700;
        color: #111827;
        padding-right: 36px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
    }
    
    #statusFilter:hover {
        background-color: #ffc107;
    }
    
    span[class*="bg-"] {
        display: inline-block !important;
        white-space: nowrap !important;
    }

    .fas {
        font-size: 1.125rem;
    }

    button:disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
    }

    .cursor-not-allowed {
        cursor: not-allowed !important;
        opacity: 0.5;
    }

    .cursor-not-allowed:hover {
        opacity: 0.5 !important;
        transform: none !important;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
@endsection