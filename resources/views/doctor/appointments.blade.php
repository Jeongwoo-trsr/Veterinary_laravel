@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold" style="color: #2c3e50;">Appointments</h1>
            <p style="color: #5d6d7e;">Manage appointments and scheduling</p>
        </div>
        <a href="{{ route('doctor.appointments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition" style="background-color: #0d5cb6;">
            <i class="fas fa-plus mr-2"></i>
            Schedule Appointment
        </a>
    </div>

    @if(session('success'))
        <div class="border px-4 py-3 rounded" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="border px-4 py-3 rounded" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pending Approvals Section (Collapsible) -->
    @if($pendingAppointments->count() > 0)
    <div class="border shadow-lg rounded-lg p-6" style="background-color: #ffffff; border-color: #d68910; border-left-width: 4px;">
        <button onclick="toggleSection('pendingSection')" class="w-full flex justify-between items-center text-left focus:outline-none hover:opacity-80 transition">
            <h2 class="text-xl font-semibold" style="color: #2c3e50;">
                <i class="fas fa-clock mr-2" style="color: #d68910;"></i>Pending Approval ({{ $pendingAppointments->count() }})
            </h2>
            <i id="pendingIcon" class="fas fa-chevron-down transition-transform duration-200 rotate-180" style="color: #2c3e50;"></i>
        </button>
        
        <div id="pendingSection" class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border divide-y" style="border-color: #e5e7eb;">
                <thead style="background-color: #34495e;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Service</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase" style="color: #ffffff;">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                    @foreach($pendingAppointments as $appt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                            {{ $appt->appointment_date->format('M d, Y') }}<br>
                            <span class="text-xs" style="color: #5d6d7e;">{{ $appt->appointment_time }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">{{ $appt->pet->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appt->pet->owner->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appt->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center gap-4">
                                <button onclick="viewAppointment({{ $appt->id }})" class="transition inline-block" style="color: #3498db;" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('doctor.appointments.approve', $appt->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-white text-sm font-medium rounded transition" style="background-color: #0d5cb6;">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('doctor.appointments.reject', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this appointment?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-white text-sm font-medium rounded transition" style="background-color: #d32f2f;">
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
    <div class="border shadow-lg rounded-lg p-6" style="background-color: #ffffff; border-color: #2471a3; border-left-width: 4px;">
        <button onclick="toggleSection('cancellationSection')" class="w-full flex justify-between items-center text-left focus:outline-none hover:opacity-80 transition">
            <h2 class="text-xl font-semibold" style="color: #2c3e50;">
                <i class="fas fa-exclamation-circle mr-2" style="color: #2471a3;"></i>Cancellation Requests ({{ $cancellationRequests->count() }})
            </h2>
            <i id="cancellationIcon" class="fas fa-chevron-down transition-transform duration-200 rotate-180" style="color: #2c3e50;"></i>
        </button>
        
        <div id="cancellationSection" class="overflow-x-auto mt-4">
            <table class="min-w-full bg-white border divide-y" style="border-color: #e5e7eb;">
                <thead style="background-color: #34495e;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase" style="color: #ffffff;">Requested</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase" style="color: #ffffff;">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                    @foreach($cancellationRequests as $appt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                            {{ $appt->appointment_date->format('M d, Y') }}<br>
                            <span class="text-xs" style="color: #5d6d7e;">{{ $appt->appointment_time }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">{{ $appt->pet->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appt->pet->owner->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appt->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs" style="color: #5d6d7e;">
                            {{ $appt->cancellation_requested_at ? $appt->cancellation_requested_at->diffForHumans() : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center gap-4">
                                <button onclick="viewAppointment({{ $appt->id }})" class="transition inline-block" style="color: #3498db;" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </button>
                                <form action="{{ route('doctor.appointments.approve-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-white text-sm font-medium rounded transition" style="background-color: #0d5cb6;">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('doctor.appointments.decline-cancellation', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Decline this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-white text-sm font-medium rounded transition" style="background-color: #d32f2f;">
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
        <form method="GET" action="{{ route('doctor.appointments') }}" id="filterForm">
            <select name="status" id="statusFilter" class="px-4 py-2 font-bold rounded-lg cursor-pointer transition text-sm border-0" style="background-color: #f4d03f; color: #2c3e50;">
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
                class="px-4 py-2 border rounded-lg focus:outline-none" 
                style="border-color: #d1d5db;"
                placeholder="Search..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-3 py-2 rounded-lg transition"
                style="background-color: #34495e; color: #ffffff;"
            >
                <i class="fas fa-search"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('doctor.appointments') }}" class="px-3 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                <thead style="background-color: #34495e;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Pet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                            <i class="fas fa-paw mr-2" style="color: #3498db;"></i>{{ $appointment->pet->name }}
                            <br><span class="text-xs" style="color: #5d6d7e;">{{ $appointment->pet->species ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appointment->pet->owner->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">{{ $appointment->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #2c3e50;">
                            {{ $appointment->appointment_date->format('M d, Y') }}<br>
                            <span class="text-xs" style="color: #5d6d7e;">{{ $appointment->appointment_time }}</span>
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
                            <div class="flex gap-4 items-center">
                                <!-- View - Always Available -->
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="transition inline-block" style="color: #3498db;" title="View Details">
                                    <i class="fas fa-eye text-lg"></i>
                                </a>
                                
                                @if($appointment->cancellation_status !== 'pending')
                                    <!-- Edit - Show for all statuses, clickable to show dialog for non-scheduled -->
                                    @if($appointment->status === 'scheduled')
                                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="transition inline-block" style="color: #f39c12;" title="Edit Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                    @elseif($appointment->status === 'completed')
                                        <span class="cursor-not-allowed inline-block" style="color: #95a5a6;" onclick="showCompletedDialog()" title="Cannot Edit Completed Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </span>
                                    @elseif($appointment->status === 'cancelled')
                                        <span class="cursor-not-allowed inline-block" style="color: #95a5a6;" onclick="showCancelledDialog()" title="Cannot Edit Cancelled Appointment">
                                            <i class="fas fa-edit text-lg"></i>
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-sm text-center" style="color: #5d6d7e;">
                            No appointments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: #e5e7eb;">
            {{ $appointments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Completed Appointment Dialog Modal -->
<div id="completedDialog" class="hidden fixed inset-0 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="background-color: rgba(44, 62, 80, 0.5);">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" style="background-color: #fadbd8;">
                <i class="fas fa-exclamation-triangle text-2xl" style="color: #e74c3c;"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium mt-5" style="color: #2c3e50;">Cannot Edit Completed Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm" style="color: #5d6d7e;">
                    Completed appointments cannot be edited. The appointment has already been finished and is part of the medical history record.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('completedDialog')" class="px-4 py-2 text-white text-base font-medium rounded-md w-full shadow-sm transition" style="background-color: #e74c3c;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancelled Appointment Dialog Modal -->
<div id="cancelledDialog" class="hidden fixed inset-0 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="background-color: rgba(44, 62, 80, 0.5);">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" style="background-color: #fadbd8;">
                <i class="fas fa-times-circle text-2xl" style="color: #e74c3c;"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium mt-5" style="color: #2c3e50;">Cannot Edit Cancelled Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm" style="color: #5d6d7e;">
                    Cancelled appointments cannot be edited. If you need to reschedule, please create a new appointment instead.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('cancelledDialog')" class="px-4 py-2 text-white text-base font-medium rounded-md w-full shadow-sm transition" style="background-color: #e74c3c;">
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
        // Check if pending section should be expanded
        if (localStorage.getItem('pendingSection_expanded') === 'true') {
            const pendingSection = document.getElementById('pendingSection');
            const pendingIcon = document.getElementById('pendingIcon');
            if (pendingSection) {
                pendingSection.classList.remove('hidden');
                pendingIcon.classList.add('rotate-180');
            }
        }
        
        // Check if cancellation section should be expanded
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
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }
    
    #statusFilter:hover {
        background-color: #f9e79f;
    }

    tbody td {
        vertical-align: middle;
        overflow: visible;
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
    
    a:hover, button:hover {
        opacity: 0.8;
    }
</style>
@endsection