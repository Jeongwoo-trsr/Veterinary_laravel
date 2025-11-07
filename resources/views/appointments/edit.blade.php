@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold" style="color: #1e3a5f;">Edit Appointment</h1>
        @if(Auth::user()->isDoctor())
            <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @elseif(Auth::user()->isPetOwner())
            <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @else
            <a href="{{ route('admin.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @endif
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        @if($errors->any())
            <div class="border px-4 py-3 rounded mb-4" style="background-color: #fee2e2; border-color: #ef4444; color: #991b1b;">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Appointment Information (Read-only) -->
        <div class="mb-6 p-4 rounded-lg" style="background-color: #f8f9fa;">
            <h3 class="text-lg font-semibold mb-3" style="color: #1e3a5f;">Appointment Details</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium" style="color: #6b7280;">Pet Name</label>
                    <p style="color: #1e3a5f;">{{ $appointment->pet->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium" style="color: #6b7280;">Owner</label>
                    <p style="color: #1e3a5f;">{{ $appointment->pet->owner->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium" style="color: #6b7280;">Doctor</label>
                    <p style="color: #1e3a5f;">{{ $appointment->doctor->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium" style="color: #6b7280;">Service</label>
                    <p style="color: #1e3a5f;">{{ $appointment->service->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium" style="color: #6b7280;">Current Status</label>
                    <span class="inline-block px-3 py-1 text-sm font-bold rounded-md
                        @if($appointment->status === 'scheduled') text-white
                        @elseif($appointment->status === 'completed') text-white
                        @elseif($appointment->status === 'pending') text-white
                        @else text-white
                        @endif"
                        style="background-color: 
                        @if($appointment->status === 'scheduled') #fbbf24
                        @elseif($appointment->status === 'completed') #0d6efd
                        @elseif($appointment->status === 'pending') #c77e23
                        @else #dc2626
                        @endif;">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" id="appointmentForm">
            @csrf
            @method('PUT')

            <input type="hidden" name="pet_id" value="{{ $appointment->pet_id }}">
            <input type="hidden" name="service_id" value="{{ $appointment->service_id }}">
            <input type="hidden" name="doctor_id" value="{{ $appointment->doctor_id }}">
            <input type="hidden" name="appointment_date" id="hiddenDate" value="{{ $appointment->appointment_date->format('Y-m-d') }}">
            <input type="hidden" name="appointment_time" id="hiddenTime" value="{{ $appointment->appointment_time }}">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: #1e3a5f;">Date</label>
                <input type="date" id="appointment_date" 
                    value="{{ $appointment->appointment_date->format('Y-m-d') }}" 
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none"
                    style="border-color: #495057;">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: #1e3a5f;">Time</label>
                <p class="text-sm mb-2" style="color: #6b7280;">Clinic hours: 8:00 AM - 6:00 PM</p>
                <div id="timeSlotContainer" class="mt-2">
                    <p class="text-sm italic" style="color: #6b7280;">Loading time slots...</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: #1e3a5f;">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none" style="border-color: #495057;">{{ $appointment->notes }}</textarea>
            </div>

            <div class="flex justify-between items-center gap-4">
                <div class="flex gap-4">
                    @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                        @if($appointment->status !== 'completed')
                        <button type="button" onclick="markAsCompleted()" class="px-4 py-2 text-white rounded transition" style="background-color: #0d6efd;">
                            <i class="fas fa-check-circle mr-2"></i>Mark as Completed
                        </button>
                        @endif
                        
                        @if($appointment->status !== 'cancelled')
                        <button type="button" onclick="cancelAppointment()" class="px-4 py-2 text-white rounded transition" style="background-color: #dc2626;">
                            <i class="fas fa-times-circle mr-2"></i>Cancel Appointment
                        </button>
                        @endif
                    @endif
                </div>

                <div class="flex gap-3">
                    @if(Auth::user()->isDoctor())
                        <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                            Back
                        </a>
                    @elseif(Auth::user()->isPetOwner())
                        <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                            Back
                        </a>
                    @else
                        <a href="{{ route('admin.appointments') }}" class="px-4 py-2 rounded" style="background-color: #495057; color: #ffffff;">
                            Back
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-2 text-white rounded" style="background-color: #1e3a5f;">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>

        <!-- Action Forms (hidden) -->
        <form id="completeForm" action="{{ route('appointments.mark-completed', $appointment->id) }}" method="POST" style="display: none;">
            @csrf
            @method('PUT')
        </form>

        <form id="cancelForm" action="{{ route('appointments.mark-cancelled', $appointment->id) }}" method="POST" style="display: none;">
            @csrf
            @method('PUT')
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const hiddenDate = document.getElementById('hiddenDate');
    const hiddenTime = document.getElementById('hiddenTime');
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    const originalTime = '{{ $appointment->appointment_time }}';
    const doctorId = {{ $appointment->doctor_id }};

    dateInput.addEventListener('change', function() {
        hiddenDate.value = this.value;
        loadTimeSlots(this.value, originalTime);
    });

    loadTimeSlots(dateInput.value, originalTime);

    function loadTimeSlots(selectedDate, currentTime) {
        if (!selectedDate) {
            timeSlotContainer.innerHTML = '<p class="text-sm italic" style="color: #6b7280;">Please select a date first</p>';
            return;
        }

        timeSlotContainer.innerHTML = '<p class="text-sm italic" style="color: #6b7280;"><i class="fas fa-spinner fa-spin mr-2"></i>Loading available time slots...</p>';

        fetch(`/appointments/available-slots?date=${selectedDate}&doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotContainer.innerHTML = `<p class="text-sm" style="color: #dc2626;">${data.error}</p>`;
                    return;
                }

                let html = '<div class="grid grid-cols-4 gap-2">';
                data.forEach(slot => {
                    const isOriginalTime = slot.time === currentTime;
                    const isCurrentlySelected = slot.time === hiddenTime.value;
                    const isAvailable = slot.available || isOriginalTime;
                    
                    if (isAvailable) {
                        let bgColor = '';
                        let textColor = '';
                        let borderColor = '';
                        let label = '';
                        
                        if (isOriginalTime && isCurrentlySelected) {
                            bgColor = '#c77e23';
                            textColor = '#ffffff';
                            borderColor = '#c77e23';
                            label = 'Current ⭐';
                        } else if (isCurrentlySelected) {
                            bgColor = '#0d6efd';
                            textColor = '#ffffff';
                            borderColor = '#0d6efd';
                            label = 'Selected';
                        } else if (isOriginalTime) {
                            bgColor = '#fef3c7';
                            textColor = '#c77e23';
                            borderColor = '#c77e23';
                            label = 'Current ⭐';
                        } else {
                            bgColor = '#f0f9ff';
                            textColor = '#0d6efd';
                            borderColor = '#0d6efd';
                            label = 'Available';
                        }
                        
                        html += `
                            <button type="button" 
                                class="time-slot-btn px-3 py-2 border-2 rounded-md transition text-sm font-medium"
                                style="background-color: ${bgColor}; color: ${textColor}; border-color: ${borderColor};"
                                data-time="${slot.time}"
                                data-is-original="${isOriginalTime}">
                                ${slot.display}
                                <span class="block text-xs" style="color: ${textColor}; font-weight: bold;">${label}</span>
                            </button>
                        `;
                    } else {
                        html += `
                            <button type="button" 
                                class="px-3 py-2 border-2 rounded-md cursor-not-allowed text-sm"
                                style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5;"
                                disabled>
                                ${slot.display}
                                <span class="block text-xs" style="color: #991b1b;">Booked</span>
                            </button>
                        `;
                    }
                });
                html += '</div>';
                
                timeSlotContainer.innerHTML = html;

                document.querySelectorAll('.time-slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const newTime = this.dataset.time;
                        const isOriginal = this.dataset.isOriginal === 'true';
                        
                        hiddenTime.value = newTime;
                        
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            const bIsOriginal = b.dataset.isOriginal === 'true';
                            const bTime = b.dataset.time;
                            
                            let bgColor, textColor, borderColor, label;
                            
                            if (bTime === newTime) {
                                if (bIsOriginal) {
                                    bgColor = '#c77e23';
                                    textColor = '#ffffff';
                                    borderColor = '#c77e23';
                                    label = 'Current ⭐';
                                } else {
                                    bgColor = '#0d6efd';
                                    textColor = '#ffffff';
                                    borderColor = '#0d6efd';
                                    label = 'Selected';
                                }
                            } else if (bIsOriginal) {
                                bgColor = '#fef3c7';
                                textColor = '#c77e23';
                                borderColor = '#c77e23';
                                label = 'Current ⭐';
                            } else {
                                bgColor = '#f0f9ff';
                                textColor = '#0d6efd';
                                borderColor = '#0d6efd';
                                label = 'Available';
                            }
                            
                            b.style.backgroundColor = bgColor;
                            b.style.color = textColor;
                            b.style.borderColor = borderColor;
                            
                            const span = b.querySelector('span');
                            span.style.color = textColor;
                            span.textContent = label;
                        });
                    });
                    
                    btn.addEventListener('mouseenter', function() {
                        if (!this.disabled) {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                        }
                    });
                    
                    btn.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotContainer.innerHTML = '<p class="text-sm" style="color: #dc2626;">Error loading time slots. Please try again.</p>';
            });
    }
});

function markAsCompleted() {
    if (confirm('Are you sure you want to mark this appointment as completed? This action cannot be undone.')) {
        document.getElementById('completeForm').submit();
    }
}

function cancelAppointment() {
    if (confirm('Are you sure you want to cancel this appointment? The pet owner will be notified.')) {
        document.getElementById('cancelForm').submit();
    }
}
</script>

<style>
.time-slot-btn {
    transition: all 0.15s ease-in-out;
}

input[type="date"]:focus, 
textarea:focus {
    border-color: #1e3a5f !important;
    box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.2) !important;
}
</style>
@endsection