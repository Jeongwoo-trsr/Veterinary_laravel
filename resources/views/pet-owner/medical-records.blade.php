@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <h1 class="text-xl sm:text-2xl font-bold">My Pets' Medical Records</h1>
    </div>

    <div class="bg-blue-50 shadow-lg rounded-lg p-3 sm:p-4 lg:p-6">
        @if($medicalRecords->count())
            <!-- Mobile View: Cards -->
            <div class="block md:hidden space-y-3 sm:space-y-4">
                @foreach($medicalRecords as $record)
                    <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                        <!-- Pet Name Header -->
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-paw text-blue-600"></i>
                                <h3 class="font-bold text-gray-900">{{ $record->pet->name }}</h3>
                            </div>
                            <span class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y') }}</span>
                        </div>

                        <!-- Record Details -->
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Doctor</p>
                                <p class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-user-md text-blue-500 mr-1"></i>
                                    {{ $record->doctor->user->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">Diagnosis</p>
                                <p class="text-sm text-gray-700 line-clamp-2">{{ $record->diagnosis }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 mb-1">Treatment</p>
                                <p class="text-sm text-gray-700 line-clamp-2">{{ $record->treatment }}</p>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-2 border-t border-gray-100">
                                <a href="{{ route('medical-records.show', $record->id) }}" 
                                   class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition text-sm font-medium">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($medicalRecords as $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $record->pet->name }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->doctor->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 text-sm text-gray-600">
                                    <span class="line-clamp-2">{{ Str::limit($record->diagnosis, 60) }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 text-sm text-gray-600">
                                    <span class="line-clamp-2">{{ Str::limit($record->treatment, 60) }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('medical-records.show', $record->id) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-600 hover:bg-blue-200 rounded transition text-sm font-medium" 
                                       title="View Details">
                                        <i class="fas fa-eye mr-2"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $medicalRecords->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-medical text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-base sm:text-lg">No medical records found for your pets.</p>
            </div>
        @endif
    </div>
</div>
@endsection

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
