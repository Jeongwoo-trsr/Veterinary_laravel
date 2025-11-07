@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-2xl font-bold text-[#1e3a5f]">My Appointments</h1>
        <a href="{{ route('pet-owner.appointments.create') }}" class="bg-[#0066cc] hover:bg-[#003d82] text-white px-6 py-3 rounded-lg flex items-center gap-2 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
            <i class="fas fa-plus"></i> Schedule Appointment
        </a>
    </div>

    <div class="flex gap-4 items-center">
        <!-- Status Filter Dropdown -->
        <div class="relative inline-block">
            <select id="statusFilter" class="appearance-none bg-[#fcd34d] text-gray-900 font-semibold px-4 py-2 pr-10 border-2 border-[#d4931d] rounded-lg text-sm cursor-pointer hover:bg-[#d4931d] transition-all shadow-md">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <svg class="absolute right-3 top-3 w-4 h-4 pointer-events-none text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <!-- Search Input -->
        <div class="relative flex-1" style="max-width: 400px;">
            <input type="text" id="searchInput" placeholder="Search pet, service, doctor..." class="w-full px-4 py-2 pr-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] text-sm shadow-sm">
            <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg shadow-md">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-md">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

@if($appointments->count())
    <div class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden shadow-lg">
        <table class="w-full divide-y divide-gray-200" id="appointmentsTable">
            <thead class="bg-[#1e3a5f]">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">DATE & TIME</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">PET</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">SERVICE</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">DOCTOR</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">STATUS</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($appointments as $appt)
                <tr class="appointment-row hover:bg-blue-50 transition-all"
                    data-pet="{{ strtolower($appt->pet->name) }}"
                    data-service="{{ strtolower($appt->service->name ?? '') }}"
                    data-doctor="{{ strtolower($appt->doctor->user->name ?? '') }}"
                    data-status="{{ $appt->status }}">
                    <td class="px-6 py-4 text-sm">
                        <div class="text-sm font-semibold text-[#1e3a5f]">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $appt->appointment_time }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $appt->pet->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $appt->service->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $appt->doctor->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($appt->cancellation_status === 'pending')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-purple-100 text-purple-800 border-2 border-purple-300">
                                <i class="fas fa-hourglass-half mr-1"></i>Cancellation Requested
                            </span>
                        @elseif($appt->status === 'pending')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-yellow-100 text-yellow-800 border-2 border-[#fcd34d]">
                                <i class="fas fa-clock mr-1"></i>Pending Approval
                            </span>
                        @elseif($appt->status === 'scheduled')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-blue-100 text-[#0066cc] border-2 border-[#0066cc]">
                                <i class="fas fa-check-circle mr-1"></i>Scheduled
                            </span>
                        @elseif($appt->status === 'completed')
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-green-100 text-green-800 border-2 border-green-400">
                                <i class="fas fa-check-double mr-1"></i>Completed
                            </span>
                        @else
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap inline-block bg-red-100 text-red-800 border-2 border-red-300">
                                <i class="fas fa-times-circle mr-1"></i>Cancelled
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex gap-3">
                            <!-- View - Always Available -->
                            <button onclick="viewAppointment({{ $appt->id }})" class="text-[#0066cc] hover:text-[#003d82] transition-colors" title="View">
                                <i class="fas fa-eye text-lg"></i>
                            </button>
                            
                            @if($appt->cancellation_status !== 'pending')
                                <!-- Edit - Only for Pending appointments -->
                                @if($appt->status === 'pending')
                                    <button onclick="editAppointment({{ $appt->id }})" class="text-[#d4931d] hover:text-[#b8791a] transition-colors" title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                @elseif($appt->status === 'scheduled')
                                    <button onclick="showScheduledDialog()" class="text-gray-400 cursor-not-allowed" title="Cannot Edit - Approved" disabled>
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                @elseif($appt->status === 'completed')
                                    <button onclick="showCompletedDialog()" class="text-gray-400 cursor-not-allowed" title="Cannot Edit - Completed" disabled>
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                @else
                                    <button onclick="showCancelledDialog()" class="text-gray-400 cursor-not-allowed" title="Cannot Edit - Cancelled" disabled>
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                @endif
                                
                                <!-- Cancel/Request Cancellation - UPDATED LOGIC -->
                                @if($appt->status === 'pending')
                                    <!-- Direct cancel for pending appointments -->
                                    <form action="{{ route('pet-owner.appointments.destroy', $appt->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Cancel Appointment">
                                            <i class="fas fa-times-circle text-lg"></i>
                                        </button>
                                    </form>
                                @elseif($appt->status === 'scheduled')
                                    <!-- Request cancellation for scheduled appointments -->
                                    <button onclick="openCancelModal({{ $appt->id }})" class="text-red-600 hover:text-red-800 transition-colors" title="Request Cancellation">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </button>
                                @else
                                    <button class="text-gray-400 cursor-not-allowed" title="Cannot Cancel" disabled>
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </button>
                                @endif
                            @else
                                <!-- Show disabled buttons when cancellation is pending -->
                                <button class="text-gray-400 cursor-not-allowed" title="Cancellation Pending" disabled>
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                <button class="text-gray-400 cursor-not-allowed" title="Cancellation Pending" disabled>
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="noResults" class="text-center text-gray-500 py-8 hidden">
            <i class="fas fa-search text-3xl text-gray-300 mb-2"></i>
            <p>No appointments match your search criteria.</p>
        </div>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
@else
    <div class="bg-white border-2 border-gray-200 rounded-lg p-8 text-center shadow-lg">
        <i class="fas fa-calendar-times text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-600 text-sm mb-4">You don't have any appointments yet.</p>
        <a href="{{ route('pet-owner.appointments.create') }}" class="inline-block px-6 py-3 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] text-sm font-semibold shadow-md hover:shadow-lg transition-all transform hover:scale-105">
            <i class="fas fa-calendar-plus mr-2"></i>Schedule First Appointment
        </a>
    </div>
@endif

<!-- Cancel Modal -->
<div id="cancelModal" class="modal-overlay">
    <div class="modal-content-wrapper">
        <div class="modal-inner">
            <div class="modal-header-section">
                <h3 class="modal-title" id="modalTitle">Cancel Appointment</h3>
                <button type="button" class="modal-close-btn" onclick="closeCancelModal()">&times;</button>
            </div>
            
            <form id="cancelForm" method="POST">
                @csrf
                <div id="modalMessage" class="mb-4 p-3 rounded-lg"></div>
                
                <div class="modal-form-group" id="reasonSection">
                    <label class="modal-label">Reason for Cancellation <span id="reasonRequired" class="text-red-500">*</span></label>
                    <select id="cancelReasonSelect" name="cancellation_reason" class="modal-select" required>
                        <option value="">-- Select Reason --</option>
                        <option value="Schedule Conflict">Schedule Conflict</option>
                        <option value="Pet is Feeling Better">Pet is Feeling Better</option>
                        <option value="Financial Reasons">Financial Reasons</option>
                        <option value="Found Another Vet">Found Another Vet</option>
                        <option value="Emergency">Emergency</option>
                        <option value="other">Other (Please specify)</option>
                    </select>
                </div>
                
                <div id="otherReasonContainer" class="modal-form-group" style="display: none;">
                    <label class="modal-label">Please specify your reason <span class="text-red-500">*</span></label>
                    <textarea id="otherReasonText" class="modal-textarea" rows="3" maxlength="500" placeholder="Enter your reason here..."></textarea>
                    <p class="modal-helper-text">Maximum 500 characters</p>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="modal-btn-secondary" onclick="closeCancelModal()">Close</button>
                    <button type="submit" class="modal-btn-danger" id="submitBtn">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scheduled Appointment Dialog -->
<div id="scheduledDialog" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border-2 border-[#0066cc] w-96 shadow-2xl rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-100">
                <i class="fas fa-info-circle text-[#0066cc] text-3xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-bold text-[#1e3a5f] mt-5">Cannot Edit Approved Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-600">
                    This appointment has been approved and scheduled. To make changes, please contact the clinic directly or request a cancellation.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('scheduledDialog')" class="px-4 py-2 bg-[#0066cc] text-white text-base font-semibold rounded-lg w-full shadow-md hover:bg-[#003d82] transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Completed Appointment Dialog -->
<div id="completedDialog" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border-2 border-green-500 w-96 shadow-2xl rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-100">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-bold text-[#1e3a5f] mt-5">Cannot Edit Completed Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-600">
                    This appointment has been completed and is part of your pet's medical history. It cannot be modified.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('completedDialog')" class="px-4 py-2 bg-green-600 text-white text-base font-semibold rounded-lg w-full shadow-md hover:bg-green-700 transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancelled Appointment Dialog -->
<div id="cancelledDialog" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border-2 border-red-500 w-96 shadow-2xl rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100">
                <i class="fas fa-times-circle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-lg leading-6 font-bold text-[#1e3a5f] mt-5">Cannot Edit Cancelled Appointment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-600">
                    This appointment has been cancelled. If you need to reschedule, please create a new appointment instead.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closeDialog('cancelledDialog')" class="px-4 py-2 bg-red-600 text-white text-base font-semibold rounded-lg w-full shadow-md hover:bg-red-700 transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<style>
#statusFilter:hover { 
    background: #d4931d !important;
}

.status-badge { 
    display: inline-block !important; 
    white-space: nowrap !important; 
}

/* Modal Styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 999999 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow-y: auto;
}

.modal-overlay.active {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-content-wrapper {
    width: 90%;
    max-width: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
    margin: 20px auto;
    position: relative;
    border: 2px solid #0066cc;
}

.modal-inner {
    padding: 24px;
    position: relative;
}

.modal-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.modal-title {
    font-size: 20px;
    font-weight: bold;
    margin: 0;
    color: #1e3a5f;
}

.modal-close-btn {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #9CA3AF;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    transition: color 0.2s;
}

.modal-close-btn:hover {
    color: #1e3a5f;
}

.modal-form-group {
    margin-bottom: 16px;
}

.modal-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
    color: #374151;
}

.modal-select,
.modal-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #D1D5DB;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.modal-select:focus,
.modal-textarea:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.modal-textarea {
    resize: vertical;
}

.modal-helper-text {
    font-size: 12px;
    color: #6B7280;
    margin-top: 4px;
    margin-bottom: 0;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 2px solid #e5e7eb;
}

.modal-btn-secondary,
.modal-btn-danger {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s;
}

.modal-btn-secondary {
    background-color: #E5E7EB;
    color: #374151;
}

.modal-btn-secondary:hover {
    background-color: #D1D5DB;
}

.modal-btn-danger {
    background: #DC2626;
    color: white;
    box-shadow: 0 4px 6px rgba(220, 38, 38, 0.2);
}

.modal-btn-danger:hover {
    background: #B91C1C;
    box-shadow: 0 6px 8px rgba(220, 38, 38, 0.3);
}

/* Disabled button styling */
button:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
}
</style>

<script>
// Define all functions in global scope
window.viewAppointment = function(id) {
    window.location.href = '/appointments/' + id;
};

window.editAppointment = function(id) {
    window.location.href = '/appointments/' + id + '/edit';
};

window.openCancelModal = function(appointmentId) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    
    if (!modal || !form) return;
    
    form.action = '{{ url("/pet-owner/appointments") }}/' + appointmentId + '/request-cancellation';
    modal.classList.add('active');
};

window.closeCancelModal = function() {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    const select = document.getElementById('cancelReasonSelect');
    const otherContainer = document.getElementById('otherReasonContainer');
    const otherText = document.getElementById('otherReasonText');
    
    if (modal) modal.classList.remove('active');
    if (select) select.value = '';
    if (otherContainer) otherContainer.style.display = 'none';
    if (otherText) otherText.value = '';
};

window.showScheduledDialog = function() {
    document.getElementById('scheduledDialog').classList.remove('hidden');
};

window.showCompletedDialog = function() {
    document.getElementById('completedDialog').classList.remove('hidden');
};

window.showCancelledDialog = function() {
    document.getElementById('cancelledDialog').classList.remove('hidden');
};

window.closeDialog = function(dialogId) {
    document.getElementById(dialogId).classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancelModal');
    const cancelForm = document.getElementById('cancelForm');
    const cancelReasonSelect = document.getElementById('cancelReasonSelect');
    const otherReasonContainer = document.getElementById('otherReasonContainer');
    const otherReasonText = document.getElementById('otherReasonText');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const noResults = document.getElementById('noResults');
    
    // Show/hide other reason textarea
    if (cancelReasonSelect) {
        cancelReasonSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                otherReasonContainer.style.display = 'block';
                if (otherReasonText) otherReasonText.required = true;
            } else {
                otherReasonContainer.style.display = 'none';
                if (otherReasonText) otherReasonText.required = false;
            }
        });
    }

    // Handle form submission
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            if (cancelReasonSelect.value === 'other') {
                e.preventDefault();
                const reason = otherReasonText.value.trim();
                
                if (!reason) {
                    alert('Please specify your reason for cancellation.');
                    return;
                }
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'cancellation_reason';
                hiddenInput.value = reason;
                cancelForm.appendChild(hiddenInput);
                
                cancelForm.submit();
            }
        });
    }

    // Close modal when clicking outside
    if (cancelModal) {
        cancelModal.addEventListener('click', function(e) {
            if (e.target === cancelModal) {
                closeCancelModal();
            }
        });
    }

    // Close modals with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancelModal();
            closeDialog('scheduledDialog');
            closeDialog('completedDialog');
            closeDialog('cancelledDialog');
        }
    });

    // Close info dialogs when clicking outside
    ['scheduledDialog', 'completedDialog', 'cancelledDialog'].forEach(dialogId => {
        const dialog = document.getElementById(dialogId);
        if (dialog) {
            dialog.addEventListener('click', function(e) {
                if (e.target === dialog) {
                    closeDialog(dialogId);
                }
            });
        }
    });

    // Filter appointments
    function filterAppointments() {
        const search = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const status = statusFilter ? statusFilter.value.toLowerCase() : '';
        let visibleCount = 0;

        document.querySelectorAll('.appointment-row').forEach(function(row) {
            const pet = row.dataset.pet || '';
            const service = row.dataset.service || '';
            const doctor = row.dataset.doctor || '';
            const rowStatus = row.dataset.status.toLowerCase();
            
            const matchesSearch = !search || 
                pet.includes(search) || 
                service.includes(search) || 
                doctor.includes(search);
            
            const matchesStatus = !status || rowStatus === status;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = 'table-row';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterAppointments);
    if (statusFilter) statusFilter.addEventListener('change', filterAppointments);
});
</script>
@endsection