@extends('layouts.app')

@section('title', 'Edit Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Appointment</h1>
        @if(Auth::user()->isDoctor())
            <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @elseif(Auth::user()->isPetOwner())
            <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @else
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        @endif
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Appointment Information (Read-only) -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-3">Appointment Details</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Pet Name</label>
                    <p class="text-gray-900">{{ $appointment->pet->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Owner</label>
                    <p class="text-gray-900">{{ $appointment->pet->owner->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Doctor</label>
                    <p class="text-gray-900">{{ $appointment->doctor->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Service</label>
                    <p class="text-gray-900">{{ $appointment->service->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Current Status</label>
                    <span class="inline-block px-3 py-1 text-sm font-bold rounded-md
                        @if($appointment->status === 'scheduled') bg-yellow-200 text-yellow-800
                        @elseif($appointment->status === 'completed') bg-green-200 text-green-800
                        @elseif($appointment->status === 'pending') bg-purple-200 text-purple-800
                        @else bg-red-200 text-red-800
                        @endif">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" id="appointment_date" 
                    value="{{ $appointment->appointment_date->format('Y-m-d') }}" 
                    min="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                <p class="text-sm text-gray-600 mb-2">Clinic hours: 8:00 AM - 6:00 PM</p>
                <div id="timeSlotContainer" class="mt-2">
                    <p class="text-gray-500 text-sm italic">Loading time slots...</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $appointment->notes }}</textarea>
            </div>

            <div class="flex justify-between items-center gap-4">
                <div class="flex gap-4">
                    @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                        @if($appointment->status !== 'completed')
                        <button type="button" onclick="markAsCompleted()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Mark as Completed
                        </button>
                        @endif
                        
                        @if($appointment->status !== 'cancelled')
                        <button type="button" onclick="cancelAppointment()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                            <i class="fas fa-times-circle mr-2"></i>Cancel Appointment
                        </button>
                        @endif
                    @endif
                </div>

                <div class="flex gap-3">
                    @if(Auth::user()->isDoctor())
                        <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Back
                        </a>
                    @elseif(Auth::user()->isPetOwner())
                        <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Back
                        </a>
                    @else
                        <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Back
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
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
            timeSlotContainer.innerHTML = '<p class="text-gray-500 text-sm italic">Please select a date first</p>';
            return;
        }

        timeSlotContainer.innerHTML = '<p class="text-gray-500 text-sm italic"><i class="fas fa-spinner fa-spin mr-2"></i>Loading available time slots...</p>';

        fetch(`/appointments/available-slots?date=${selectedDate}&doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotContainer.innerHTML = `<p class="text-red-500 text-sm">${data.error}</p>`;
                    return;
                }

                let html = '<div class="grid grid-cols-4 gap-2">';
                data.forEach(slot => {
                    const isOriginalTime = slot.time === currentTime;
                    const isCurrentlySelected = slot.time === hiddenTime.value;
                    const isAvailable = slot.available || isOriginalTime;
                    
                    if (isAvailable) {
                        let buttonClass = '';
                        let textClass = '';
                        let label = '';
                        
                        if (isOriginalTime && isCurrentlySelected) {
                            buttonClass = 'bg-purple-600 text-white border-purple-600';
                            textClass = 'text-white font-bold';
                            label = 'Current ⭐';
                        } else if (isCurrentlySelected) {
                            buttonClass = 'bg-blue-600 text-white border-blue-600';
                            textClass = 'text-white font-bold';
                            label = 'Selected';
                        } else if (isOriginalTime) {
                            buttonClass = 'bg-purple-100 border-purple-500 text-purple-700';
                            textClass = 'text-purple-600 font-bold';
                            label = 'Current ⭐';
                        } else {
                            buttonClass = 'border-green-500 bg-green-50 text-green-700';
                            textClass = 'text-green-600';
                            label = 'Available';
                        }
                        
                        html += `
                            <button type="button" 
                                class="time-slot-btn px-3 py-2 border-2 ${buttonClass} rounded-md hover:opacity-90 transition text-sm font-medium"
                                data-time="${slot.time}"
                                data-is-original="${isOriginalTime}">
                                ${slot.display}
                                <span class="block text-xs ${textClass}">${label}</span>
                            </button>
                        `;
                    } else {
                        html += `
                            <button type="button" 
                                class="px-3 py-2 border-2 border-red-300 bg-red-50 text-red-400 rounded-md cursor-not-allowed text-sm"
                                disabled>
                                ${slot.display}
                                <span class="block text-xs text-red-400">Booked</span>
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
                            
                            b.className = 'time-slot-btn px-3 py-2 border-2 rounded-md hover:opacity-90 transition text-sm font-medium';
                            const span = b.querySelector('span');
                            span.className = 'block text-xs';
                            
                            if (bTime === newTime) {
                                if (bIsOriginal) {
                                    b.classList.add('bg-purple-600', 'text-white', 'border-purple-600');
                                    span.classList.add('text-white', 'font-bold');
                                    span.textContent = 'Current ⭐';
                                } else {
                                    b.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                                    span.classList.add('text-white', 'font-bold');
                                    span.textContent = 'Selected';
                                }
                            } else if (bIsOriginal) {
                                b.classList.add('bg-purple-100', 'border-purple-500', 'text-purple-700');
                                span.classList.add('text-purple-600', 'font-bold');
                                span.textContent = 'Current ⭐';
                            } else {
                                b.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
                                span.classList.add('text-green-600');
                                span.textContent = 'Available';
                            }
                        });
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotContainer.innerHTML = '<p class="text-red-500 text-sm">Error loading time slots. Please try again.</p>';
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
.time-slot-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection
