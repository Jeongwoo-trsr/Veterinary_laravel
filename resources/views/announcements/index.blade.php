@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mx-auto px-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#2d3748]">Announcements</h1>
            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                <a href="{{ route('announcements.create') }}" class="bg-[#0066cc] text-white px-4 py-2 rounded-lg hover:bg-[#003d82]">
                    <i class="fas fa-plus mr-2"></i>Create Announcement
                </a>
            @endif
        </div>

        <!-- Search Bar -->
        <div style="margin-bottom: 3rem;">
            <form method="GET" action="{{ route('announcements.index') }}" class="flex gap-4">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search announcements..." 
                    value="{{ request('search') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0066cc]"
                >
                <button type="submit" class="bg-[#0066cc] text-white px-6 py-2 rounded-lg hover:bg-[#003d82]">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('announcements.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Announcements List -->
        <div class="space-y-4">
            @forelse($announcements as $announcement)
                <div class="bg-white border-l-4 border-[#0066cc] shadow rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-[#2d3748]">{{ $announcement->title }}</h3>
                        <span class="text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <p class="text-gray-700 mb-3">{{ Str::limit($announcement->content, 200) }}</p>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i>
                            Posted by {{ $announcement->creator->name }}
                        </div>
                        
                        @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                            <div class="flex items-center gap-4">
                                <a href="{{ route('announcements.edit', $announcement) }}" class="text-[#d4931d] hover:text-[#fcd34d] flex items-center gap-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 flex items-center gap-1">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-bullhorn text-4xl mb-4"></i>
                    <p>No announcements found.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    </div>
</div>
@endsection