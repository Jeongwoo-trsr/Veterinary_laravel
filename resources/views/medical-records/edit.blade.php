@extends('layouts.app')

@section('title', 'Edit Medical Record')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Edit Medical Record</h1>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.medical-records') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                @elseif(Auth::user()->isDoctor())
                    <a href="{{ route('doctor.medical-records') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                @else
                    <a href="{{ route('pet-owner.medical-records') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                @endif
            </div>

            <form method="POST" action="{{ route('medical-records.update', $medicalRecord->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Pet and Doctor Selection -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="pet_id" class="block text-sm font-medium text-gray-700">Pet *</label>
                        <select id="pet_id" name="pet_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('pet_id') border-red-500 @enderror">
                            <option value="">Select pet</option>
                            @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ $medicalRecord->pet_id == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} ({{ $pet->owner->user->name }})
                            </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Doctor Info (Read-only display) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attending Doctor</label>
                        <div class="mt-1 px-3 py-2 border border-gray-200 rounded-md bg-gray-50">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user-md text-blue-600"></i>
                                <span class="font-medium text-gray-900">{{ $doctor->user->name }}</span>
                                @if($doctor->specialization)
                                    <span class="text-sm text-gray-500">({{ $doctor->specialization }})</span>
                                @endif
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Doctor is automatically assigned</p>
                    </div>
                </div>

                <!-- Appointment Selection -->
                <div>
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700">Related Appointment (Optional)</label>
                    <select id="appointment_id" name="appointment_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('appointment_id') border-red-500 @enderror">
                        <option value="">Select appointment</option>
                        @foreach($appointments as $appointment)
                        <option value="{{ $appointment->id }}" {{ $medicalRecord->appointment_id == $appointment->id ? 'selected' : '' }}>
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
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis *</label>
                    <textarea id="diagnosis" name="diagnosis" rows="4" required 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('diagnosis') border-red-500 @enderror" 
                              placeholder="Enter diagnosis details...">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Treatment -->
                <div>
                    <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment *</label>
                    <textarea id="treatment" name="treatment" rows="4" required 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('treatment') border-red-500 @enderror" 
                              placeholder="Enter treatment details...">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
                    @error('treatment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- General Prescription Notes -->
                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700">General Prescription Notes</label>
                    <textarea id="prescription" name="prescription" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('prescription') border-red-500 @enderror" 
                              placeholder="Enter general prescription notes...">{{ old('prescription', $medicalRecord->prescription) }}</textarea>
                    @error('prescription')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medications</label>
                    <div id="medications-container">
                        @forelse($medicalRecord->prescriptions as $index => $prescription)
                        <div class="medication-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Medication Name</label>
                                <input type="text" name="medications[{{ $index }}][name]" value="{{ old("medications.$index.name", $prescription->medication_name) }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., Amoxicillin">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Dosage</label>
                                <input type="text" name="medications[{{ $index }}][dosage]" value="{{ old("medications.$index.dosage", $prescription->dosage) }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., 250mg">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Frequency</label>
                                <input type="text" name="medications[{{ $index }}][frequency]" value="{{ old("medications.$index.frequency", $prescription->frequency) }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., Twice daily">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Duration (days)</label>
                                <input type="number" name="medications[{{ $index }}][duration_days]" value="{{ old("medications.$index.duration_days", $prescription->duration_days) }}" min="1" 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="7">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600">Instructions</label>
                                <input type="text" name="medications[{{ $index }}][instructions]" value="{{ old("medications.$index.instructions", $prescription->instructions) }}"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                       placeholder="e.g., With food">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="this.closest('.medication-row').remove()" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="medication-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
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
                        @endforelse
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
                               value="{{ old('follow_up_date', $medicalRecord->follow_up_date ? $medicalRecord->follow_up_date : '') }}">
                        @error('follow_up_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="follow_up_notes" class="block text-sm font-medium text-gray-700">Follow-up Notes</label>
                        <input id="follow_up_notes" name="follow_up_notes" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Follow-up instructions..." 
                               value="{{ old('follow_up_notes', $medicalRecord->followUpSchedules->first()->notes ?? '') }}">
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.medical-records') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    @elseif(Auth::user()->isDoctor())
                        <a href="{{ route('doctor.medical-records') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('pet-owner.medical-records') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    @endif
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Update Medical Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let medicationIndex = {{ $medicalRecord->prescriptions->count() }};

document.getElementById('add-medication').addEventListener('click', function() {
    const container = document.getElementById('medications-container');
    const newRow = document.createElement('div');
    newRow.className = 'medication-row grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50';
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
        <div class="flex items-end">
            <button type="button" onclick="this.closest('.medication-row').remove()" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    medicationIndex++;
});
</script>
@endsection