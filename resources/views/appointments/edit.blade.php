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

        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" id="appointmentForm">
            @csrf
            @method('PUT')

            <!-- Hidden fields to pass required IDs -->
            <input type="hidden" name="pet_id" value="{{ $appointment->pet_id }}">
            <input type="hidden" name="service_id" value="{{ $appointment->service_id }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pet Name</label>
                <input type="text" value="{{ $appointment->pet->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Owner</label>
                <input type="text" value="{{ $appointment->pet->owner->user->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Doctor</label>
                <input type="text" value="{{ $appointment->doctor->user->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <input type="text" value="{{ $appointment->service->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="appointment_date" id="appointment_date" 
                    value="{{ $appointment->appointment_date->format('Y-m-d') }}" 
                    min="{{ date('Y-m-d') }}"
                    required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                <p class="text-sm text-gray-600 mb-2">Clinic hours: 8:00 AM - 6:00 PM</p>
                <div id="timeSlotContainer" class="mt-2">
                    <p class="text-gray-500 text-sm italic">Loading time slots...</p>
                </div>
                <input type="hidden" name="appointment_time" id="appointment_time" 
                    value="{{ $appointment->appointment_time }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $appointment->notes }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="scheduled" {{ $appointment->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                @if(Auth::user()->isDoctor())
                    <a href="{{ route('doctor.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                @elseif(Auth::user()->isPetOwner())
                    <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                @else
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                @endif
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    const timeInput = document.getElementById('appointment_time');
    const currentTime = timeInput.value;

    // Load time slots for the current date
    loadTimeSlots(dateInput.value, currentTime);

    dateInput.addEventListener('change', function() {
        loadTimeSlots(this.value, null);
    });

    function loadTimeSlots(selectedDate, selectedTime) {
        if (!selectedDate) {
            timeSlotContainer.innerHTML = '<p class="text-gray-500 text-sm italic">Please select a date first</p>';
            return;
        }

        timeSlotContainer.innerHTML = '<p class="text-gray-500 text-sm italic"><i class="fas fa-spinner fa-spin mr-2"></i>Loading available time slots...</p>';

        fetch(`/appointments/available-slots?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotContainer.innerHTML = `<p class="text-red-500 text-sm">${data.error}</p>`;
                    return;
                }

                let html = '<div class="grid grid-cols-4 gap-2">';
                data.forEach(slot => {
                    // Current appointment's time slot should be available even if booked
                    const isCurrentSlot = slot.time === selectedTime;
                    const isAvailable = slot.available || isCurrentSlot;
                    
                    if (isAvailable) {
                        const isSelected = slot.time === selectedTime;
                        html += `
                            <button type="button" 
                                class="time-slot-btn px-3 py-2 border-2 ${isSelected ? 'bg-blue-600 text-white border-blue-600' : 'border-green-500 bg-green-50 text-green-700'} rounded-md hover:bg-green-100 transition text-sm font-medium"
                                data-time="${slot.time}">
                                ${slot.display}
                                <span class="block text-xs ${isSelected ? 'text-white' : 'text-green-600'}">${isCurrentSlot ? 'Current' : 'Available'}</span>
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
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                            b.classList.add('bg-green-50', 'text-green-700', 'border-green-500');
                            b.querySelector('span').classList.remove('text-white');
                            b.querySelector('span').classList.add('text-green-600');
                        });
                        
                        this.classList.remove('bg-green-50', 'text-green-700', 'border-green-500');
                        this.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                        this.querySelector('span').classList.remove('text-green-600');
                        this.querySelector('span').classList.add('text-white');
                        
                        timeInput.value = this.dataset.time;
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotContainer.innerHTML = '<p class="text-red-500 text-sm">Error loading time slots. Please try again.</p>';
            });
    }

    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        if (!timeInput.value) {
            e.preventDefault();
            alert('Please select a time slot');
        }
    });
});
</script>

<style>
.time-slot-btn:hover:not(.bg-blue-600) {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection