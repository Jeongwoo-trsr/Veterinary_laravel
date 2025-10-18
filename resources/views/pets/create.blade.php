@extends('layouts.app')

@section('title', 'Add Pet')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Pet</h1>

            <form method="POST" action="{{ route('pets.store') }}" class="space-y-6">
                @csrf

                <!-- Pet Owner -->
                @if(Auth::user()->isAdmin())
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-gray-700">Pet Owner</label>
                    <select id="owner_id" name="owner_id" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('owner_id') border-red-500 @enderror">
                        <option value="">Select pet owner</option>
                        @foreach($petOwners as $owner)
                        <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->user->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <input type="hidden" name="owner_id" value="{{ Auth::user()->petOwner->id }}">
                @endif

                <!-- Pet Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror" 
                           placeholder="Enter pet name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Species and Breed -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                        <select id="species" name="species" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('species') border-red-500 @enderror">
                            <option value="">Select species</option>
                            <option value="dog" {{ old('species') == 'dog' ? 'selected' : '' }}>Dog</option>
                            <option value="cat" {{ old('species') == 'cat' ? 'selected' : '' }}>Cat</option>
                            <option value="bird" {{ old('species') == 'bird' ? 'selected' : '' }}>Bird</option>
                            <option value="rabbit" {{ old('species') == 'rabbit' ? 'selected' : '' }}>Rabbit</option>
                            <option value="hamster" {{ old('species') == 'hamster' ? 'selected' : '' }}>Hamster</option>
                            <option value="fish" {{ old('species') == 'fish' ? 'selected' : '' }}>Fish</option>
                            <option value="other" {{ old('species') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('species')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                        <input id="breed" name="breed" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('breed') border-red-500 @enderror" 
                               placeholder="Enter breed" value="{{ old('breed') }}">
                        @error('breed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Age and Weight -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">Age (years)</label>
                        <input id="age" name="age" type="number" min="0" max="30" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('age') border-red-500 @enderror" 
                               placeholder="0" value="{{ old('age') }}">
                        @error('age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input id="weight" name="weight" type="number" step="0.1" min="0" max="1000" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('weight') border-red-500 @enderror" 
                               placeholder="0.0" value="{{ old('weight') }}">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Color and Gender -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                        <input id="color" name="color" type="text" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('color') border-red-500 @enderror" 
                               placeholder="Enter color" value="{{ old('color') }}">
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                        <select id="gender" name="gender" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('gender') border-red-500 @enderror">
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <!-- Medical Notes -->
                <div>
                    <label for="medical_notes" class="block text-sm font-medium text-gray-700">Medical Notes</label>
                    <textarea id="medical_notes" name="medical_notes" rows="3" 
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('medical_notes') border-red-500 @enderror" 
                              placeholder="Enter any medical notes or special requirements">{{ old('medical_notes') }}</textarea>
                    @error('medical_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('pets.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Add Pet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
