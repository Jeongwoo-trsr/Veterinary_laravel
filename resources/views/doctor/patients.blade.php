@extends('layouts.app')

@section('title', 'My Patients')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Patients</h1>
            <p class="text-gray-600">View and manage your patients</p>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Species Filter Dropdown (Left) -->
        <form method="GET" action="{{ route('doctor.patients') }}" id="filterForm">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <select name="species" id="speciesFilter" class="px-4 py-2 text-gray-900 font-bold rounded-lg cursor-pointer transition text-sm border-0" style="background-color:#FCD34D;color:#111827;font-weight:600;padding-right:32px;">
                <option value="">All Species</option>
                <option value="dog" {{ request('species') == 'dog' ? 'selected' : '' }}>Dog</option>
                <option value="cat" {{ request('species') == 'cat' ? 'selected' : '' }}>Cat</option>
                <option value="bird" {{ request('species') == 'bird' ? 'selected' : '' }}>Bird</option>
                <option value="rabbit" {{ request('species') == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                <option value="hamster" {{ request('species') == 'hamster' ? 'selected' : '' }}>Hamster</option>
                <option value="guinea_pig" {{ request('species') == 'guinea_pig' ? 'selected' : '' }}>Guinea Pig</option>
                <option value="reptile" {{ request('species') == 'reptile' ? 'selected' : '' }}>Reptile</option>
                <option value="other" {{ request('species') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </form>

        <!-- Search Input (Right) -->
        <div class="ml-auto w-full max-w-md relative">
            <input type="hidden" name="species" value="{{ request('species') }}" id="speciesHidden">
            <input 
                id="searchInput"
                type="text" 
                name="search" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Search pet name, breed, owner..." 
                value="{{ request('search') }}"
            >
            <!-- live search: no submit button and no clear button -->
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Species</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Breed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Appointments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pets as $pet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-paw text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $pet->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $pet->age ? $pet->age . ' years old' : 'Age not specified' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <span class="capitalize">{{ $pet->species }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $pet->breed ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $pet->owner->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 text-blue-800 rounded-full text-xs font-medium">
                                {{ $pet->appointments->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <button 
                                    onclick="openPetModal({{ $pet->id }})" 
                                    class="text-blue-600 hover:text-blue-900 transition" 
                                    title="View Details"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button 
                                    onclick="openEditModal({{ $pet->id }})" 
                                    class="text-green-600 hover:text-green-900 transition" 
                                    title="Edit Pet"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-sm text-gray-500 text-center">
                            No patients found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Pet Details Modal (View Only) -->
<div id="petModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold text-gray-900">Patient Details</h3>
            <button onclick="closePetModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="modal-body">
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pet Edit Modal -->
<div id="editModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold text-gray-900">Edit Patient Details</h3>
            <button onclick="closeEditModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPetForm" class="modal-body">
            @csrf
            @method('PUT')
            <div id="editFormContent">
                <div class="flex justify-center items-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-submit form when species dropdown changes
    const searchInput = document.getElementById('searchInput');
    const speciesFilter = document.getElementById('speciesFilter');
    const speciesHidden = document.getElementById('speciesHidden');
    const resultsContainer = document.querySelector('.bg-white.shadow.rounded-lg.overflow-hidden');

    function debounce(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    async function fetchResults(search = '', species = '') {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (species) params.append('species', species);
        const url = `/doctor/patients?${params.toString()}`;

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newContainer = doc.querySelector('.bg-white.shadow.rounded-lg.overflow-hidden');
            if (newContainer && resultsContainer) {
                resultsContainer.innerHTML = newContainer.innerHTML;
            }
        } catch (err) {
            console.error('Search error', err);
        }
    }

    // when species changes, update hidden field and fetch results via AJAX
    speciesFilter.addEventListener('change', function() {
        speciesHidden.value = this.value;
        fetchResults(searchInput.value, this.value);
    });

    // live search with debounce
    searchInput.addEventListener('input', debounce(function(e) {
        const q = e.target.value.trim();
        fetchResults(q, speciesFilter.value);
    }, 300));

    // Open view-only modal
    function openPetModal(petId) {
        const modal = document.getElementById('petModal');
        const modalContent = document.getElementById('modalContent');
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i></div>';
        
        fetch(`/doctor/patients/${petId}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                const owner = data.owner || {};
                const ownerUser = owner.user || {};
                
                modalContent.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Pet Information</h4>
                            <div class="space-y-3">
                                <div><label class="text-sm text-gray-600">Name</label><p class="font-medium">${data.name || 'N/A'}</p></div>
                                <div><label class="text-sm text-gray-600">Species</label><p class="font-medium capitalize">${data.species || 'N/A'}</p></div>
                                <div><label class="text-sm text-gray-600">Breed</label><p class="font-medium">${data.breed || 'N/A'}</p></div>
                                <div><label class="text-sm text-gray-600">Age</label><p class="font-medium">${data.age ? data.age + ' years old' : 'Not specified'}</p></div>
                                <div><label class="text-sm text-gray-600">Gender</label><p class="font-medium capitalize">${data.gender || 'Not specified'}</p></div>
                                <div><label class="text-sm text-gray-600">Weight</label><p class="font-medium">${data.weight ? data.weight + ' kg' : 'Not specified'}</p></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Owner Information</h4>
                            <div class="space-y-3">
                                <div><label class="text-sm text-gray-600">Owner Name</label><p class="font-medium">${ownerUser.name || 'Not specified'}</p></div>
                                <div><label class="text-sm text-gray-600">Email</label><p class="font-medium">${ownerUser.email || 'Not specified'}</p></div>
                                <div><label class="text-sm text-gray-600">Emergency Contact</label><p class="font-medium">${owner.emergency_contact || 'Not specified'}</p></div>
                                <div><label class="text-sm text-gray-600">Emergency Phone</label><p class="font-medium">${owner.emergency_phone || 'Not specified'}</p></div>
                            </div>
                        </div>
                    </div>
                    ${data.medical_notes ? `<div class="mt-6 pt-6 border-t"><h4 class="text-lg font-semibold mb-3 text-gray-800">Medical Notes</h4><p class="text-sm text-gray-600">${data.medical_notes}</p></div>` : ''}
                    <div class="mt-6 pt-6 border-t flex justify-between items-center">
                        <div><label class="text-sm text-gray-600">Total Appointments</label><p class="text-lg font-bold text-blue-600">${data.appointments_count || 0}</p></div>
                        <button type="button" onclick="closePetModal();openEditModal(${data.id})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"><i class="fas fa-edit mr-2"></i>Edit Pet</button>
                    </div>
                `;
            })
            .catch(error => {
                modalContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-red-600 text-4xl mb-3"></i><p class="text-red-600 font-medium">Error loading pet details</p></div>';
            });
    }

    // Open edit modal
    function openEditModal(petId) {
        const modal = document.getElementById('editModal');
        const formContent = document.getElementById('editFormContent');
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        formContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i></div>';
        
        fetch(`/doctor/patients/${petId}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                formContent.innerHTML = `
                    <input type="hidden" name="pet_id" value="${data.id}">
                    <input type="hidden" name="owner_id" value="${data.owner_id}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Pet Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input type="text" name="name" value="${data.name || ''}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Species *</label>
                                    <input type="text" name="species" value="${data.species || ''}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Breed</label>
                                    <input type="text" name="breed" value="${data.breed || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age *</label>
                                    <input type="number" name="age" value="${data.age || ''}" required min="0" max="30" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Physical Details</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                                    <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="male" ${data.gender === 'male' ? 'selected' : ''}>Male</option>
                                        <option value="female" ${data.gender === 'female' ? 'selected' : ''}>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                    <input type="number" name="weight" value="${data.weight || ''}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                    <input type="text" name="color" value="${data.color || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medical Notes</label>
                        <textarea name="medical_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${data.medical_notes || ''}</textarea>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"><i class="fas fa-save mr-2"></i>Save Changes</button>
                    </div>
                `;
                
                // Handle form submission
                document.getElementById('editPetForm').onsubmit = function(e) {
                    e.preventDefault();
                    savePetChanges(petId);
                };
            })
            .catch(error => {
                formContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-red-600 text-4xl mb-3"></i><p class="text-red-600 font-medium">Error loading pet details</p></div>';
            });
    }

    // Save pet changes
    function savePetChanges(petId) {
        const form = document.getElementById('editPetForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        
        fetch(`/pets/${petId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
            .then(async response => {
                const text = await response.text();
                let json = null;
                try { json = JSON.parse(text); } catch (e) { json = null; }

                if (!response.ok) {
                    let msg = 'Error updating pet details. Please try again.';
                    if (json && json.message) msg = json.message;
                    else if (json && json.errors) {
                        msg = Object.values(json.errors).flat().join('\n');
                    }
                    throw new Error(msg);
                }

                // Success
                closeEditModal();
                alert('Pet details updated successfully!');
                window.location.reload();
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert(error.message || 'Error updating pet details. Please try again.');
            });
    }

    // Close modals
    function closePetModal() {
        document.getElementById('petModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const petModal = document.getElementById('petModal');
        const editModal = document.getElementById('editModal');
        if (event.target == petModal) closePetModal();
        if (event.target == editModal) closeEditModal();
    }
</script>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow-y: auto;
    }
    
    .modal-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: modalFadeIn 0.3s ease-out;
    }
    
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 10;
    }
    
    .modal-close-btn {
        color: #9ca3af;
        transition: color 0.2s;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
    }
    
    .modal-close-btn:hover {
        color: #4b5563;
    }
    
    .modal-body {
        padding: 24px;
    }
    
    #speciesFilter {
        background-color: #FCD34D;
        color: #111827;
        font-weight: bold;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }
    
    #speciesFilter:hover {
        background-color: #FBBF24;
    }
</style>
@endsection