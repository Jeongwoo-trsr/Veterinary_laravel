@extends('layouts.app')

@section('title', 'Pet Owner Details')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.pet-owners') }}" class="text-[#0066cc] hover:text-[#003d82]">
        <i class="fas fa-arrow-left mr-2"></i>Back to Pet Owners
    </a>
</div>

<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <!-- Header with solid color -->
    <div class="bg-[#1e3a5f] p-8">
        <div>
            <h2 class="text-4xl font-bold mb-2 text-white">{{ $petOwner->user->name }}</h2>
            <p class="text-gray-200 text-lg">{{ $petOwner->user->email }}</p>
        </div>
    </div>

    <!-- Owner Information -->
    <div class="p-6">
        <h3 class="text-xl font-bold mb-4 text-[#2d3748]">Owner Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->user->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->user->email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->user->phone ?? 'Not provided' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Emergency Phone</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->emergency_phone ?? 'Not provided' }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->user->address ?? 'Not provided' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Emergency Contact Name</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->emergency_contact ?? 'Not provided' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Registration Date</label>
                <p class="text-gray-900 font-semibold">{{ $petOwner->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Pets Section -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-[#2d3748]">Pets ({{ $petOwner->pets->count() }})</h3>
                <a href="{{ route('pets.create', ['owner_id' => $petOwner->id]) }}" class="px-4 py-2 bg-[#0066cc] text-white rounded hover:bg-[#003d82]">
                    <i class="fas fa-plus mr-2"></i>Add Pet
                </a>
            </div>

            @if($petOwner->pets->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($petOwner->pets as $pet)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="font-bold text-lg text-gray-900">{{ $pet->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $pet->species }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded {{ $pet->breed ? 'bg-[#fcd34d] text-[#2d3748]' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $pet->breed ?? 'Mixed' }}
                                </span>
                            </div>
                            
                            <div class="mt-3 space-y-1 text-sm">
                                <p class="text-gray-700"><span class="font-medium">Age:</span> {{ $pet->age ?? 'Unknown' }}</p>
                                <p class="text-gray-700"><span class="font-medium">Color:</span> {{ $pet->color ?? 'Not specified' }}</p>
                                <p class="text-gray-700"><span class="font-medium">Weight:</span> {{ $pet->weight ?? 'Not specified' }}</p>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('pets.show', $pet->id) }}" class="flex-1 px-3 py-1 bg-[#0066cc] text-white text-center rounded hover:bg-[#003d82] text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('pets.edit', $pet->id) }}" class="flex-1 px-3 py-1 bg-[#d4931d] text-white text-center rounded hover:bg-[#fcd34d] hover:text-[#2d3748] text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <i class="fas fa-paw text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500">No pets registered for this owner yet.</p>
                    <a href="{{ route('pets.create', ['owner_id' => $petOwner->id]) }}" class="inline-block mt-3 text-[#0066cc] hover:text-[#003d82]">
                        Add their first pet
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection