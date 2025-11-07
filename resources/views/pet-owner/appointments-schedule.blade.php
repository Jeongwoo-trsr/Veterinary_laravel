@extends('layouts.app')

@section('title', 'Schedule Appointment')

@section('content')
<style>
    :root {
        --navy: #1e3a5f;
        --gold: #d4931d;
        --yellow: #fcd34d;
        --charcoal: #2d3748;
        --blue-primary: #003d82;
        --blue-bright: #0066cc;
    }

    /* Time Slot Styling */
    .time-slot-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 20px;
        background: #f0f4f8;
        border-radius: 12px;
        min-height: 200px;
        border: 2px solid var(--blue-bright);
    }
    
    @media (max-width: 768px) {
        .time-slot-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .time-slot-btn {
        position: relative;
        padding: 16px 12px;
        border: 2px solid var(--blue-bright);
        background: white;
        color: var(--charcoal);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-size: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.1);
    }
    
    .time-slot-btn:hover {
        background: #e3f2fd;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.25);
        border-color: var(--blue-primary);
    }
    
    .time-slot-btn.selected {
        background: var(--navy) !important;
        color: white !important;
        border-color: var(--navy) !important;
        box-shadow: 0 8px 20px rgba(30, 58, 95, 0.4);
        transform: scale(1.05);
    }
    
    .time-slot-btn.selected .slot-icon,
    .time-slot-btn.selected .slot-status {
        color: var(--yellow) !important;
    }
    
    .time-slot-disabled {
        padding: 16px 12px;
        border: 2px solid #cbd5e0;
        background: #f7fafc;
        color: #a0aec0;
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
        color: var(--blue-bright);
    }
    
    .slot-icon {
        font-size: 14px;
        color: var(--blue-bright);
        margin-bottom: 2px;
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #718096;
    }
    
    .empty-state i {
        font-size: 48px;
        color: var(--blue-bright);
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .loading-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: var(--blue-bright);
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
        background: #fef3c7;
        border: 2px solid var(--gold);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 4px 12px rgba(212, 147, 29, 0.15);
    }
    
    .doctor-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--navy);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        margin-right: 16px;
        box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3);
        border: 3px solid var(--yellow);
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
        color: var(--charcoal);
        margin-bottom: 8px;
    }
    
    .form-label i {
        color: var(--gold);
        margin-right: 8px;
    }
    
    .clinic-hours-badge {
        display: inline-block;
        background: var(--yellow);
        color: var(--charcoal);
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 2px solid var(--gold);
        box-shadow: 0 2px 6px rgba(212, 147, 29, 0.2);
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
        background: var(--blue-bright);
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 102, 204, 0.3);
        z-index: 1000;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 2px solid var(--navy);
    }
    
    .notification i {
        color: var(--yellow);
    }
    
    .notification.error {
        background: #dc2626;
        border-color: #7f1d1d;
        box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
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
        border-color: var(--blue-bright);
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
    }

    /* Card styling */
    .form-card {
        background: white;
        box-shadow: 0 10px 30px rgba(30, 58, 95, 0.15);
        border: 2px solid var(--blue-bright);
    }

    /* Header styling */
    .page-header {
        background: var(--navy);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .page-header h2 {
        color: white;
    }

    .page-header h2 i {
        color: var(--yellow);
    }

    .page-header p {
        color: rgba(255, 255, 255, 0.9);
    }

    /* Input styling */
    input[type="date"],
    select,
    textarea {
        border: 2px solid #cbd5e0;
        transition: all 0.2s;
    }

    input[type="date"]:hover,
    select:hover,
    textarea:hover {
        border-color: var(--blue-bright);
    }

    /* Button styling */
    .btn-back {
        background: #e2e8f0;
        color: var(--charcoal);
        border: 2px solid #a0aec0;
    }

    .btn-back:hover {
        background: #cbd5e0;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background: var(--blue-bright);
        border: 2px solid var(--navy);
    }

    .btn-primary:hover {
        background: var(--blue-primary);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 58, 95, 0.3);
    }

    .btn-primary i {
        color: var(--yellow);
    }

    /* Error messages */
    .error-box {
        background: #fee2e2;
        border-left: 4px solid #dc2626;
    }
</style>

<div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-lg p-8 form-card">
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-calendar-plus mr-2"></i>Schedule an Appointment
                </h2>
                <p class="text-sm mt-1">Fill in the details below to book your appointment</p>
            </div>
            <a href="{{ route('pet-owner.appointments') }}" class="px-4 py-2 btn-back rounded-lg transition-all shadow-md" style="text-decoration: none;">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="error-box text-red-700 px-4 py-3 rounded-lg mb-4 shadow-md">
            <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pet-owner.appointments.store') }}" method="POST" id="appointmentForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Select Pet -->
            <div class="mb-4">
                <label for="pet_id" class="form-label">
                    <i class="fas fa-paw"></i>Select Pet *
                </label>
                <select name="pet_id" id="pet_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg shadow-sm" required>
                    <option value="">-- Choose Your Pet --</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                            {{ $pet->name }} ({{ $pet->species }})
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
                <select name="service_id" id="service_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg shadow-sm" required>
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
                    <p style="font-weight: 700; color: var(--charcoal); font-size: 16px;">{{ $doctor->user->name }}</p>
                    @if($doctor->specialization)
                        <p style="font-size: 14px; color: #718096; font-weight: 500;">{{ $doctor->specialization }}</p>
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
                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg shadow-sm" required>
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
                    <p style="font-size: 13px; color: #a0aec0;">Available time slots will appear here</p>
                </div>
            </div>
            <input type="hidden" name="appointment_time" id="appointment_time" required>
            @error('appointment_time') <span class="text-red-500 text-sm"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="form-label">
                <i class="fas fa-sticky-note"></i>Additional Notes <span style="font-weight: 400; color: #718096;">(Optional)</span>
            </label>
            <textarea name="notes" id="notes" rows="4" 
                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg shadow-sm"
                placeholder="Any additional information (symptoms, special requests, etc.)">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-3" style="padding-top: 20px; border-top: 2px solid #e2e8f0;">
            <a href="{{ route('pet-owner.appointments') }}" class="px-6 py-2 btn-back rounded-lg font-medium shadow-md transition-all" style="text-decoration: none;">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="px-6 py-2 btn-primary text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all">
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
                    <p style="font-size: 13px; color: #a0aec0;">Available time slots will appear here</p>
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

        fetch(`{{ route('pet-owner.appointments.available-slots') }}?date=${selectedDate}`)
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