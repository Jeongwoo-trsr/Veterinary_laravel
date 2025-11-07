@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center" 
     style="background-image: url('{{ asset('images/petpro_login.png') }}'); background-repeat: no-repeat; background-size: cover; background-position: center;">
    
    <!-- Removed the dark overlay -->

    <div class="relative max-w-md w-full space-y-8 bg-white/90 backdrop-blur-sm p-8 rounded-xl shadow-lg">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center">
                <i class="fas fa-paw text-blue-600 text-3xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Login your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Veterinary Clinic Management System
            </p>
        </div>
        <form class="mt-8 space-y-4" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="rounded-md shadow-sm space-y-4 px-4">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                           placeholder="Email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sign in
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Register here
                    </a>
                </p>
            </div>

            <!-- Demo Accounts -->
            <div class="mt-6 p-4 bg-gray-100 rounded-lg">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Demo Accounts:</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Admin:</strong> admin@veterinary.com / password</p>
                    <p><strong>Doctor:</strong> sarah.johnson@veterinary.com / password</p>
                    <p><strong>Pet Owner:</strong> john.smith@email.com / password</p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection