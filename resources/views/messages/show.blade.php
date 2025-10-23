@extends('layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Sent Messages</h1>
                    <p class="text-blue-100 text-sm">Messages you have sent</p>
                </div>
                <a href="{{ route('messages.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> New Message
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="flex px-6">
                <a href="{{ route('messages.inbox') }}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-inbox mr-2"></i>Inbox
                </a>
                <a href="{{ route('messages.sent') }}" class="px-4 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                    <i class="fas fa-paper-plane mr-2"></i>Sent Mail
                </a>
            </div>
        </div>

        <!-- Messages List -->
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
                <div class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition" 
                     onclick="window.location='{{ route('messages.show', $message) }}'">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-600 text-white flex items-center justify-center font-semibold">
                            {{ strtoupper(substr($message->receiver->name, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-gray-900">
                                        To: {{ $message->receiver->name }}
                                    </span>
                                    <span class="text-sm text-gray-600">{{ $message->subject }}</span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $message->created_at->format('M d, g:i a') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">
                                {{ Str::limit($message->message, 100) }}
                            </p>
                        </div>

                        @if($message->is_read)
                            <i class="fas fa-check-double text-blue-500" title="Read"></i>
                        @else
                            <i class="fas fa-check text-gray-400" title="Sent"></i>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-paper-plane text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No sent messages</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
