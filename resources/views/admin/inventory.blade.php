@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<h1 class="text-2xl font-bold mb-6">Inventory</h1>

<!-- Welcome message -->
<p class="text-gray-600 mb-6">Welcome to the Inventory Management System</p>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.inventory') }}" class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Total Items</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $totalItems }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'low-stock') }}" class="bg-yellow-50 p-6 rounded-lg shadow hover:shadow-lg transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Low Stock Alerts</h3>
        <p class="text-3xl font-bold text-yellow-600">⚠️</p>
        <p class="text-sm text-gray-500 mt-1">{{ $lowStockCount }} items</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'top-used') }}" class="bg-purple-50 p-6 rounded-lg shadow hover:shadow-lg transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Top Used Items</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $topUsedItems->count() }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'expired') }}" class="bg-red-50 p-6 rounded-lg shadow hover:shadow-lg transition cursor-pointer">
        <h3 class="text-gray-600 text-sm font-medium">Expired Items</h3>
        <p class="text-3xl font-bold text-red-600">{{ $expiredCount }}</p>
    </a>
</div>



<!-- Inventory Items Section -->
<div class="bg-blue-100 shadow-lg rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Inventory Items</h2>
        <button onclick="openAddModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Item
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($items->count())
        <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Stock</th>
                    <th class="px-6 py-3 text-center">Quick Adjust</th>
                    <th class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->category }}</td>
                    <td class="px-6 py-4">
                        <span class="@if($item->isLowStock()) text-red-600 font-bold @endif">
                            {{ $item->current_stock }} {{ $item->unit }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="adjustment_type" value="reduce">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="px-3 py-1 bg-red-500 text-blue rounded hover:bg-red-600 font-bold">
                                    -
                                </button>
                            </form>
                            <span class="font-bold">{{ $item->current_stock }}</span>
                            <form action="{{ route('inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="adjustment_type" value="add">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="px-3 py-1 bg-green-500 text-blue rounded hover:bg-green-600 font-bold">
                                    +
                                </button>
                            </form>
                        </div>
                   <td class="px-6 py-4 text-center">
    <div class="flex items-center justify-center gap-4">
        <button onclick="openEditModal({{ $item->id }})" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit
        </button>
        <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                Delete
            </button>
        </form>
    </div>
</td>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $items->links() }}</div>
    @else
        <p class="text-gray-500">No inventory items found.</p>
    @endif
</div>

<!-- Add/Edit Modal -->
<div id="itemModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 700px; position: relative;">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Edit Item</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>
        
        <form id="itemForm" method="POST">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="PUT">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                <input type="text" name="name" id="itemName" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="itemCategory" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Medicine">Medicine</option>
                    <option value="Consumables">Consumables</option>
                    <option value="Equipment">Equipment</option>
                    <option value="Pet Food">Pet Food</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="itemDescription" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                    <input type="number" name="current_stock" id="itemStock" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock Level</label>
                    <input type="number" name="minimum_stock_level" id="itemMinStock" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" id="itemUnit" required placeholder="pieces, bottles, etc." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                    <input type="number" name="selling_price" id="itemPrice" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional)</label>
                    <input type="date" name="expiry_date" id="itemExpiry" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                    <input type="text" name="batch_number" id="itemBatch" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name</label>
                <input type="text" name="supplier_name" id="itemSupplier" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Item';
    document.getElementById('itemForm').action = "{{ route('inventory.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('itemForm').reset();
    document.getElementById('itemModal').style.display = 'block';
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Edit Item';
    document.getElementById('itemForm').action = "/inventory/" + id;
    document.getElementById('formMethod').value = 'PUT';
    
    fetch('/inventory/' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('itemName').value = data.name;
            document.getElementById('itemCategory').value = data.category;
            document.getElementById('itemDescription').value = data.description || '';
            document.getElementById('itemStock').value = data.current_stock;
            document.getElementById('itemMinStock').value = data.minimum_stock_level;
            document.getElementById('itemUnit').value = data.unit;
            document.getElementById('itemPrice').value = data.selling_price;
            if (data.expiry_date) {
                    const date = new Date(data.expiry_date);
                    const formatted = date.toISOString().split('T')[0];
                        document.getElementById('itemExpiry').value = formatted;
                } else {
                      document.getElementById('itemExpiry').value = '';
                }
            document.getElementById('itemBatch').value = data.batch_number || '';
            document.getElementById('itemSupplier').value = data.supplier_name || '';
            
            document.getElementById('itemModal').style.display = 'block';
        });
}

function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

document.getElementById('itemModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection