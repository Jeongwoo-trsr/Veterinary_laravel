@extends('layouts.app')
@section('title', 'Billing')
@section('content')

<div class="mb-6">
    <div class="flex justify-between items-center gap-4">
        <div class="flex gap-4 flex-1">
            <!-- Status Filter Dropdown -->
           <select id="statusFilter" style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-color: #FCD34D; color: #000; font-weight: 600; padding: 8px 40px 8px 16px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 14px; cursor: pointer;">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
                <option value="unpaid">Unpaid</option>
            </select>
            
            <!-- Search Input -->
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search pet, owner, service..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
                <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        
        <a href="{{ route('doctor.bills.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap font-medium text-sm transition">
            + Add Bill
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif

@if($bills->count())
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <table class="w-full divide-y divide-gray-200" id="billsTable">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <!-- <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">DATE & TIME</th> -->
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">PET</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">OWNER</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">TOTAL BILL</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">BALANCE</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STATUS</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($bills as $bill)
                <tr class="bill-row hover:bg-gray-50 transition" 
                    data-bill-id="{{ $bill->id }}" 
                    data-pet="{{ strtolower($bill->pet->name) }}" 
                    data-owner="{{ strtolower($bill->pet->owner->user->name) }}" 
                    data-status="{{ $bill->status }}">
                    <!-- <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="text-sm font-medium text-gray-900">{{ $bill->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $bill->created_at->format('Y-m-d H:i:s') }}</div>
                    </td> -->
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $bill->pet->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $bill->pet->owner->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-red-600">₱{{ number_format($bill->balance, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($bill->status == 'paid')
                            <span class="text-green-600 font-medium">Paid</span>
                        @elseif($bill->status == 'partial')
                            <span class="text-yellow-600 font-medium">Partial</span>
                        @else
                            <span class="text-red-600 font-medium">Unpaid</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-4">
                            <button onclick="viewBill({{ $bill->id }}); return false;" class="text-blue-600 hover:text-blue-900 transition" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editBill({{ $bill->id }}); return false;" class="text-orange-500 hover:text-orange-700 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="noResults" class="text-center text-gray-500 py-8" style="display: none;">
            No bills match your search criteria.
        </div>
    </div>

    <div class="mt-4">
        {{ $bills->links() }}
    </div>
@else
    <div class="bg-white border border-gray-200 rounded p-8 text-center">
        <p class="text-gray-600 text-sm mb-4">No bills created yet</p>
        <a href="{{ route('doctor.bills.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block text-sm font-medium">
            Create First Bill
        </a>
    </div>
@endif

<!-- Bill Details Modal -->
<div id="billModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 900px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); rounded-lg; overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 5px; border-bottom: 1px solid #e5e7eb; background-color: #1e3a8a; border-top-left-radius: 8px; border-top-right-radius: 8px  ">
            <h3 class="px-3 py-1 text-xl font-bold text-white">Billing Details</h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer;">
                ×
            </button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; padding-bottom: 48px; overflow-y: auto; flex: 1; ">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Pet Name</p>
                    <p class="font-semibold text-lg text-gray-900" id="billPetName"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Owner Name</p>
                    <p class="font-semibold text-lg text-gray-900" id="billOwnerName"></p>
                </div>
            </div>

            <!-- View Mode -->
            <div id="viewMode">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-semibold text-gray-900">Billing Items</h4>
                        <button onclick="enableEditMode()" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-medium transition">
                            Edit Items
                        </button>
                    </div>
                    <div id="billItemsList" class="bg-white border-t-2 border-b-2 border-dashed border-gray-400 py-3 space-y-1"></div>
                </div>
----------------------------------------------------------------------------------------------------------------------------------
                <div class="border-t-2 border-dashed border-gray-400 pt-4 mb-6">
                    <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-300">
                        <span class="text-sm font-bold text-gray-900 uppercase">Total Amount</span>
                        <span class="text-lg font-bold text-gray-900" id="billSubtotal"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-dashed border-gray-300">
                        <span class="text-sm font-bold text-gray-900 uppercase">Paid Amount</span>
                        <span class="text-lg font-bold text-green-600" id="billPaidAmount"></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm font-bold text-gray-900 uppercase">Balance</span>
                        <span class="text-lg font-bold text-red-600" id="billBalance"></span>
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Current Status</p>
                    <span id="billStatus" class="px-3 py-1 rounded inline-block text-xs font-medium"></span>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Notes</p>
                    <p id="billNotes" class="text-gray-700 text-sm"></p>
                </div>

                <!-- Update Payment Form -->
                <div class="border-t pt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Update Payment</h4>
                    <form id="updatePaymentForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Current Balance</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-red-600 font-bold text-sm">
                                <span id="displayBalance">₱0.00</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Payment Amount</label>
                            <input type="number" id="paymentAmount" step="0.01" min="0" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">New Balance</label>
                            <div class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-red-600 font-bold text-sm">
                                <span id="calculatedBalance">₱0.00</span>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
                            <select name="status" id="updateStatus" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                            <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition text-sm font-medium">
                                Close
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm font-medium">
                                Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Mode -->
            <div id="editMode" style="display: none;">
                <form id="editItemsForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h4 class="font-semibold text-gray-900 mb-4">Edit Billing Items</h4>
                    <div id="editBillItems" class="mb-4"></div>
                    <button type="button" onclick="addEditItem()" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium transition mb-4">
                        + Add Item
                    </button>
                    
                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button type="button" onclick="cancelEdit()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition text-sm font-medium">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm font-medium">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let currentBillData = null;
let editItemCount = 0;
let isModalOpen = false;

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const noResults = document.getElementById('noResults');
const billModal = document.getElementById('billModal');

function filterBills() {
    if (isModalOpen) return;
    
    const searchTerm = searchInput.value.toLowerCase().trim();
    const statusValue = statusFilter.value.trim().toLowerCase();
    
    const rows = document.querySelectorAll('.bill-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const petName = (row.getAttribute('data-pet') || '').trim();
        const ownerName = (row.getAttribute('data-owner') || '').trim();
        const rowStatus = (row.getAttribute('data-status') || '').trim().toLowerCase();
        
        const matchesSearch = searchTerm === '' || petName.includes(searchTerm) || ownerName.includes(searchTerm);
        const matchesStatus = statusValue === '' || rowStatus === statusValue;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = 'table-row';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}

searchInput.addEventListener('input', filterBills);
statusFilter.addEventListener('change', filterBills);

function viewBill(id) {
    isModalOpen = true;
    fetch('/doctor/bills/' + id)
        .then(response => response.json())
        .then(data => {
            currentBillData = data;
            document.getElementById('billPetName').textContent = data.pet.name;
            document.getElementById('billOwnerName').textContent = data.pet.owner.user.name;
            
            let itemsHTML = '';
            data.items.forEach(item => {
                itemsHTML += `
                    <div class="flex justify-between py-1.5 px-2">
                        <span class="text-gray-700 text-sm">${item.description}</span>
                        <span class="font-semibold text-gray-900 text-sm">₱  ${parseFloat(item.amount).toFixed(2)}</span>
                    </div>
                `;
            });
            document.getElementById('billItemsList').innerHTML = itemsHTML;

            document.getElementById('billSubtotal').textContent = '₱ ' + parseFloat(data.total_amount).toFixed(2);
            document.getElementById('billPaidAmount').textContent = '₱ ' + parseFloat(data.paid_amount).toFixed(2);
            document.getElementById('billBalance').textContent = '₱ ' + parseFloat(data.balance).toFixed(2);
            document.getElementById('displayBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
            const statusElement = document.getElementById('billStatus');
            if (data.status === 'paid') {
                statusElement.textContent = 'Paid';
                statusElement.className = 'px-3 py-1 rounded bg-green-100 text-green-800 inline-block text-xs font-medium';
            } else if (data.status === 'partial') {
                statusElement.textContent = 'Partial';
                statusElement.className = 'px-3 py-1 rounded bg-yellow-100 text-yellow-800 inline-block text-xs font-medium';
            } else {
                statusElement.textContent = 'Unpaid';
                statusElement.className = 'px-3 py-1 rounded bg-red-100 text-red-800 inline-block text-xs font-medium';
            }
            
            document.getElementById('billNotes').textContent = data.notes || 'No notes';
            
            const formAction = "{{ route('doctor.bills.update-status', ['bill' => ':billId']) }}".replace(':billId', data.id);
            document.getElementById('updatePaymentForm').action = formAction;
            document.getElementById('paymentAmount').value = '';
            document.getElementById('updateStatus').value = data.status;
            document.getElementById('calculatedBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
            document.getElementById('viewMode').style.display = 'block';
            document.getElementById('editMode').style.display = 'none';
            
            billModal.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function editBill(id) {
    viewBill(id);
}

function deleteBill(id) {
    alert('Delete functionality coming soon');
}

document.getElementById('paymentAmount').addEventListener('input', function() {
    if (currentBillData) {
        const currentBalance = parseFloat(currentBillData.balance);
        const paymentAmount = parseFloat(this.value) || 0;
        const newBalance = Math.max(0, currentBalance - paymentAmount);
        document.getElementById('calculatedBalance').textContent = '₱' + newBalance.toFixed(2);
    }
});

document.getElementById('updatePaymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
    
    if (paymentAmount <= 0) {
        alert('Please enter a valid payment amount');
        return;
    }

    const newPaidAmount = parseFloat(currentBillData.paid_amount) + paymentAmount;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || 
                      document.querySelector('input[name="_token"]')?.value;

    const formData = new FormData();
    formData.append('paid_amount', newPaidAmount.toFixed(2));
    formData.append('status', document.getElementById('updateStatus').value);
    formData.append('_method', 'PUT');
    formData.append('_token', csrfToken);

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment updated successfully');
            closeModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment');
    });
});

function enableEditMode() {
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
    
    let editHTML = '';
    editItemCount = 0;
    currentBillData.items.forEach(item => {
        editHTML += createEditItemHTML(editItemCount, item.description, item.amount);
        editItemCount++;
    });
    document.getElementById('editBillItems').innerHTML = editHTML;
    document.getElementById('editItemsForm').action = '/doctor/bills/' + currentBillData.id + '/update-items';
}

function createEditItemHTML(index, description = '', amount = '') {
    return `
        <div class="bill-item mb-3 p-3 border border-gray-300 rounded bg-gray-50">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <input type="text" name="items[${index}][description]" value="${description}" placeholder="Description" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
                <div class="flex gap-2">
                    <input type="number" name="items[${index}][amount]" value="${amount}" placeholder="Amount" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="button" onclick="removeEditItem(this)" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-bold">×</button>
                </div>
            </div>
        </div>
    `;
}

function addEditItem() {
    const container = document.getElementById('editBillItems');
    const newItem = document.createElement('div');
    newItem.innerHTML = createEditItemHTML(editItemCount);
    container.appendChild(newItem.firstElementChild);
    editItemCount++;
}

function removeEditItem(button) {
    button.closest('.bill-item').remove();
}

function cancelEdit() {
    document.getElementById('editMode').style.display = 'none';
    document.getElementById('viewMode').style.display = 'block';
}

function closeModal() {
    isModalOpen = false;
    billModal.style.display = 'none';
    filterBills();
}

billModal.addEventListener('click', function(e) {
    if (e.target === billModal) {
        closeModal();
    }
});

filterBills();
</script>

@endsection