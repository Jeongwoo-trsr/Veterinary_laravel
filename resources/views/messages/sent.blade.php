@extends('layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white">Sent Messages</h1>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">Messages you have sent</p>
                </div>
                <a href="{{ route('messages.create') }}" class="inline-flex items-center justify-center bg-white text-blue-600 px-4 py-2.5 sm:py-2 rounded-lg font-semibold hover:bg-blue-50 transition gap-2 text-sm">
                    <i class="fas fa-plus"></i> 
                    <span>New Message</span>
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="flex px-3 sm:px-6 overflow-x-auto">
                <a href="{{ route('messages.inbox') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 whitespace-nowrap flex items-center">
                    <i class="fas fa-inbox mr-2"></i>
                    <span>Inbox</span>
                </a>
                <a href="{{ route('messages.sent') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    <span>Sent Mail</span>
                </a>
            </div>
        </div>

        <!-- Messages List -->
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
                <!-- Desktop View -->
                <div class="hidden sm:block px-4 sm:px-6 py-4 hover:bg-gray-50 cursor-pointer transition" 
                     onclick="window.location='{{ route('messages.show', $message) }}'">
                    <div class="flex items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <span class="font-semibold text-gray-900 text-sm sm:text-base">
                                        To: {{ $message->receiver->name }}
                                    </span>
                                    <span class="text-xs sm:text-sm text-gray-600 truncate">{{ $message->subject }}</span>
                                </div>
                                <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0 ml-2">
                                    {{ $message->created_at->format('M d, g:i a') }}
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">
                                {{ Str::limit($message->message, 100) }}
                            </p>
                        </div>

                        <div class="flex-shrink-0">
                            @if($message->is_read)
                                <i class="fas fa-check-double text-blue-500 text-sm sm:text-base" title="Read"></i>
                            @else
                                <i class="fas fa-check text-gray-400 text-sm sm:text-base" title="Sent"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mobile View -->
                <div class="block sm:hidden p-4 hover:bg-gray-50 cursor-pointer transition" 
                     onclick="window.location='{{ route('messages.show', $message) }}'">
                    <div class="flex items-start gap-3">
                        <!-- Avatar -->
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                            {{ strtoupper(substr($message->receiver->name, 0, 1)) }}
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Header: To, Date, Status -->
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-gray-900 truncate">
                                        To: {{ $message->receiver->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $message->created_at->format('M d, g:i a') }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 pt-0.5">
                                    @if($message->is_read)
                                        <i class="fas fa-check-double text-blue-500 text-sm" title="Read"></i>
                                    @else
                                        <i class="fas fa-check text-gray-400 text-sm" title="Sent"></i>
                                    @endif
                                </div>
                            </div>

                            <!-- Subject -->
                            <h3 class="text-sm font-medium text-gray-900 mb-1 line-clamp-1">
                                {{ $message->subject }}
                            </h3>

                            <!-- Message Preview -->
                            <p class="text-xs text-gray-600 line-clamp-2">
                                {{ Str::limit($message->message, 100) }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 sm:px-6 py-12 text-center">
                    <i class="fas fa-paper-plane text-gray-300 text-5xl sm:text-6xl mb-4"></i>
                    <p class="text-gray-500 text-base sm:text-lg">No sent messages</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
