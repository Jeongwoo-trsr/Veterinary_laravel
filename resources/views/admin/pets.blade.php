@extends('layouts.app')

@section('title', 'Manage Pets')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Pets</h1>
            <p class="text-gray-600">View and manage all pets in the system</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <select id="speciesFilter" class="bg-yellow-300 hover:bg-yellow-400 text-gray-800 px-4 py-2 rounded font-semibold cursor-pointer border-0">
                <option value="">All Species</option>
                <option value="dog" {{ request('species') == 'dog' ? 'selected' : '' }}>Dog</option>
                <option value="cat" {{ request('species') == 'cat' ? 'selected' : '' }}>Cat</option>
                <option value="bird" {{ request('species') == 'bird' ? 'selected' : '' }}>Bird</option>
                <option value="rabbit" {{ request('species') == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                <option value="other" {{ request('species') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="ml-auto w-full max-w-md">
            <div class="relative">
                <input id="searchInput" type="text" value="{{ request('search') }}" placeholder="Search pet name, breed, owner..." class="w-full px-4 py-2 border border-gray-300 rounded bg-white focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute right-3 top-2.5 text-gray-500"></i>
            </div>
        </div>

        @if(request('search') || request('species'))
            <a href="{{ route('admin.pets') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Clear</a>
        @endif
    </div>

    <div id="petsContainer">
        @if($pets->count())
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Species</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Breed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pets as $pet)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 mr-4">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-paw text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $pet->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($pet->gender ?? 'Unknown') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($pet->species) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $pet->breed ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $pet->owner->user->name ?? 'No Owner' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $pet->age ? $pet->age . ' yrs' : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('pets.show', $pet->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pets.edit', $pet->id) }}" class="text-green-600 hover:text-green-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.pets.destroy', $pet->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this pet?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-6 pb-6">
                {{ $pets->links() }}
            </div>
        </div>
        @else
        <div class="bg-white shadow rounded-lg">
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-paw text-3xl text-gray-400"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Pets Found</h3>
                <p class="text-gray-600">
                    @if(request('search') || request('species'))
                        No pets match your search criteria.
                    @else
                        No pets in the system yet.
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
(function(){
    const searchInput = document.getElementById('searchInput');
    const speciesFilter = document.getElementById('speciesFilter');
    const petsContainer = document.getElementById('petsContainer');
    const baseUrl = "{{ route('admin.pets') }}";

    function debounce(fn, delay) {
        let timer;
        return function(...args){
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        }
    }

    async function fetchPets(searchQuery = '', species = ''){
        const params = new URLSearchParams();
        if(searchQuery) params.append('search', searchQuery);
        if(species) params.append('species', species);
        
        const url = baseUrl + (params.toString() ? ('?' + params.toString()) : '');

        try{
            const res = await fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' }, 
                credentials: 'same-origin' 
            });
            
            if(!res.ok) {
                console.warn('Fetch pets failed', res.status);
                return;
            }
            
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newContainer = doc.getElementById('petsContainer');
            
            if(newContainer && petsContainer){
                petsContainer.innerHTML = newContainer.innerHTML;
            }
        } catch(e) { 
            console.error('Error fetching pets:', e); 
        }
    }

    const debouncedFetch = debounce(function(){
        const query = searchInput.value.trim();
        const species = speciesFilter.value;
        fetchPets(query, species);
    }, 300);

    searchInput.addEventListener('input', debouncedFetch);
    
    speciesFilter.addEventListener('change', function(){
        const query = searchInput.value.trim();
        const species = this.value;
        fetchPets(query, species);
    });
})();
</script>

<style>
    .hover\:bg-gray-50:hover {
        background-color: rgba(249, 250, 251, 1);
    }
</style>
@endsection