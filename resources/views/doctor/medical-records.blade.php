@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Medical Records</h1>
            <p class="text-gray-600 mt-1">Manage and view patient medical records</p>
        </div>
        <a href="{{ route('medical-records.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-plus"></i>
            Add Medical Record
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none';" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.style.display='none';" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Search Section -->
    <div class="mb-8 flex justify-between items-center gap-4">
        <form method="GET" action="{{ route('doctor.medical-records') }}" class="flex items-center gap-4 w-full">
            <div class="relative ml-auto w-full max-w-md">
                <input id="searchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Search pet, owner, service..." 
                    class="px-4 py-2 border border-gray-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full">
            </div>

            @if(request('search'))
                <a href="{{ route('doctor.medical-records') }}" class="ml-3 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Clear</a>
            @endif
        </form>
    </div>

    <!-- Records Table -->
    @if($medicalRecords->count())
        <div id="recordsContainer" class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pet Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Owner</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Diagnosis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Treatment</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($medicalRecords as $record)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-paw text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $record->pet->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $record->pet->owner->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 30) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 30) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($record->follow_up_date && $record->follow_up_date >= now()->toDateString())
                                <span class="text-yellow-800 font-semibold">Follow-up</span>
                            @else
                                <span class="text-green-800 font-semibold">Resolved</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-4">
                                <a href="{{ route('medical-records.show', $record->id) }}" class="text-blue-600 hover:text-blue-900 transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('medical-records.edit', $record->id) }}" class="text-green-600 hover:text-green-900 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('medical-records.destroy', $record->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Delete">
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

        <!-- Pagination -->
        <div class="mt-6">
            {{ $medicalRecords->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div id="recordsContainer" class="bg-white rounded-lg shadow-sm">
            <div class="text-center py-16">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-medical text-3xl text-gray-400"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Medical Records Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('search'))
                        No records match your search criteria.
                    @else
                        Get started by creating your first medical record for a patient.
                    @endif
                </p>
                @if(!request('search'))
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-plus"></i>
                    Create First Record
                </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .hover\:bg-gray-50:hover {
        background-color: rgba(249, 250, 251, 1);
    }
</style>

<script>
(function(){
    const searchInput = document.getElementById('searchInput');
    const recordsContainer = document.getElementById('recordsContainer');
    const baseUrl = "{{ route('doctor.medical-records') }}";

    function debounce(fn, delay) {
        let timer;
        return function(...args){
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        }
    }

    async function fetchRecords(searchQuery = ''){
        const params = new URLSearchParams();
        if(searchQuery) params.append('search', searchQuery);
        
        const url = baseUrl + (params.toString() ? ('?' + params.toString()) : '');

        try{
            const res = await fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' }, 
                credentials: 'same-origin' 
            });
            
            if(!res.ok) {
                console.warn('Fetch records failed', res.status);
                return;
            }
            
            const text = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newContainer = doc.getElementById('recordsContainer');
            
            if(newContainer && recordsContainer){
                recordsContainer.innerHTML = newContainer.innerHTML;
            }
        } catch(e) { 
            console.error('Error fetching records:', e); 
        }
    }

    const debouncedFetch = debounce(function(){
        const query = searchInput.value.trim();
        fetchRecords(query);
    }, 300);

    searchInput.addEventListener('input', debouncedFetch);
})();
</script>
@endsection