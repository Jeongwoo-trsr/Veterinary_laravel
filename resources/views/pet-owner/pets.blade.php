@extends('layouts.app')

@section('title', 'My Pets')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#2d3748]">
            <i class="fas fa-paw text-[#fcd34d] mr-2"></i>My Pets
        </h1>
        <a href="{{ route('pet-owner.pets.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition-colors shadow-md">
            <i class="fas fa-plus-circle mr-2 text-[#fcd34d]"></i>
            Register New Pet
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($pets->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-paw text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Pets Yet</h3>
            <p class="text-gray-500 mb-6">Register your first pet to start booking appointments!</p>
            <a href="{{ route('pet-owner.pets.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition-colors">
                <i class="fas fa-plus-circle mr-2"></i>
                Register Your First Pet
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pets as $pet)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Pet Header -->
                    <div class="bg-gradient-to-r from-[#1e3a5f] to-[#0066cc] p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-[#fcd34d] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-paw text-[#1e3a5f] text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ $pet->name }}</h3>
                                    <p class="text-sm text-gray-200">{{ ucfirst($pet->species) }}</p>
                                </div>
                            </div>
                            
                            <!-- Approval Status Badge -->
                            @if($pet->approval_status === 'pending')
                                <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-semibold">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @elseif($pet->approval_status === 'approved')
                                <span class="px-3 py-1 bg-green-400 text-green-900 rounded-full text-xs font-semibold">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @elseif($pet->approval_status === 'rejected')
                                <span class="px-3 py-1 bg-red-400 text-red-900 rounded-full text-xs font-semibold">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Pet Details -->
                    <div class="p-4">
                        <div class="space-y-2 text-sm">
                            @if($pet->breed)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-dog w-5 text-[#d4931d]"></i>
                                    <span class="ml-2">{{ $pet->breed }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-birthday-cake w-5 text-[#d4931d]"></i>
                                <span class="ml-2">{{ $pet->age }} {{ $pet->age == 1 ? 'year' : 'years' }} old</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-venus-mars w-5 text-[#d4931d]"></i>
                                <span class="ml-2">{{ ucfirst($pet->gender) }}</span>
                            </div>
                            @if($pet->weight)
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-weight w-5 text-[#d4931d]"></i>
                                    <span class="ml-2">{{ $pet->weight }} kg</span>
                                </div>
                            @endif
                        </div>

                        <!-- Rejection Reason -->
                        @if($pet->approval_status === 'rejected' && $pet->rejection_reason)
                            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-xs font-semibold text-red-800 mb-1">
                                    <i class="fas fa-info-circle"></i> Rejection Reason:
                                </p>
                                <p class="text-xs text-red-700">{{ $pet->rejection_reason }}</p>
                            </div>
                        @endif

                        <!-- Pending Message -->
                        @if($pet->approval_status === 'pending')
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-xs text-yellow-800">
                                    <i class="fas fa-hourglass-half"></i> Your pet registration is being reviewed by our admin team.
                                </p>
                            </div>
                        @endif

                        <!-- Statistics -->
                        <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-[#0066cc]">{{ $pet->appointments->count() }}</p>
                                <p class="text-xs text-gray-500">Appointments</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-[#0066cc]">{{ $pet->medicalRecords->count() }}</p>
                                <p class="text-xs text-gray-500">Records</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('pet-owner.pets.show', $pet->id) }}" 
                               class="flex-1 text-center px-3 py-2 bg-[#0066cc] text-white text-sm rounded-lg hover:bg-[#003d82] transition-colors">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </a>
                            
                            @if($pet->approval_status !== 'approved')
                                <form action="{{ route('pet-owner.pets.destroy', $pet->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this pet?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $pets->links() }}
        </div>
    @endif
</div>
@endsection