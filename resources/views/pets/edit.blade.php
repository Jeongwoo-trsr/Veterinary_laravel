@extends('layouts.app')

@section('title', 'Edit Pet')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[#2d3748]">
            <i class="fas fa-edit text-[#fcd34d] mr-2"></i>Edit Pet: {{ $pet->name }}
        </h1>
        <a href="{{ route('admin.pets') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#1e3a5f] px-6 py-6">
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-paw text-[#fcd34d] mr-2"></i>Update Pet Information
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

            <form action="{{ route('pets.update', $pet->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-[#d4931d] mr-1"></i>Pet Owner *
                    </label>
                    <select name="owner_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                        @foreach(\App\Models\PetOwner::with('user')->get() as $owner)
                            <option value="{{ $owner->id }}" {{ $pet->owner_id == $owner->id ? 'selected' : '' }}>
                                {{ $owner->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-paw text-[#fcd34d] mr-1"></i>Pet Name *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $pet->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list text-[#d4931d] mr-1"></i>Species *
                        </label>
                        <input type="text" name="species" value="{{ old('species', $pet->species) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dog text-[#fcd34d] mr-1"></i>Breed
                        </label>
                        <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-birthday-cake text-[#d4931d] mr-1"></i>Age *
                        </label>
                        <input type="number" name="age" value="{{ old('age', $pet->age) }}" required min="0" max="30" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-weight text-[#fcd34d] mr-1"></i>Weight (kg)
                        </label>
                        <input type="number" name="weight" value="{{ old('weight', $pet->weight) }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-palette text-[#d4931d] mr-1"></i>Color
                        </label>
                        <input type="text" name="color" value="{{ old('color', $pet->color) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars text-[#fcd34d] mr-1"></i>Gender *
                        </label>
                        <select name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">
                            <option value="male" {{ $pet->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $pet->gender == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                  
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-notes-medical text-[#d4931d] mr-1"></i>Medical Notes
                    </label>
                    <textarea name="medical_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]">{{ old('medical_notes', $pet->medical_notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.pets') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] font-medium">
                        <i class="fas fa-save mr-2 text-[#fcd34d]"></i>Update Pet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection