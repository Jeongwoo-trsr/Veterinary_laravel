@extends('layouts.app')

@section('title', 'Create Bill')

@section('content')
<h1 class="text-2xl font-bold mb-6">Create New Bill</h1>

<div class="bg-blue-100 shadow-lg rounded-lg p-6">
    <form action="{{ route('doctor.bills.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Pet</label>
            <select name="pet_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Select Pet --</option>
                @foreach($pets as $pet)
                    <option value="{{ $pet->id }}">{{ $pet->name }} (Owner: {{ $pet->owner->user->name }})</option>
                @endforeach
            </select>
            @error('pet_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Billing Items</label>
            <div id="billItems">
                <div class="bill-item mb-3 p-4 border border-gray-300 rounded bg-white">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="items[0][description]" placeholder="Description (e.g., Consultation)" required class="w-full px-3 py-2 border border-gray-300 rounded">
                        </div>
                        <div>
                            <input type="number" name="items[0][amount]" placeholder="Amount" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" onclick="addItem()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                + Add Item
            </button>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('doctor.bills') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Create Bill
            </button>
        </div>
    </form>
</div>

<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('billItems');
    const newItem = document.createElement('div');
    newItem.className = 'bill-item mb-3 p-4 border border-gray-300 rounded bg-white';
    newItem.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <input type="text" name="items[${itemCount}][description]" placeholder="Description" required class="w-full px-3 py-2 border border-gray-300 rounded">
            </div>
            <div class="flex gap-2">
                <input type="number" name="items[${itemCount}][amount]" placeholder="Amount" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded">
                <button type="button" onclick="removeItem(this)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">×</button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemCount++;
}

function removeItem(button) {
    button.closest('.bill-item').remove();
}
</script>
@endsection