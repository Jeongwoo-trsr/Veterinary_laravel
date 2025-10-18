@extends('layouts.app')

@section('title', 'My Pets')

@section('content')
    <h1 class="text-2xl font-bold mb-4">My Pets</h1>

    <div class="bg-blue-100 shadow-lg rounded-lg p-6">
        @if($pets->count())
            <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="bg-blue-500 px-6 py-3">Name</th>
                        <th class="bg-blue-500 px-6 py-3">Species</th>
                        <th class="bg-blue-500 px-6 py-3">Breed</th>
                        <th class="bg-blue-500 px-6 py-3">Age</th>
                        <th class="bg-blue-500 px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pets as $pet)
                        <tr>
                            <td class="px-6 py-4">{{ $pet->name }}</td>
                            <td class="px-6 py-4">{{ $pet->species }}</td>
                            <td class="px-6 py-4">{{ $pet->breed ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $pet->age ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pet-owner.pets.show', $pet->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700" title="View">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">
                {{ $pets->links() }}
            </div>
        @else
            <p class="text-gray-500">You don't have any pets yet.</p>
        @endif
    </div>
@endsection