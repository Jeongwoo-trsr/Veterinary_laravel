@extends('layouts.app')

@section('title', 'My Patients')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold" style="color: #2c3e50;">My Patients</h1>
            <p style="color: #5d6d7e;">View and manage your patients</p>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="flex items-center justify-between gap-4">
        <!-- Species Filter Dropdown (Left) -->
        <form method="GET" action="{{ route('doctor.patients') }}" id="filterForm">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <select name="species" id="speciesFilter" class="px-4 py-2 font-bold rounded-lg cursor-pointer transition text-sm border-0" style="background-color: #f4d03f; color: #2c3e50;">
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
                class="w-full px-4 py-2 border rounded-lg focus:outline-none" 
                style="border-color: #d1d5db;"
                placeholder="Search pet name, breed, owner..." 
                value="{{ request('search') }}"
            >
        </div>
    </div>

    <!-- Patients Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color: #e5e7eb;">
                <thead style="background-color: #34495e;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Species</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Breed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Owner</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Appointments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y" style="border-color: #e5e7eb;">
                    @forelse($pets as $pet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                                        <i class="fas fa-paw" style="color: #0d5cb6;"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium" style="color: #2c3e50;">{{ $pet->name }}</div>
                                    <div class="text-xs" style="color: #5d6d7e;">
                                        {{ $pet->age ? $pet->age . ' years old' : 'Age not specified' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                            <span class="capitalize">{{ $pet->species }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                            {{ $pet->breed ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #5d6d7e;">
                            {{ $pet->owner->user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-medium" style="color: #0d5cb6;">
                                {{ $pet->appointments->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <button 
                                    onclick="openPetModal({{ $pet->id }})" 
                                    class="transition" 
                                    style="color: #0d5cb6;"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button 
                                    onclick="openEditModal({{ $pet->id }})" 
                                    class="transition" 
                                    style="color: #28a745;"
                                    title="Edit Pet"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-sm text-center" style="color: #5d6d7e;">
                            No patients found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pets->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: #e5e7eb;">
            {{ $pets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Pet Details Modal (View Only) -->
<div id="petModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold" style="color: #2c3e50;">Patient Details</h3>
            <button onclick="closePetModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="modalContent" class="modal-body">
            <div class="flex justify-center items-center py-8">
                <i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Pet Edit Modal -->
<div id="editModal" style="display: none;" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-xl font-bold" style="color: #2c3e50;">Edit Patient Details</h3>
            <button onclick="closeEditModal()" class="modal-close-btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editPetForm" class="modal-body">
            @csrf
            @method('PUT')
            <div id="editFormContent">
                <div class="flex justify-center items-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i>
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
        modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i></div>';
        
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
                            <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Pet Information</h4>
                            <div class="space-y-3">
                                <div><label class="text-sm" style="color: #5d6d7e;">Name</label><p class="font-medium" style="color: #2c3e50;">${data.name || 'N/A'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Species</label><p class="font-medium capitalize" style="color: #2c3e50;">${data.species || 'N/A'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Breed</label><p class="font-medium" style="color: #2c3e50;">${data.breed || 'N/A'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Age</label><p class="font-medium" style="color: #2c3e50;">${data.age ? data.age + ' years old' : 'Not specified'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Gender</label><p class="font-medium capitalize" style="color: #2c3e50;">${data.gender || 'Not specified'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Weight</label><p class="font-medium" style="color: #2c3e50;">${data.weight ? data.weight + ' kg' : 'Not specified'}</p></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Owner Information</h4>
                            <div class="space-y-3">
                                <div><label class="text-sm" style="color: #5d6d7e;">Owner Name</label><p class="font-medium" style="color: #2c3e50;">${ownerUser.name || 'Not specified'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Email</label><p class="font-medium" style="color: #2c3e50;">${ownerUser.email || 'Not specified'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Emergency Contact</label><p class="font-medium" style="color: #2c3e50;">${owner.emergency_contact || 'Not specified'}</p></div>
                                <div><label class="text-sm" style="color: #5d6d7e;">Emergency Phone</label><p class="font-medium" style="color: #2c3e50;">${owner.emergency_phone || 'Not specified'}</p></div>
                            </div>
                        </div>
                    </div>
                    ${data.medical_notes ? `<div class="mt-6 pt-6 border-t"><h4 class="text-lg font-semibold mb-3" style="color: #2c3e50;">Medical Notes</h4><p class="text-sm" style="color: #5d6d7e;">${data.medical_notes}</p></div>` : ''}
                    <div class="mt-6 pt-6 border-t flex justify-between items-center">
                        <div><label class="text-sm" style="color: #5d6d7e;">Total Appointments</label><p class="text-lg font-bold" style="color: #0d5cb6;">${data.appointments_count || 0}</p></div>
                        <button type="button" onclick="closePetModal();openEditModal(${data.id})" class="px-4 py-2 text-white rounded-lg transition" style="background-color: #28a745;"><i class="fas fa-edit mr-2"></i>Edit Pet</button>
                    </div>
                `;
            })
            .catch(error => {
                modalContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-4xl mb-3" style="color: #d32f2f;"></i><p class="font-medium" style="color: #d32f2f;">Error loading pet details</p></div>';
            });
    }

    // Open edit modal
    function openEditModal(petId) {
        const modal = document.getElementById('editModal');
        const formContent = document.getElementById('editFormContent');
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        formContent.innerHTML = '<div class="flex justify-center items-center py-8"><i class="fas fa-spinner fa-spin text-3xl" style="color: #0d5cb6;"></i></div>';
        
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
                            <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Pet Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Name *</label>
                                    <input type="text" name="name" value="${data.name || ''}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Species *</label>
                                    <input type="text" name="species" value="${data.species || ''}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Breed</label>
                                    <input type="text" name="breed" value="${data.breed || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Age *</label>
                                    <input type="number" name="age" value="${data.age || ''}" required min="0" max="30" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold mb-4" style="color: #2c3e50;">Physical Details</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Gender *</label>
                                    <select name="gender" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                        <option value="male" ${data.gender === 'male' ? 'selected' : ''}>Male</option>
                                        <option value="female" ${data.gender === 'female' ? 'selected' : ''}>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Weight (kg)</label>
                                    <input type="number" name="weight" value="${data.weight || ''}" step="0.01" min="0" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Color</label>
                                    <input type="text" name="color" value="${data.color || ''}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium mb-1" style="color: #2c3e50;">Medical Notes</label>
                        <textarea name="medical_notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:border-blue-500" style="border-color: #d1d5db;">${data.medical_notes || ''}</textarea>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-lg transition" style="background-color: #95a5a6; color: #ffffff;">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-white rounded-lg transition" style="background-color: #0d5cb6;"><i class="fas fa-save mr-2"></i>Save Changes</button>
                    </div>
                `;
                
                // Handle form submission
                document.getElementById('editPetForm').onsubmit = function(e) {
                    e.preventDefault();
                    savePetChanges(petId);
                };
            })
            .catch(error => {
                formContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-4xl mb-3" style="color: #d32f2f;"></i><p class="font-medium" style="color: #d32f2f;">Error loading pet details</p></div>';
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
        background-color: rgba(44, 62, 80, 0.5);
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
        color: #95a5a6;
        transition: color 0.2s;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
    }
    
    .modal-close-btn:hover {
        color: #5d6d7e;
    }
    
    .modal-body {
        padding: 24px;
    }
    
    #speciesFilter {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 20px;
        padding-right: 32px;
    }
    
    #speciesFilter:hover {
        background-color: #f9e79f;
    }
    
    button:hover, a:hover {
        opacity: 0.8;
    }
</style>
@endsection