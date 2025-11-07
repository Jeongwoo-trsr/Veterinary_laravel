@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <h1 class="text-xl sm:text-2xl font-bold" style="color: #2c3e50;">My Pets' Medical Records</h1>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-3 sm:p-4 lg:p-6 border-2" style="border-color: #e5e7eb;">
        @if($medicalRecords->count())
            <!-- Mobile View: Cards -->
            <div class="block md:hidden space-y-3 sm:space-y-4">
                @foreach($medicalRecords as $record)
                    <div class="bg-white rounded-lg shadow-md border-2 p-4 transition-all" style="border-color: #e5e7eb;">
                        <!-- Pet Name Header -->
                        <div class="flex items-center justify-between mb-3 pb-3 border-b-2" style="border-color: #e5e7eb;">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: #d6eaf8;">
                                    <i class="fas fa-paw text-sm" style="color: #0d5cb6;"></i>
                                </div>
                                <h3 class="font-bold" style="color: #2c3e50;">{{ $record->pet->name }}</h3>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded" style="color: #5d6d7e; background-color: #f9fafb;">
                                {{ $record->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        <!-- Record Details -->
                        <div class="space-y-3">
                            <div class="p-3 rounded-lg border" style="background-color: #ebf5fb; border-color: #aed6f1;">
                                <p class="text-xs font-semibold mb-1 uppercase" style="color: #5d6d7e;">Doctor</p>
                                <p class="text-sm font-bold" style="color: #2c3e50;">
                                    <i class="fas fa-user-md mr-1" style="color: #0d5cb6;"></i>
                                    {{ $record->doctor->user->name ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="p-3 rounded-lg border" style="background-color: #fef9e7; border-color: #f9e79f;">
                                <p class="text-xs font-semibold mb-1 uppercase" style="color: #5d6d7e;">Diagnosis</p>
                                <p class="text-sm line-clamp-2 font-medium" style="color: #5d6d7e;">{{ $record->diagnosis }}</p>
                            </div>

                            <div class="p-3 rounded-lg border" style="background-color: #eafaf1; border-color: #a9dfbf;">
                                <p class="text-xs font-semibold mb-1 uppercase" style="color: #5d6d7e;">Treatment</p>
                                <p class="text-sm line-clamp-2 font-medium" style="color: #5d6d7e;">{{ $record->treatment }}</p>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-2 border-t-2" style="border-color: #f9fafb;">
                                <a href="{{ route('medical-records.show', $record->id) }}" 
                                   class="inline-flex items-center justify-center w-full px-4 py-2.5 text-white rounded-lg transition-all text-sm font-semibold shadow-md"
                                   style="background-color: #0d5cb6;">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto rounded-lg">
                <table class="min-w-full bg-white border-2 divide-y rounded-lg overflow-hidden" style="border-color: #e5e7eb;">
                    <thead style="background-color: #34495e;">
                        <tr>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Pet</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Doctor</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Diagnosis</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Treatment</th>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Date</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: #e5e7eb;">
                        @foreach($medicalRecords as $record)
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-bold" style="color: #2c3e50;">
                                    <i class="fas fa-paw mr-2" style="color: #0d5cb6;"></i>{{ $record->pet->name }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: #5d6d7e;">
                                    <i class="fas fa-user-md mr-1" style="color: #0d5cb6;"></i>{{ $record->doctor->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 text-sm" style="color: #5d6d7e;">
                                    <span class="line-clamp-2">{{ Str::limit($record->diagnosis, 60) }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 text-sm" style="color: #5d6d7e;">
                                    <span class="line-clamp-2">{{ Str::limit($record->treatment, 60) }}</span>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-semibold" style="color: #2c3e50;">
                                    <i class="fas fa-calendar mr-1" style="color: #0d5cb6;"></i>{{ $record->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('medical-records.show', $record->id) }}" 
                                       class="inline-flex items-center px-4 py-2 text-white rounded-lg transition-all text-sm font-semibold shadow-md" 
                                       style="background-color: #0d5cb6;"
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
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4" style="background-color: #ecf0f1;">
                    <i class="fas fa-file-medical text-4xl" style="color: #95a5a6;"></i>
                </div>
                <p class="text-base sm:text-lg font-medium" style="color: #5d6d7e;">No medical records found for your pets.</p>
                <p class="text-sm mt-2" style="color: #95a5a6;">Medical records will appear here after your appointments.</p>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

a:hover {
    opacity: 0.9;
    transform: scale(1.02);
}
</style>
@endsection