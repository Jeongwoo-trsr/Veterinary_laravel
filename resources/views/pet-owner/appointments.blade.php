@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-2xl font-bold">My Appointments</h1>
        <a href="{{ route('pet-owner.appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
            + Schedule Appointment
        </a>
    </div>

    <div class="flex gap-4 items-center">
        <!-- Status Filter Dropdown -->
        <div class="relative inline-block">
            <select id="statusFilter" style="appearance: none; background-color: #FCD34D; color: #000; font-weight: 600; padding: 8px 40px 8px 16px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <svg class="absolute right-3 top-3 w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <!-- Search Input -->
        <div class="relative flex-1" style="max-width: 400px;">
            <input type="text" id="searchInput" placeholder="Search pet, owner, service..." class="w-full px-4 py-2 pr-10 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
@endif

@if($appointments->count())
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <table class="w-full divide-y divide-gray-200" id="appointmentsTable">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">DATE & TIME</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">PET</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">SERVICE</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">DOCTOR</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STATUS</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($appointments as $appt)
                <tr class="appointment-row hover:bg-gray-50 transition"
                    data-pet="{{ strtolower($appt->pet->name) }}"
                    data-service="{{ strtolower($appt->service->name ?? '') }}"
                    data-doctor="{{ strtolower($appt->doctor->user->name ?? '') }}"
                    data-status="{{ $appt->status }}">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $appt->appointment_time }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $appt->pet->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $appt->service->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $appt->doctor->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($appt->cancellation_status === 'pending')
                            <span class="status-badge text-purple-700 font-semibold">Cancellation Requested</span>
                        @elseif($appt->status === 'pending')
                            <span class="status-badge text-yellow-700 font-semibold">Pending</span>
                        @elseif($appt->status === 'scheduled')
                            <span class="status-badge text-orange-700 font-semibold">Scheduled</span>
                        @elseif($appt->status === 'completed')
                            <span class="status-badge text-green-600 font-semibold">Completed</span>
                        @else
                            <span class="status-badge text-red-600 font-semibold">Cancelled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex gap-3 justify-end">
                            <button onclick="viewAppointment({{ $appt->id }})" class="text-blue-600 hover:text-blue-900" title="View"><i class="fas fa-eye"></i></button>
                            @if($appt->status === 'pending')
                                <button onclick="editAppointment({{ $appt->id }})" class="text-orange-500 hover:text-orange-700" title="Edit"><i class="fas fa-edit"></i></button>
                                <button onclick="openCancelModal({{ $appt->id }})" class="text-red-600 hover:text-red-900" title="Cancel Appointment"><i class="fas fa-times-circle"></i></button>
                            @elseif($appt->status === 'scheduled' && $appt->cancellation_status !== 'pending')
                                <button onclick="openCancelModal({{ $appt->id }})" class="text-red-600 hover:text-red-900" title="Request Cancellation"><i class="fas fa-times-circle"></i></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="noResults" class="text-center text-gray-500 py-8 hidden">
            No appointments match your search criteria.
        </div>
    </div>

    <div class="mt-4">
        {{ $appointments->links() }}
    </div>
@else
    <div class="bg-white border border-gray-200 rounded p-8 text-center">
        <p class="text-gray-600 text-sm mb-4">You don't have any appointments yet.</p>
        <a href="{{ route('pet-owner.appointments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">Schedule First Appointment</a>
    </div>
@endif
@endsection

{{-- Modal placed OUTSIDE @section('content') to avoid flex layout issues --}}
@section('modals')
<div id="cancelModal" class="modal-overlay">
    <div class="modal-content-wrapper">
        <div class="modal-inner">
            <div class="modal-header-section">
                <h3 class="modal-title">Request Cancellation</h3>
                <button type="button" class="modal-close-btn" onclick="closeCancelModal()">&times;</button>
            </div>
            
            <form id="cancelForm" method="POST">
                @csrf
                <div class="modal-form-group">
                    <label class="modal-label">Reason for Cancellation <span class="text-red-500">*</span></label>
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
                    <button type="button" class="modal-btn-secondary" onclick="closeCancelModal()">Cancel</button>
                    <button type="submit" class="modal-btn-danger">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
#statusFilter:hover { 
    background-color: #FBBF24 !important; 
}

.status-badge { 
    display: inline-block !important; 
    white-space: nowrap !important; 
}

/* Modal Styles - Using unique class names to avoid conflicts */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.5);
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
    border-radius: 8px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    margin: 20px auto;
    position: relative;
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
}

.modal-title {
    font-size: 20px;
    font-weight: bold;
    margin: 0;
    color: #111827;
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
}

.modal-close-btn:hover {
    color: #6B7280;
}

.modal-form-group {
    margin-bottom: 16px;
}

.modal-label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 14px;
    color: #374151;
}

.modal-select,
.modal-textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
}

.modal-select:focus,
.modal-textarea:focus {
    outline: none;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
}

.modal-btn-secondary,
.modal-btn-danger {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    transition: opacity 0.2s;
}

.modal-btn-secondary {
    background-color: #E5E7EB;
    color: #374151;
}

.modal-btn-secondary:hover {
    background-color: #D1D5DB;
}

.modal-btn-danger {
    background-color: #DC2626;
    color: white;
}

.modal-btn-danger:hover {
    background-color: #B91C1C;
}

body.modal-open {
    /* Removed body locking styles that were causing layout issues */
}
</style>

<script>
console.log('=== APPOINTMENTS SCRIPT LOADED ===');

// Define all functions in global scope IMMEDIATELY
window.openCancelModal = function(appointmentId) {
    console.log('openCancelModal called with ID:', appointmentId);
    
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    
    if (!modal) {
        console.error('Modal element not found!');
        alert('ERROR: Modal not found. Check console.');
        return;
    }
    
    if (!form) {
        console.error('Form element not found!');
        alert('ERROR: Form not found. Check console.');
        return;
    }
    
    // Set form action
    form.action = '{{ url("/pet-owner/appointments") }}/' + appointmentId + '/request-cancellation';
    console.log('Form action set to:', form.action);
    
    // Show modal
    modal.classList.add('active');
    // document.body.classList.add('modal-open'); // REMOVED - causing layout issues
    
    console.log('Modal displayed. Classes:', modal.className);
};

window.closeCancelModal = function() {
    console.log('closeCancelModal called');
    
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    const select = document.getElementById('cancelReasonSelect');
    const otherContainer = document.getElementById('otherReasonContainer');
    const otherText = document.getElementById('otherReasonText');
    
    if (modal) {
        modal.classList.remove('active');
        // document.body.classList.remove('modal-open'); // REMOVED - was causing layout issues
    }
    
    if (select) select.value = '';
    if (otherContainer) otherContainer.style.display = 'none';
    if (otherText) otherText.value = '';
    
    console.log('Modal closed');
};

window.viewAppointment = function(id) {
    window.location.href = '/appointments/' + id;
};

window.editAppointment = function(id) {
    window.location.href = '/appointments/' + id + '/edit';
};

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const cancelModal = document.getElementById('cancelModal');
    const cancelForm = document.getElementById('cancelForm');
    const cancelReasonSelect = document.getElementById('cancelReasonSelect');
    const otherReasonContainer = document.getElementById('otherReasonContainer');
    const otherReasonText = document.getElementById('otherReasonText');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const noResults = document.getElementById('noResults');
    
    console.log('Modal element found:', !!cancelModal);
    console.log('Form element found:', !!cancelForm);
    
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
                
                // Create hidden input with the custom reason
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'cancellation_reason';
                hiddenInput.value = reason;
                cancelForm.appendChild(hiddenInput);
                
                // Submit the form
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

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && cancelModal && cancelModal.classList.contains('active')) {
            closeCancelModal();
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
    
    console.log('All event listeners attached');
});

console.log('=== SCRIPT INITIALIZATION COMPLETE ===');
</script>
@endsection