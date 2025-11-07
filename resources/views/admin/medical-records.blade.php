@extends('layouts.app')

@section('title', 'Medical Records')

@section('content')
<div class="container mx-auto px-4 py-8">
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

    <!-- Header and Search Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#1e3a5f]">Medical Records</h1>
        
        <form method="GET" action="{{ route('medical-records.index') }}" class="flex items-center gap-2">
            <input 
                id="searchInput" 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search pet, owner, service..." 
                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d47a1] w-48"
            >
            <button 
                type="submit" 
                class="px-2.5 py-1.5 bg-[#2c3e50] text-white text-sm rounded-lg hover:bg-[#34495e] transition"
            >
                <i class="fas fa-search text-sm"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('medical-records.index') }}" class="px-2.5 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times text-sm"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Records Table -->
    <div id="recordsContainer">
        @if($medicalRecords->count())
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-[#1e3a5f]">
                <table class="w-full">
                    <thead class="bg-[#1e3a5f] border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Pet Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Owner</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Doctor</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Diagnosis</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Treatment</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($medicalRecords as $record)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration + ($medicalRecords->currentPage() - 1) * $medicalRecords->perPage() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $record->pet->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $record->pet->owner->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $record->doctor->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="truncate max-w-xs" title="{{ $record->diagnosis }}">{{ Str::limit($record->diagnosis, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="truncate max-w-xs" title="{{ $record->treatment }}">{{ Str::limit($record->treatment, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-6">
                                <a href="{{ route('medical-records.show', $record->id) }}" class="text-[#0d47a1] hover:text-[#1565c0] transition" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
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
            <div class="bg-white rounded-lg shadow-lg border-t-4 border-[#1e3a5f]">
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
                            No medical records available. Doctors can create medical records for patients.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection