@extends('layouts.app')

@section('title', 'My Bills')

@section('content')
<h1 class="text-2xl font-bold mb-6">My Bills</h1>

<div class="bg-blue-100 shadow-lg rounded-lg p-6">
    @if($bills->count())
        <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Pet Name</th>
                    <th class="px-6 py-3 text-left">Service</th>
                    <th class="px-6 py-3 text-left">Total Amount</th>
                    <th class="px-6 py-3 text-left">Balance</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $bill->pet->name }}</td>
                    <td class="px-6 py-4">
                        @if($bill->items->count() > 0)
                            {{ $bill->items->first()->description }}
                            @if($bill->items->count() > 1)
                                <span class="text-gray-500 text-sm">+{{ $bill->items->count() - 1 }} more</span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-6 py-4">₱{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="px-6 py-4">₱{{ number_format($bill->balance, 2) }}</td>
                    <td class="px-6 py-4">
                        @if($bill->status == 'paid')
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                <span class="text-green-600 font-medium">Paid</span>
                            </span>
                        @elseif($bill->status == 'partial')
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                <span class="text-yellow-600 font-medium">Partial</span>
                            </span>
                        @else
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                <span class="text-red-600 font-medium">Unpaid</span>
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="viewBill({{ $bill->id }})" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $bills->links() }}</div>
    @else
        <p class="text-gray-500">No bills found.</p>
    @endif
</div>

<!-- Bill Details Modal -->
<div id="billModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 600px; position: relative;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Bill Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>
        

        <div class="mb-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Pet Name</p>
                    <p class="font-medium" id="billPetName"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Service</p>
                    <p class="font-medium" id="billService"></p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Doctor</p>
                <p class="font-medium" id="billDoctor"></p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Date</p>
                <p class="font-medium" id="billDate"></p>
            </div>

            <div class="border-t pt-4">
                <h4 class="font-semibold mb-2">Itemizing</h4>
                <div id="billItemsList" class="space-y-2"></div>
            </div>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Total</span>
                    <span id="billTotal" class="font-semibold"></span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Balance</span>
                    <span id="billBalance" class="font-semibold"></span>
                </div>
            </div>

            <div class="border-t pt-4 mt-2">
                <p class="text-sm text-gray-600 mb-1">Remarks</p>
                <p id="billRemarks" class="text-gray-700"></p>
            </div>
        </div>

        <div class="flex justify-end">
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function viewBill(id) {
    fetch('/pet-owner/bills/' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('billPetName').textContent = data.pet.name;
            
            if (data.items.length > 0) {
                document.getElementById('billService').textContent = data.items[0].description;
            }
            
            document.getElementById('billDoctor').textContent = data.doctor.user.name;
            
            const date = new Date(data.created_at);
            document.getElementById('billDate').textContent = date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            let itemsHTML = '';
            data.items.forEach(item => {
                itemsHTML += `
                    <div class="flex justify-between">
                        <span>${item.description}</span>
                        <span>₱${parseFloat(item.amount).toFixed(2)}</span>
                    </div>
                `;
            });
            document.getElementById('billItemsList').innerHTML = itemsHTML;
            
            document.getElementById('billTotal').textContent = '₱' + parseFloat(data.total_amount).toFixed(2);
            document.getElementById('billBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            document.getElementById('billRemarks').textContent = data.notes || 'To be settled on next visit';
            
            document.getElementById('billModal').style.display = 'block';
        });
}

function closeModal() {
    document.getElementById('billModal').style.display = 'none';
}

document.getElementById('billModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection