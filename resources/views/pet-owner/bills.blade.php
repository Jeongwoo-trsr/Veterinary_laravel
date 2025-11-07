@extends('layouts.app')

@section('title', 'My Bills')

@section('content')
<h1 class="text-2xl font-bold mb-6 text-[#1e3a5f]">My Bills</h1>

<div class="bg-white shadow-lg rounded-lg p-6 border-2 border-gray-200">
    @if($bills->count())
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border-2 border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-[#1e3a5f]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Pet Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Service</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($bills as $bill)
                    <tr class="hover:bg-blue-50 transition-all">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $bill->pet->name }}</td>
                        <td class="px-6 py-4 text-gray-700">
                            @if($bill->items->count() > 0)
                                {{ $bill->items->first()->description }}
                                @if($bill->items->count() > 1)
                                    <span class="text-gray-500 text-sm font-medium">+{{ $bill->items->count() - 1 }} more</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-[#1e3a5f]">₱{{ number_format($bill->total_amount, 2) }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900">₱{{ number_format($bill->balance, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($bill->status == 'paid')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-100 border-2 border-green-400">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-green-700 font-bold text-sm">Paid</span>
                                </span>
                            @elseif($bill->status == 'partial')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-yellow-100 border-2 border-[#fbbf24]">
                                    <span class="w-2 h-2 bg-[#f59e0b] rounded-full mr-2"></span>
                                    <span class="text-[#d97706] font-bold text-sm">Partial</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-100 border-2 border-red-400">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    <span class="text-red-700 font-bold text-sm">Unpaid</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="viewBill({{ $bill->id }})" class="px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $bills->links() }}</div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-file-invoice text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">No bills found.</p>
        </div>
    @endif
</div>

<!-- Bill Details Modal -->
<div id="billModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6);">
    <div style="background-color: white; margin: 5% auto; padding: 0; border: 3px solid #0066cc; border-radius: 12px; width: 90%; max-width: 600px; position: relative; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <!-- Header -->
        <div class="bg-[#1e3a5f] px-6 py-4 rounded-t-lg">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-file-invoice mr-2"></i>Bill Details
                </h3>
                <button onclick="closeModal()" class="text-white hover:text-gray-200 text-3xl font-light transition-colors">
                    &times;
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
            <div class="space-y-6">
                <!-- Pet and Service Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                        <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Pet Name</p>
                        <p class="font-bold text-[#1e3a5f] text-lg" id="billPetName"></p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                        <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Service</p>
                        <p class="font-bold text-[#1e3a5f] text-lg" id="billService"></p>
                    </div>
                </div>

                <!-- Doctor Info -->
                <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Doctor</p>
                    <p class="font-bold text-gray-900 flex items-center" id="billDoctor">
                        <i class="fas fa-user-md text-[#0066cc] mr-2"></i>
                    </p>
                </div>

                <!-- Date -->
                <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <p class="text-xs text-gray-600 font-semibold mb-1 uppercase">Date</p>
                    <p class="font-bold text-gray-900 flex items-center" id="billDate">
                        <i class="fas fa-calendar text-[#0066cc] mr-2"></i>
                    </p>
                </div>

                <!-- Items List -->
                <div class="border-2 border-gray-200 rounded-lg p-4 bg-gray-50">
                    <h4 class="font-bold text-[#1e3a5f] mb-3 flex items-center">
                        <i class="fas fa-list mr-2"></i>Itemizing
                    </h4>
                    <div id="billItemsList" class="space-y-2"></div>
                </div>

                <!-- Total and Balance -->
                <div class="bg-[#1e3a5f] rounded-lg p-4 text-white">
                    <div class="flex justify-between items-center mb-3 pb-3 border-b-2 border-blue-400">
                        <span class="font-bold text-lg">Total Amount</span>
                        <span id="billTotal" class="font-bold text-2xl"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Balance Due</span>
                        <span id="billBalance" class="font-bold text-2xl"></span>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="bg-yellow-50 border-l-4 border-[#fcd34d] p-4 rounded">
                    <p class="text-xs text-gray-600 font-semibold mb-2 uppercase flex items-center">
                        <i class="fas fa-sticky-note text-[#d4931d] mr-2"></i>Remarks
                    </p>
                    <p id="billRemarks" class="text-gray-700 font-medium"></p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t-2 border-gray-200 flex justify-end">
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-semibold shadow-md transition-all">
                <i class="fas fa-times mr-2"></i>Close
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
            
            const doctorElement = document.getElementById('billDoctor');
            doctorElement.innerHTML = '<i class="fas fa-user-md text-[#0066cc] mr-2"></i>' + data.doctor.user.name;
            
            const date = new Date(data.created_at);
            const dateElement = document.getElementById('billDate');
            dateElement.innerHTML = '<i class="fas fa-calendar text-[#0066cc] mr-2"></i>' + date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
            
            let itemsHTML = '';
            data.items.forEach(item => {
                itemsHTML += `
                    <div class="flex justify-between items-center py-2 px-3 bg-white rounded border-2 border-gray-200">
                        <span class="font-medium text-gray-700">${item.description}</span>
                        <span class="font-bold text-[#1e3a5f]">₱${parseFloat(item.amount).toFixed(2)}</span>
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

// Close with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection