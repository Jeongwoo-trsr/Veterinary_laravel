@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#1e3a5f]">Create New Bill</h1>
        <a href="{{ route('doctor.bills') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-[#2c3e50] px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-invoice text-yellow-400"></i>
                Billing Information
            </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                    <p class="font-semibold mb-2"><i class="fas fa-exclamation-circle mr-2"></i>Please fix the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('doctor.bills.store') }}" method="POST">
                @csrf

                <!-- Select Pet -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-paw text-blue-600 mr-2"></i>Select Pet *
                    </label>
                    <select name="pet_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Select Pet --</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}">{{ $pet->name }} (Owner: {{ $pet->owner->user->name }})</option>
                        @endforeach
                    </select>
                    @error('pet_id')
                        <p class="text-red-500 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Billing Items -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-list text-blue-600 mr-2"></i>Billing Items *
                        </label>
                        <button type="button" onclick="addItem()" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i>Add Item
                        </button>
                    </div>

                    <div id="billItems" class="space-y-3">
                        <div class="bill-item p-4 border-2 border-gray-300 rounded-lg bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                                    <input type="text" name="items[0][description]" placeholder="e.g., Consultation, Vaccine, etc." required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₱)</label>
                                    <input type="number" name="items[0][amount]" placeholder="0.00" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Notes (Optional)
                    </label>
                    <textarea name="notes" rows="3" placeholder="Add any additional notes or remarks..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('doctor.bills') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md">
                        <i class="fas fa-save mr-2"></i>Create Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('billItems');
    const newItem = document.createElement('div');
    newItem.className = 'bill-item p-4 border-2 border-gray-300 rounded-lg bg-gray-50';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <input type="text" name="items[${itemCount}][description]" placeholder="e.g., Consultation, Vaccine, etc." required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Amount (₱)</label>
                    <input type="number" name="items[${itemCount}][amount]" placeholder="0.00" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeItem(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-bold text-lg h-[42px]" title="Remove Item">
                        ×
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemCount++;
}

function removeItem(button) {
    if (document.querySelectorAll('.bill-item').length > 1) {
        button.closest('.bill-item').remove();
    } else {
        alert('You must have at least one billing item.');
    }
}
</script>
@endsection