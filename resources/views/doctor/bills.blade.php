@extends('layouts.app')
@section('title', 'Billing')
@section('content')

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold" style="color: #1E3A8A;">Billing</h1>
    <p class="text-gray-600 mt-2">Manage all billing records and payments</p>
</div>

<div class="mb-6">
    <div class="flex justify-between items-center gap-4">
        <div class="flex gap-4 flex-1">
            <!-- Status Filter Dropdown -->
            <select id="statusFilter" style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-color: #FDB913; color: #000; font-weight: 600; padding: 8px 40px 8px 16px; border: 1px solid #FDB913; border-radius: 6px; font-size: 14px; cursor: pointer;">
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
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 text-sm"
                    style="border-color: #D1D5DB; background-color: white;"
                >
                <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        
        <a href="{{ route('doctor.bills.create') }}" class="px-4 py-2 text-white rounded whitespace-nowrap font-medium text-sm transition hover:opacity-90" style="background-color: #1E3A8A;">
            + Add Bill
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded" style="background-color: #D1FAE5; border: 1px solid #10B981; color: #065F46;">
        {{ session('success') }}
    </div>
@endif

@if($bills->count())
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="w-full" id="billsTable">
            <thead style="background-color: #1E3A8A;">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Pet Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Service</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Total Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Balance</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @foreach($bills as $bill)
                <tr class="bill-row border-b hover:bg-gray-50 transition" 
                    data-bill-id="{{ $bill->id }}" 
                    data-pet="{{ strtolower($bill->pet->name) }}" 
                    data-owner="{{ strtolower($bill->pet->owner->user->name) }}" 
                    data-status="{{ $bill->status }}"
                    style="border-color: #E5E7EB;">
                    <td class="px-6 py-4 text-sm font-medium" style="color: #111827;">{{ $bill->pet->name }}</td>
                    <td class="px-6 py-4 text-sm" style="color: #6B7280;">
                        @if($bill->items->count() > 0)
                            {{ $bill->items->first()->description }}
                            @if($bill->items->count() > 1)
                                <span style="color: #1E3A8A;">+{{ $bill->items->count() - 1 }} more</span>
                            @endif
                        @else
                            No items
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold" style="color: #111827;">₱{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold" style="color: #111827;">₱{{ number_format($bill->balance, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($bill->status == 'paid')
                            <span class="px-3 py-1 rounded-full text-xs font-medium inline-flex items-center" style="background-color: #D1FAE5; color: #065F46;">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #059669;"></span>
                                Paid
                            </span>
                        @elseif($bill->status == 'partial')
                            <span class="px-3 py-1 rounded-full text-xs font-medium inline-flex items-center" style="background-color: #FEF3C7; color: #92400E;">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #F59E0B;"></span>
                                Partial
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-medium inline-flex items-center" style="background-color: #FEE2E2; color: #991B1B;">
                                <span class="w-2 h-2 rounded-full mr-2" style="background-color: #DC2626;"></span>
                                Unpaid
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="viewBill({{ $bill->id }}); return false;" class="px-4 py-2 text-white rounded text-sm font-medium transition hover:opacity-90" style="background-color: #1E3A8A;">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
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
    <div class="bg-white border rounded p-8 text-center" style="border-color: #E5E7EB;">
        <p class="text-gray-600 text-sm mb-4">No bills created yet</p>
        <a href="{{ route('doctor.bills.create') }}" class="px-4 py-2 text-white rounded inline-block text-sm font-medium hover:opacity-90" style="background-color: #1E3A8A;">
            Create First Bill
        </a>
    </div>
@endif

<!-- Bill Details Modal -->
<div id="billModal" style="display: none; position: fixed; z-index: 50; left: 0; top: 0; width: 100%; height: 100%; overflow: hidden; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 3% auto; border-radius: 8px; width: 95%; max-width: 700px; position: relative; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Modal Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background-color: #1E3A8A;">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-invoice text-white"></i>
                <h3 class="text-xl font-bold text-white">Bill Details</h3>
            </div>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl" style="background: none; border: none; cursor: pointer; padding: 0; line-height: 1;">
                ×
            </button>
        </div>

        <!-- Modal Content -->
        <div style="padding: 24px; overflow-y: auto; flex: 1;">
            <!-- Pet, Service, and Date Info -->
            <div class="grid grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded" style="border: 1px solid #E5E7EB;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #6B7280;">Pet Name</p>
                    <p class="font-bold text-lg" style="color: #1E3A8A;" id="billPetName"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded" style="border: 1px solid #E5E7EB;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #6B7280;">Service</p>
                    <p class="font-semibold" style="color: #111827;" id="billService"></p>
                </div>
                <div class="bg-gray-50 p-4 rounded" style="border: 1px solid #E5E7EB;">
                    <p class="text-xs uppercase font-semibold mb-1" style="color: #6B7280;">Date</p>
                    <p class="font-semibold flex items-center" style="color: #111827;">
                        <i class="fas fa-calendar mr-2" style="color: #1E3A8A;"></i>
                        <span id="billDate"></span>
                    </p>
                </div>
            </div>

            <!-- View Mode -->
            <div id="viewMode">
                <!-- Itemizing Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-bold flex items-center" style="color: #111827;">
                            <i class="fas fa-list mr-2" style="color: #1E3A8A;"></i>
                            Itemizing
                        </h4>
                        <button onclick="enableEditMode()" class="px-3 py-1.5 text-white rounded text-xs font-medium transition hover:opacity-90" style="background-color: #1E3A8A;">
                            <i class="fas fa-edit mr-1"></i> Edit Items
                        </button>
                    </div>
                    <div class="bg-white rounded" style="border: 1px solid #E5E7EB;">
                        <div id="billItemsList" class="divide-y" style="border-color: #E5E7EB;"></div>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="bg-gray-50 p-4 rounded mb-6" style="border: 1px solid #E5E7EB;">
                    <div class="flex justify-between items-center py-2 border-b" style="border-color: #E5E7EB;">
                        <span class="text-sm font-bold" style="color: #111827;">Total Amount</span>
                        <span class="text-xl font-bold" style="color: #1E3A8A;" id="billSubtotal"></span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm font-bold" style="color: #111827;">Balance Due</span>
                        <span class="text-xl font-bold" style="color: #1E3A8A;" id="billBalance"></span>
                    </div>
                </div>

                <!-- Update Payment Form -->
                <div style="border-top: 2px solid #E5E7EB; padding-top: 24px;">
                    <h4 class="font-bold mb-4 flex items-center" style="color: #111827;">
                        <i class="fas fa-money-bill-wave mr-2" style="color: #1E3A8A;"></i>
                        Update Payment
                    </h4>
                    <form id="updatePaymentForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #6B7280;">Current Balance</label>
                            <div class="w-full px-4 py-3 border rounded font-bold" style="border-color: #1E3A8A; background-color: #EFF6FF; color: #1E3A8A;">
                                <span id="displayBalance">₱0.00</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #6B7280;">Payment Amount</label>
                            <input type="number" id="paymentAmount" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 font-semibold" style="border-color: #D1D5DB; focus:ring-color: #1E3A8A;">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #6B7280;">New Balance</label>
                            <div class="w-full px-4 py-3 border rounded font-bold" style="border-color: #1E3A8A; background-color: #EFF6FF; color: #1E3A8A;">
                                <span id="calculatedBalance">₱0.00</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold mb-2 uppercase" style="color: #6B7280;">Status</label>
                            <select name="status" id="updateStatus" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 font-semibold" style="border-color: #D1D5DB;">
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" onclick="closeModal()" class="px-6 py-2.5 rounded transition text-sm font-medium" style="background-color: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
                                Close
                            </button>
                            <button type="submit" class="px-6 py-2.5 text-white rounded transition text-sm font-medium hover:opacity-90" style="background-color: #1E3A8A;">
                                <i class="fas fa-check mr-1"></i> Update Payment
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
                    
                    <h4 class="font-bold mb-4 flex items-center" style="color: #111827;">
                        <i class="fas fa-edit mr-2" style="color: #1E3A8A;"></i>
                        Edit Billing Items
                    </h4>
                    <div id="editBillItems" class="mb-4 space-y-3"></div>
                    <button type="button" onclick="addEditItem()" class="px-4 py-2 text-white rounded text-sm font-medium transition mb-4 hover:opacity-90" style="background-color: #059669;">
                        <i class="fas fa-plus mr-1"></i> Add Item
                    </button>
                    
                    <div class="flex justify-end gap-3 pt-4" style="border-top: 1px solid #E5E7EB;">
                        <button type="button" onclick="cancelEdit()" class="px-6 py-2.5 rounded transition text-sm font-medium" style="background-color: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 text-white rounded transition text-sm font-medium hover:opacity-90" style="background-color: #1E3A8A;">
                            <i class="fas fa-save mr-1"></i> Save Changes
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
            
            // Display first service or "Multiple services"
            let serviceText = '';
            if (data.items.length > 0) {
                serviceText = data.items[0].description;
                if (data.items.length > 1) {
                    serviceText += ' +' + (data.items.length - 1) + ' more';
                }
            }
            document.getElementById('billService').textContent = serviceText || 'No services';
            
            // Add date field
            document.getElementById('billDate').textContent = data.created_at ? new Date(data.created_at).toLocaleDateString() : 'N/A';
            
            let itemsHTML = '';
            data.items.forEach((item, index) => {
                itemsHTML += `
                    <div class="flex justify-between p-4 ${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'}">
                        <span class="text-sm font-medium" style="color: #374151;">${item.description}</span>
                        <span class="font-bold text-sm" style="color: #1E3A8A;">₱${parseFloat(item.amount).toFixed(2)}</span>
                    </div>
                `;
            });
            document.getElementById('billItemsList').innerHTML = itemsHTML;

            document.getElementById('billSubtotal').textContent = '₱' + parseFloat(data.total_amount).toFixed(2);
            document.getElementById('billBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            document.getElementById('displayBalance').textContent = '₱' + parseFloat(data.balance).toFixed(2);
            
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
        <div class="bill-item p-4 border rounded" style="border-color: #D1D5DB; background-color: #F9FAFB;">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: #6B7280;">Description</label>
                    <input type="text" name="items[${index}][description]" value="${description}" placeholder="Service description" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 text-sm" style="border-color: #D1D5DB;">
                </div>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold mb-1" style="color: #6B7280;">Amount</label>
                        <input type="number" name="items[${index}][amount]" value="${amount}" placeholder="0.00" step="0.01" min="0" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 text-sm" style="border-color: #D1D5DB;">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeEditItem(this)" class="px-3 py-2 text-white rounded transition text-sm font-bold hover:opacity-90" style="background-color: #DC2626;" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
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