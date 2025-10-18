@extends('layouts.app')

@section('title', 'Edit Pet Owner')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Pet Owner: {{ $petOwner->user->name }}</h1>
        <a href="{{ route('admin.pet-owners') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #4663e4ff 0%, #fcff54ff 100%); padding: 24px; color: white;">
            <h2 class="text-2xl font-bold">
                <i class="fas fa-edit mr-2"></i>Update Pet Owner Information
            </h2>
        </div>

        <!-- Form -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Personal Information</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $petOwner->user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $petOwner->user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $petOwner->user->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('address', $petOwner->user->address) }}</textarea>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">Emergency Contact</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $petOwner->emergency_contact) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Phone</label>
                            <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $petOwner->emergency_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.pet-owners') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 text-white rounded-lg font-medium" style="background: linear-gradient(135deg,  #233581ff 0%);">
                        <i class="fas fa-save mr-2"></i>Update Pet Owner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection