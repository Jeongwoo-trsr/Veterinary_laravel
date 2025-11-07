@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="container mx-auto px-4 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('announcements.index') }}" class="text-[#0066cc] hover:text-[#003d82]">
            ← Back to Announcements
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-[#2d3748]">Edit Announcement</h1>

        <form method="POST" action="{{ route('announcements.update', $announcement) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $announcement->title) }}" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] @error('title') border-red-500 @enderror"
                >
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea 
                    name="content" 
                    id="content" 
                    rows="8" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc] @error('content') border-red-500 @enderror"
                >{{ old('content', $announcement->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('announcements.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82]">
                    Update Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection