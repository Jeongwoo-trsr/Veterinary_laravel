@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Pets</h1>

    {{-- Search bar --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('pets.index') }}" class="flex space-x-2">
            <input 
                type="text" 
                name="search" 
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Search pets by name, species or breed..." 
                value="{{ $search  }}"
            >
            <button 
                type="submit" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Species</th>
                    <th class="px-4 py-2">Breed</th>
                    <th class="px-4 py-2">Owner</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pets as $pet)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $pet->name }}</td>
                        <td class="px-4 py-2">{{ $pet->species }}</td>
                        <td class="px-4 py-2">{{ $pet->breed }}</td>
                        <td class="px-4 py-2">
                            {{ $pet->owner->user->name ?? 'No Owner' }}
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('pets.show', $pet->id) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('pets.edit', $pet->id) }}" class="ml-2 text-yellow-600 hover:underline">Edit</a>
                            <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:underline" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            No pets found{{ !empty($search) ? " for \"$search\"" : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $pets->links() }}
    </div>
</div>
@endsection
