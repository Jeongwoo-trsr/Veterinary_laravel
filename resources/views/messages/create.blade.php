@extends('layouts.app')

@section('title', 'New Message')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">New Message</h1>
            <a href="{{ route('messages.inbox') }}" class="text-white hover:text-blue-100">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('messages.store') }}" class="p-6">
            @csrf

            <!-- To -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">To</label>
                <select name="receiver_id" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('receiver_id') border-red-500 @enderror">
                    <option value="">Select recipient...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" 
                            {{ (old('receiver_id') == $user->id || request('receiver_id') == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
                @error('receiver_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject', request('subject')) }}" required
                       placeholder="Enter subject..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                @error('subject')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                <textarea name="message" rows="10" required
                          placeholder="Type your message here..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('messages.inbox') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    <i class="fas fa-paper-plane mr-2"></i>Send
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
