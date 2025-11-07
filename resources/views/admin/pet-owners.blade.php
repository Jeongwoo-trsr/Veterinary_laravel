@extends('layouts.app')

@section('title', 'Pet Owners')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#1e3a5f]">Pet Owners</h1>
    <button onclick="openAddModal()" class="px-4 py-2 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition">
        <i class="fas fa-plus mr-2"></i>Add Pet Owner
    </button>
</div>

<div class="bg-white shadow-lg rounded-lg p-6 border-t-4 border-[#1e3a5f]">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-4 flex items-center gap-2">
        <form method="GET" action="{{ route('admin.pet-owners') }}" class="flex items-center gap-2 flex-1">
            <input 
                type="text" 
                name="search" 
                class="flex-1 px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1]" 
                placeholder="Search by name or email..." 
                value="{{ request('search') }}"
            >
            <button 
                type="submit" 
                class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition"
            >
                <i class="fas fa-search text-sm"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.pet-owners') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times text-sm"></i>
                </a>
            @endif
        </form>
    </div>

    @if($petOwners->count())
        <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-[#1e3a5f]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase">Email</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Total Pets</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-200 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($petOwners as $owner)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ ($petOwners->currentPage() - 1) * $petOwners->perPage() + $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $owner->user->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $owner->user->email }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $owner->pets->count() }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-4">
                            <a href="{{ route('admin.pet-owners.show', $owner->id) }}" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pet-owners.edit', $owner->id) }}" class="text-[#d4911e] hover:text-[#b8860b] transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $petOwners->appends(['search' => request('search')])->links() }}</div>
    @else
        <p class="text-gray-500 text-center py-8">No pet owners found.</p>
    @endif
</div>

<!-- Add Pet Owner Modal -->
<div id="addPetOwnerModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(30, 58, 95, 0.5);">
    <div style="background-color: white; margin: 3% auto; padding: 32px; border: 1px solid #888; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; border-top: 4px solid #1e3a5f;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#1e3a5f]">Add New Pet Owner</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>

        <form action="{{ route('admin.pet-owners.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Email *</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Password *</label>
                <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Phone</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_phone" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1e3a5f] mb-1">Emergency Contact Name</label>
                <input type="text" name="emergency_contact" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#0d47a1]">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-[#0d47a1] text-white rounded hover:bg-[#1565c0] transition">
                    Add Pet Owner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addPetOwnerModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addPetOwnerModal').style.display = 'none';
}

document.getElementById('addPetOwnerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Auto-open modal if there are validation errors
@if($errors->any())
    openAddModal();
@endif
</script>
@endsection