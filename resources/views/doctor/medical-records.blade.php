@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-1" style="color: #2c3e50;">Medical Records</h1>
        <p style="color: #5d6d7e;">Manage and view patient medical records</p>
    </div>

    <!-- Add Button and Search Section -->
    <div class="flex justify-between items-center gap-4 mb-6">
        <a href="{{ route('medical-records.create') }}" class="text-white px-6 py-2.5 rounded-lg flex items-center gap-2 transition" style="background-color: #0d5cb6;">
            <i class="fas fa-plus"></i>
            Add Medical Record
        </a>

        <form method="GET" action="{{ route('doctor.medical-records') }}" class="flex items-center gap-3 max-w-md flex-1">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2" style="color: #95a5a6;"></i>
                <input id="searchInput" type="text" name="search" value="{{ request('search') }}" placeholder="Search pet, owner, service..." 
                    class="pl-10 pr-4 py-2.5 border rounded-lg bg-white focus:ring-2 focus:border-transparent w-full" style="border-color: #d1d5db; outline: none;">
            </div>

            @if(request('search'))
                <a href="{{ route('doctor.medical-records') }}" class="px-4 py-2.5 rounded-lg transition whitespace-nowrap" style="background-color: #95a5a6; color: #ffffff;">
                    Clear
                </a>
            @endif
        </form>
    </div>
    @if (session('success'))
        <div class="border px-4 py-3 rounded-lg mb-6 flex justify-between items-center" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none';" style="color: #155724;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="border px-4 py-3 rounded-lg mb-6 flex justify-between items-center" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24;">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.style.display='none';" style="color: #721c24;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Records Table -->
    <div id="recordsContainer">
        @if($medicalRecords->count())
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="border-b" style="background-color: #34495e; border-color: #e5e7eb;">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Pet Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Owner</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Diagnosis</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Treatment</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: #ffffff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: #e5e7eb;">
                        @foreach($medicalRecords as $record)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm" style="color: #2c3e50;">{{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                                        <i class="fas fa-paw" style="color: #0d5cb6;"></i>
                                    </div>
                                    <span class="font-medium" style="color: #2c3e50;">{{ $record->pet->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">{{ $record->pet->owner->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: #5d6d7e;">
                                <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: #2c3e50;">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-4">
                                    <a href="{{ route('medical-records.show', $record->id) }}" class="transition" style="color: #0d5cb6;" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medical-records.edit', $record->id) }}" class="transition" style="color: #28a745;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
            <div class="bg-white rounded-lg shadow-sm">
                <div class="text-center py-16">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #ecf0f1;">
                            <i class="fas fa-file-medical text-3xl" style="color: #95a5a6;"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold mb-2" style="color: #2c3e50;">No Medical Records Found</h3>
                    <p class="mb-6" style="color: #5d6d7e;">
                        @if(request('search'))
                            No records match your search criteria.
                        @else
                            Get started by creating your first medical record for a patient.
                        @endif
                    </p>
                    @if(!request('search'))
                    <a href="{{ route('medical-records.create') }}" class="inline-flex items-center gap-2 text-white px-6 py-3 rounded-lg transition" style="background-color: #0d5cb6;">
                        <i class="fas fa-plus"></i>
                        Create First Record
                    </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .hover\:bg-gray-50:hover {
        background-color: rgba(249, 250, 251, 1);
    }
    
    a:hover {
        opacity: 0.8;
    }

    #searchInput:focus {
        ring: 2px;
        border-color: #0d5cb6;
        outline: none;
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