@extends('layouts.app')

@section('title', 'New Message')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#1e3a5f] px-4 sm:px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-bold text-white">New Message</h1>
            <a href="{{ route('messages.inbox') }}" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-xl sm:text-2xl"></i>
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('messages.store') }}" class="p-4 sm:p-6">
            @csrf

            <!-- To -->
            <div class="mb-4 sm:mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                <select name="receiver_id" required 
                        class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm @error('receiver_id') border-red-500 @enderror">
                    <option value="">Select recipient...</option>
                    @foreach($users as $user)
                        @if(auth()->user()->role === 'pet_owner')
                            @if(in_array($user->role, ['admin', 'doctor']))
                                <option value="{{ $user->id }}" 
                                    {{ (old('receiver_id') == $user->id || request('receiver_id') == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endif
                        @else
                            <option value="{{ $user->id }}" 
                                {{ (old('receiver_id') == $user->id || request('receiver_id') == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('receiver_id')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="mb-4 sm:mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject', request('subject')) }}" required
                       placeholder="Enter subject..."
                       class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm @error('subject') border-red-500 @enderror">
                @error('subject')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="8" required
                          placeholder="Type your message here..."
                          class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] text-sm resize-y @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('messages.inbox') }}" 
                   class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition font-semibold text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection