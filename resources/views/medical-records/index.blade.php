@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Medical Records</h1>
            <p class="text-gray-600 mt-1">View and manage patient medical records</p>
        </div>
        @if(Auth::user()->isDoctor())
        <a href="{{ route('medical-records.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Add Medical Record
        </a>
        @endif
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex justify-between items-center gap-4">
        <form method="GET" action="{{ route('medical-records.index') }}" class="flex items-center gap-4 w-full" id="recordsFilterForm">
            <!-- Search aligned to the right -->
            <div class="ml-auto w-full max-w-md">
                <div class="relative">
                    <input id="searchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Search pet, owner, service..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            @if(request('search'))
                <a href="{{ route('medical-records.index') }}" class="ml-3 px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Clear</a>
            @endif
        </form>
    </div>

    <!-- Medical Records Table -->
    <div id="recordsContainer">
    @if($medicalRecords->count())
        <div class="bg-gray-50 rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pet</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Owner</th>
                        @if(Auth::user()->isDoctor())
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Doctor</th>
                        @endif
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Diagnosis</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Treatment</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        @if(Auth::user()->isDoctor())
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        @endif
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($medicalRecords as $record)
                    <tr class="hover:bg-white transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-paw text-blue-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $record->pet->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $record->pet->owner->user->name }}</td>
                        @if(Auth::user()->isDoctor())
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $record->doctor->user->name }}</td>
                        @endif
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 30) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 30) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                        @if(Auth::user()->isDoctor())
                        <td class="px-6 py-4">
                            @if($record->follow_up_date && $record->follow_up_date >= now()->toDateString())
                                <span class="text-yellow-800 font-semibold">Follow-up</span>
                            @else
                                <span class="text-green-800 font-semibold">Resolved</span>
                            @endif
                        </td>
                        @endif
                        <td class="px-6 py-4">
                            @if(Auth::user()->isPetOwner())
                                <a href="{{ route('medical-records.show', $record->id) }}" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded transition text-sm font-medium" title="View Details">
                                    <i class="fas fa-eye mr-2"></i>
                                    View
                                </a>
                            @elseif(Auth::user()->isDoctor())
                                <div class="flex gap-2">
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
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-6 px-6 pb-6">
                {{ $medicalRecords->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gray-50 rounded-lg shadow">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Medical Records</h2>
            </div>
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-medical text-3xl text-blue-600"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Medical Records Found</h3>
                <p class="text-gray-600 mb-6">
                    @if(request('search'))
                        No records match your search criteria.
                    @else
                        Get started by creating your first medical record.
                    @endif
                </p>
                @if(Auth::user()->isDoctor() && !request('search'))
                <a href="{{ route('medical-records.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-plus"></i>
                    Create First Record
                </a>
                @endif
            </div>
        </div>
    @endif
    </div>
</div>

<script>
(function(){
    const searchInput = document.getElementById('searchInput');
    const recordsContainer = document.getElementById('recordsContainer');
    const baseUrl = "{{ route('medical-records.index') }}";

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

<style>
    /* Add any additional custom styles here */
</style>
@endsection