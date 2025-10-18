@extends('layouts.app')

@section('title', 'Add Medical Record')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Medical Record</h1>

            <form method="POST" action="{{ route('medical-records.store') }}" class="space-y-6">
                @csrf

                <!-- Pet and Doctor Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="pet_id" class="block text-sm font-medium text-gray-700">Pet</label>
                        <select id="pet_id" name="pet_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('pet_id') border-red-500 @enderror">
                            <option value="">Select pet</option>
                            @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} ({{ $pet->owner->user->name }})
                            </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                        <select id="doctor_id" name="doctor_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('doctor_id') border-red-500 @enderror">
                            <option value="">Select doctor</option>
                            @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->name }} ({{ $doctor->specialization }})
                            </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Appointment Selection -->
                <div>
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700">Related Appointment (Optional)</label>
                    <select id="appointment_id" name="appointment_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('appointment_id') border-red-500 @enderror">
                        <option value="">Select appointment</option>
                        @foreach($appointments as $appointment)
                        <option value="{{ $appointment->id }}" {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                            {{ $appointment->pet->name }} - {{ $appointment->service->name }} ({{ $appointment->appointment_date->format('M d, Y') }})
                        </option>
                        @endforeach
                    </select>
                    @error('appointment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Diagnosis -->
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis" rows="4" required 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('diagnosis') border-red-500 @enderror" 
                              placeholder="Enter diagnosis details...">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Treatment -->
                <div>
                    <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
                    <textarea id="treatment" name="treatment" rows="4" required 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('treatment') border-red-500 @enderror" 
                              placeholder="Enter treatment details...">{{ old('treatment') }}</textarea>
                    @error('treatment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- General Prescription Notes -->
                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700">General Prescription Notes</label>
                    <textarea id="prescription" name="prescription" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('prescription') border-red-500 @enderror" 
                              placeholder="Enter general prescription notes...">{{ old('prescription') }}</textarea>
                    @error('prescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medications</label>
                    <div id="medications-container">
                        <div class="medication-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Medication Name</label>
                                <input type="text" name="medications[0][name]" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., Amoxicillin">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Dosage</label>
                                <input type="text" name="medications[0][dosage]" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., 250mg">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Frequency</label>
                                <input type="text" name="medications[0][frequency]" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., Twice daily">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Duration (days)</label>
                                <input type="number" name="medications[0][duration_days]" min="1" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="7">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Instructions</label>
                                <input type="text" name="medications[0][instructions]" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., With food">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-medication" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-plus mr-2"></i>
                        Add Medication
                    </button>
                </div>

                <!-- Follow-up Schedule -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-gray-700">Follow-up Date</label>
                        <input id="follow_up_date" name="follow_up_date" type="date" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('follow_up_date') border-red-500 @enderror" 
                               value="{{ old('follow_up_date') }}">
                        @error('follow_up_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="follow_up_notes" class="block text-sm font-medium text-gray-700">Follow-up Notes</label>
                        <input id="follow_up_notes" name="follow_up_notes" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Follow-up instructions..." value="{{ old('follow_up_notes') }}">
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('medical-records.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Create Medical Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let medicationIndex = 1;

document.getElementById('add-medication').addEventListener('click', function() {
    const container = document.getElementById('medications-container');
    const newRow = document.createElement('div');
    newRow.className = 'medication-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg';
    newRow.innerHTML = `
        <div>
            <label class="block text-xs font-medium text-gray-600">Medication Name</label>
            <input type="text" name="medications[${medicationIndex}][name]" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                   placeholder="e.g., Amoxicillin">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Dosage</label>
            <input type="text" name="medications[${medicationIndex}][dosage]" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                   placeholder="e.g., 250mg">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Frequency</label>
            <input type="text" name="medications[${medicationIndex}][frequency]" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                   placeholder="e.g., Twice daily">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Duration (days)</label>
            <input type="number" name="medications[${medicationIndex}][duration_days]" min="1" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                   placeholder="7">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Instructions</label>
            <input type="text" name="medications[${medicationIndex}][instructions]" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                   placeholder="e.g., With food">
        </div>
    `;
    container.appendChild(newRow);
    medicationIndex++;
});
</script>
@endsection
