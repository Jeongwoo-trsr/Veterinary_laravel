@extends('layouts.app')

@section('title', 'Edit Pet Owner')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#1e3a5f]">Edit Pet Owner: {{ $petOwner->user->name }}</h1>
        <a href="{{ route('admin.pet-owners') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden border-2 border-gray-200">
        <!-- Header -->
        <div class="bg-[#1e3a5f] p-6">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2 text-[#fcd34d]"></i>Update Pet Owner Information
            </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.pet-owners.update', $petOwner->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-[#1e3a5f] border-b-2 border-gray-200 pb-2">
                        <i class="fas fa-user mr-2 text-[#0066cc]"></i>Personal Information
                    </h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-[#d4931d]"></i>Full Name *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $petOwner->user->name) }}" required class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-[#d4931d]"></i>Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $petOwner->user->email) }}" required class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-[#d4931d]"></i>Phone
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $petOwner->user->phone) }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2 text-[#d4931d]"></i>Address
                        </label>
                        <textarea name="address" rows="3" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">{{ old('address', $petOwner->user->address) }}</textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-[#1e3a5f] border-b-2 border-gray-200 pb-2">
                        <i class="fas fa-exclamation-triangle mr-2 text-[#0066cc]"></i>Emergency Contact
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-friends mr-2 text-[#d4931d]"></i>Emergency Contact Name
                            </label>
                            <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $petOwner->emergency_contact) }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone-alt mr-2 text-[#d4931d]"></i>Emergency Phone
                            </label>
                            <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $petOwner->emergency_phone) }}" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] focus:border-[#0066cc] transition-all">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200">
                    <a href="{{ route('admin.pet-owners') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium transition-all shadow-md">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] font-medium transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2 text-[#fcd34d]"></i>Update Pet Owner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection