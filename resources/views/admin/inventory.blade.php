@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<h1 class="text-2xl font-bold mb-6">Inventory</h1>

<!-- Welcome message -->
<p class="text-gray-600 mb-6">Welcome to the Inventory Management System</p>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.inventory') }}" class="bg-blue-100 p-1 rounded-lg shadow hover:shadow-lg transition cursor-pointer text-center">
        <h3 class="text-gray-600 text-sm font-medium">Total Items</h3>
        <p class="text-xl font-bold text-blue-600">{{ $totalItems }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'low-stock') }}" class="bg-blue-100 p-1 rounded-lg shadow hover:shadow-lg transition cursor-pointer text-center">
        <h3 class="text-gray-600 text-sm font-medium">Low Stock Items</h3>
        <p class="text-xl font-bold text-yellow-600">{{ $lowStockCount }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'top-used') }}" class="bg-blue-100 p-1 rounded-lg shadow hover:shadow-lg transition cursor-pointer text-center">
        <h3 class="text-gray-600 text-sm font-medium">Top Used Items</h3>
        <p class="text-xl font-bold text-purple-600">{{ $topUsedItems->count() }}</p>
    </a>
    
    <a href="{{ route('admin.inventory.filter', 'expired') }}" class="bg-blue-100 p-1 rounded-lg shadow hover:shadow-lg transition cursor-pointer text-center">
        <h3 class="text-gray-600 text-sm font-medium">Expired Items</h3>
        <p class="text-xl font-bold text-red-600">{{ $expiredCount }}</p>
    </a>
</div>

<!-- Inventory Items Section -->
<div class="bg-white shadow-lg rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
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

    <!-- Search and Filter Section -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <form method="GET" action="{{ route('inventory.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search" 
                        value="{{ $search }}" 
                        placeholder="Search by name, category, or supplier..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select 
                        name="category" 
                        id="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-2 mt-4">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Apply Filters
                </button>
                <a 
                    href="{{ route('inventory.index') }}" 
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition inline-block"
                >
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Active Filters Display -->
    @if($search || $category)
        <div class="mb-4 flex flex-wrap gap-2 items-center">
            <span class="text-sm font-medium text-gray-600">Active filters:</span>
            @if($search)
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                    Search: "{{ $search }}"
                    <a href="{{ route('inventory.index', ['category' => $category]) }}" class="hover:text-blue-900 font-bold text-lg leading-none">&times;</a>
                </span>
            @endif
            @if($category)
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
                    Category: {{ $category }}
                    <a href="{{ route('inventory.index', ['search' => $search]) }}" class="hover:text-purple-900 font-bold text-lg leading-none">&times;</a>
                </span>
            @endif
        </div>
    @endif

    @if($items->count())
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quick Adjust</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                {{ $item->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="@if($item->isLowStock()) text-red-600 font-bold @endif">
                                {{ $item->current_stock }} {{ $item->unit }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-4">
                                <form action="{{ route('inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="adjustment_type" value="reduce">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-black rounded hover:bg-red-600 font-bold">
                                        -
                                    </button>
                                </form>
                                <span class="font-bold min-w-[2rem] text-center">{{ $item->current_stock }}</span>
                                <form action="{{ route('inventory.adjust-stock', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="adjustment_type" value="add">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-3 py-1 bg-green-500 text-black rounded hover:bg-green-600 font-bold">
                                        +
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    @else
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <p class="text-gray-500 text-lg mb-2">No inventory items found.</p>
            @if($search || $category)
                <p class="text-sm text-gray-400">Try adjusting your filters</p>
            @endif
        </div>
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
