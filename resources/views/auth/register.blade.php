@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    style="background-image: url('{{ asset('images/BG.jpg') }}'); background-repeat: no-repeat; background-size: cover; background-position: center;">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-3xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join our Veterinary Clinic Management System
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                <!-- Basic Information -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror" 
                           placeholder="Your full name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror" 
                           placeholder="your@email.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Confirm password">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input id="phone" name="phone" type="tel" 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-500 @enderror" 
                           placeholder="+1234567890" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="3" 
                              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address') border-red-500 @enderror" 
                              placeholder="Your address">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Account Type</label>
                    <select id="role" name="role" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('role') border-red-500 @enderror">
                        <option value="">Select account type</option>
                        <option value="pet_owner" {{ old('role') == 'pet_owner' ? 'selected' : '' }}>Pet Owner</option>
                        <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor/Veterinarian</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pet Owner specific fields -->
                <div id="pet-owner-fields" class="hidden space-y-4">
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                        <input id="emergency_contact" name="emergency_contact" type="text" 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Emergency contact name" value="{{ old('emergency_contact') }}">
                    </div>
                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                        <input id="emergency_phone" name="emergency_phone" type="tel" 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="+1234567890" value="{{ old('emergency_phone') }}">
                    </div>
                </div>

                <!-- Doctor specific fields -->
                <div id="doctor-fields" class="hidden space-y-4">
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization</label>
                        <input id="specialization" name="specialization" type="text" 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="e.g., General Practice, Surgery" value="{{ old('specialization') }}">
                    </div>
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                        <input id="license_number" name="license_number" type="text" 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="VET001" value="{{ old('license_number') }}">
                    </div>
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                        <input id="experience_years" name="experience_years" type="number" min="0" 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="5" value="{{ old('experience_years') }}">
                    </div>
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea id="bio" name="bio" rows="3" 
                                  class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                  placeholder="Tell us about your experience...">{{ old('bio') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Create Account
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('role').addEventListener('change', function() {
    const petOwnerFields = document.getElementById('pet-owner-fields');
    const doctorFields = document.getElementById('doctor-fields');
    
    if (this.value === 'pet_owner') {
        petOwnerFields.classList.remove('hidden');
        doctorFields.classList.add('hidden');
    } else if (this.value === 'doctor') {
        doctorFields.classList.remove('hidden');
        petOwnerFields.classList.add('hidden');
    } else {
        petOwnerFields.classList.add('hidden');
        doctorFields.classList.add('hidden');
    }
});
</script>
@endsection
