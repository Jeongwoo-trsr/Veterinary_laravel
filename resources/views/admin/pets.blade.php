@extends('layouts.app')

@section('title', 'Pet Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#2d3748]">
            <i class="fas fa-paw text-[#fcd34d] mr-2"></i>Pet Management
        </h1>
        <a href="{{ route('pets.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition-colors shadow-md">
            <i class="fas fa-plus-circle mr-2 text-[#fcd34d]"></i>
            Add New Pet
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pets') }}" 
               class="px-4 py-2 rounded-lg {{ !request('approval_status') ? 'bg-[#0066cc] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-list mr-1"></i> All Pets
            </a>
            <a href="{{ route('admin.pets', ['approval_status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg {{ request('approval_status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-clock mr-1"></i> Pending Approval
                @if($pendingCount ?? 0 > 0)
                    <span class="ml-1 px-2 py-0.5 bg-white text-yellow-700 rounded-full text-xs font-bold">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pets', ['approval_status' => 'approved']) }}" 
               class="px-4 py-2 rounded-lg {{ request('approval_status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-check-circle mr-1"></i> Approved
            </a>
            <a href="{{ route('admin.pets', ['approval_status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-lg {{ request('approval_status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-times-circle mr-1"></i> Rejected
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.pets') }}" class="flex gap-2">
            <input type="hidden" name="approval_status" value="{{ request('approval_status') }}">
            <input type="text" name="search" 
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]" 
                   placeholder="Search pets by name, species, breed, or owner..." 
                   value="{{ request('search') }}">
            <button type="submit" 
                    class="px-6 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82]">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Pets Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#1e3a5f] text-white">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Pet Name</th>
                    <th class="px-4 py-3">Species/Breed</th>
                    <th class="px-4 py-3">Owner</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Registered</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pets as $pet)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-[#fcd34d] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-paw text-[#1e3a5f]"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $pet->name }}</p>
                                    <p class="text-xs text-gray-500">Age: {{ $pet->age }} years</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-800">{{ ucfirst($pet->species) }}</p>
                            @if($pet->breed)
                                <p class="text-xs text-gray-500">{{ $pet->breed }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-800">{{ $pet->owner->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $pet->owner->user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($pet->approval_status === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold inline-flex items-center">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @elseif($pet->approval_status === 'approved')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold inline-flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i> Approved
                                </span>
                            @elseif($pet->approval_status === 'rejected')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold inline-flex items-center">
                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $pet->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('pets.show', $pet->id) }}" 
                                   class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($pet->approval_status === 'pending')
                                    <!-- Approve Button -->
                                    <form action="{{ route('pets.approve', $pet->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600"
                                                onclick="return confirm('Approve this pet registration?')"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <button onclick="showRejectModal({{ $pet->id }}, '{{ $pet->name }}')"
                                            class="px-3 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                <a href="{{ route('pets.edit', $pet->id) }}" 
                                   class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-paw text-4xl mb-2 text-gray-300"></i>
                            <p>No pets found{{ request('search') ? ' for "'.request('search').'"' : '' }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $pets->links() }}
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-times-circle text-red-500 mr-2"></i>Reject Pet Registration
        </h3>
        <p class="text-gray-600 mb-4">
            Pet: <span id="rejectPetName" class="font-semibold"></span>
        </p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Rejection *
                </label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Please provide a reason for rejecting this pet registration..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-times-circle mr-1"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(petId, petName) {
    document.getElementById('rejectPetName').textContent = petName;
    document.getElementById('rejectForm').action = `/pets/${petId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

@endsection