@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<style>
    /* Time Slot Styling */
    .time-slot-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 20px;
        background: #f9fafb;
        border-radius: 12px;
        min-height: 200px;
    }
    
    @media (max-width: 768px) {
        .time-slot-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .time-slot-btn {
        position: relative;
        padding: 16px 12px;
        border: 2px solid #10b981;
        background: white;
        color: #1f2937;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        font-size: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    
    .time-slot-btn:hover {
        background: #ecfdf5;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-color: #059669;
    }
    
    .time-slot-btn.selected {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        transform: scale(1.02);
    }
    
    .time-slot-btn.selected .slot-icon,
    .time-slot-btn.selected .slot-status {
        color: white !important;
    }
    
    .time-slot-disabled {
        padding: 16px 12px;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        color: #9ca3af;
        border-radius: 12px;
        cursor: not-allowed;
        text-align: center;
        font-size: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        opacity: 0.6;
    }
    
    .slot-time {
        font-weight: 700;
        font-size: 16px;
    }
    
    .slot-status {
        font-size: 11px;
        font-weight: 600;
        color: #10b981;
    }
    
    .slot-icon {
        font-size: 14px;
        color: #10b981;
        margin-bottom: 2px;
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    
    .loading-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #2563eb;
    }
    
    .loading-state i {
        font-size: 48px;
        margin-bottom: 16px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Doctor Card */
    .doctor-card {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #93c5fd;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .doctor-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #2563eb;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        margin-right: 16px;
    }
    
    .doctor-info {
        display: flex;
        align-items: center;
    }
    
    /* Form Enhancements */
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .form-label i {
        color: #2563eb;
        margin-right: 8px;
    }
    
    .clinic-hours-badge {
        display: inline-block;
        background: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    
    /* Success Notification */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        z-index: 1000;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .notification.error {
        background: #ef4444;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    /* Input Focus Effects */
    select:focus,
    input:focus,
    textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
</style>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Schedule an Appointment</h2>
            <p class="text-sm text-gray-600 mt-1">Fill in the details below to book an appointment</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400" style="text-decoration: none;">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Select Pet -->
            <div class="mb-4">
                <label for="pet_id" class="form-label">
                    <i class="fas fa-paw"></i>Select Pet *
                </label>
                <select name="pet_id" id="pet_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Choose Pet --</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} ({{ $pet->species }}) - Owner: {{ $pet->owner->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('pet_id') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>

            <!-- Select Service -->
            <div class="mb-4">
                <label for="service_id" class="form-label">
                    <i class="fas fa-stethoscope"></i>Select Service *
                </label>
                <select name="service_id" id="service_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                    <option value="">-- Choose Service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
                @error('service_id') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Doctor Info -->
        <div class="doctor-card">
            <label class="form-label">
                <i class="fas fa-user-md"></i>Assigned Doctor
            </label>
            <div class="doctor-info">
                <div class="doctor-avatar">
                    {{ strtoupper(substr($doctor->user->name, 0, 1)) }}
                </div>
                <div>
                    <p style="font-weight: 600; color: #1f2937;">{{ $doctor->user->name }}</p>
                    @if($doctor->specialization)
                        <p style="font-size: 14px; color: #6b7280;">{{ $doctor->specialization }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Appointment Date -->
        <div class="mb-4">
            <label for="appointment_date" class="form-label">
                <i class="fas fa-calendar"></i>Appointment Date *
            </label>
            <input type="date" name="appointment_date" id="appointment_date"
                value="{{ old('appointment_date') }}"
                min="{{ date('Y-m-d') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            @error('appointment_date') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <!-- Appointment Time -->
        <div class="mb-4">
            <div class="section-header">
                <label class="form-label" style="margin-bottom: 0;">
                    <i class="fas fa-clock"></i>Appointment Time *
                </label>
                <span class="clinic-hours-badge">
                    <i class="fas fa-info-circle"></i> Clinic hours: 8:00 AM - 6:00 PM
                </span>
            </div>
            
            <div id="timeSlotContainer" class="time-slot-grid">
                <div class="empty-state">
                    <i class="fas fa-calendar-day"></i>
                    <p style="font-weight: 500; margin-bottom: 4px;">Please select a date first</p>
                    <p style="font-size: 13px; color: #9ca3af;">Available time slots will appear here</p>
                </div>
            </div>
            <input type="hidden" name="appointment_time" id="appointment_time" required>
            @error('appointment_time') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="form-label">
                <i class="fas fa-sticky-note"></i>Additional Notes <span style="font-weight: 400; color: #6b7280;">(Optional)</span>
            </label>
            <textarea name="notes" id="notes" rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Any additional information about the appointment">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3" style="padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('appointments.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400" style="text-decoration: none;">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    const timeInput = document.getElementById('appointment_time');

    function showNotification(message, isError = false) {
        const notification = document.createElement('div');
        notification.className = 'notification' + (isError ? ' error' : '');
        notification.innerHTML = `<i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i>${message}`;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        
        if (!selectedDate) {
            timeSlotContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-day"></i>
                    <p style="font-weight: 500; margin-bottom: 4px;">Please select a date first</p>
                    <p style="font-size: 13px; color: #9ca3af;">Available time slots will appear here</p>
                </div>
            `;
            return;
        }

        timeSlotContainer.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner"></i>
                <p style="font-weight: 500;">Loading available time slots...</p>
            </div>
        `;

        fetch(`/appointments/available-slots?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotContainer.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>${data.error}</p></div>`;
                    return;
                }

                let html = '';
                data.forEach(slot => {
                    if (slot.available) {
                        html += `
                            <button type="button" class="time-slot-btn" data-time="${slot.time}">
                                <i class="fas fa-clock slot-icon"></i>
                                <span class="slot-time">${slot.display}</span>
                                <span class="slot-status">Available</span>
                            </button>
                        `;
                    } else {
                        html += `
                            <div class="time-slot-disabled">
                                <i class="fas fa-ban" style="color: #ef4444; margin-bottom: 2px;"></i>
                                <span class="slot-time" style="text-decoration: line-through;">${slot.display}</span>
                                <span style="font-size: 11px; color: #ef4444; font-weight: 600;">Booked</span>
                            </div>
                        `;
                    }
                });
                
                timeSlotContainer.innerHTML = html;

                document.querySelectorAll('.time-slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        timeInput.value = this.dataset.time;
                        showNotification('Time slot selected: ' + this.querySelector('.slot-time').textContent);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotContainer.innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error loading time slots</p></div>`;
            });
    });

    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        if (!timeInput.value) {
            e.preventDefault();
            showNotification('Please select a time slot', true);
            timeSlotContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection