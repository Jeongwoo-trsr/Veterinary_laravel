@extends('layouts.app')

@section('title', 'My Medical Records')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Pets' Medical Records</h1>

    <div class="bg-blue-50 shadow-lg rounded-lg p-6">
        @if($medicalRecords->count())
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($medicalRecords as $record)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $record->pet->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $record->doctor->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="line-clamp-2">{{ Str::limit($record->diagnosis, 60) }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="line-clamp-2">{{ Str::limit($record->treatment, 60) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
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
            
            <div class="mt-4">
                {{ $medicalRecords->links() }}
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No medical records found for your pets.</p>
        @endif
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