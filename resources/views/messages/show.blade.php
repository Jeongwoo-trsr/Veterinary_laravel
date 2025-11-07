@extends('layouts.app')

@section('title', 'Message')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#1e3a5f] px-4 sm:px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-bold text-white">Message</h1>
            <a href="{{ route('messages.inbox') }}" class="text-white hover:text-gray-200 transition flex items-center gap-2 text-sm sm:text-base">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Back to Inbox</span>
            </a>
        </div>

        <!-- Message Content -->
        <div class="p-4 sm:p-6">
            <!-- From/To Info -->
            <div class="mb-6 pb-6 border-b border-gray-200">
                <div class="flex items-start gap-3 sm:gap-4">
                    <!-- Avatar -->
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#0066cc] flex items-center justify-center text-white font-bold text-lg sm:text-xl flex-shrink-0">
                        {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ $message->sender->name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $message->sender->email }}</p>
                            </div>
                            <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0">
                                {{ $message->created_at->format('M d, Y g:i A') }}
                            </span>
                        </div>
                        <div class="text-xs sm:text-sm text-gray-600">
                            <span class="font-medium">To:</span> {{ $message->receiver->name }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="mb-4 sm:mb-6">
                <div class="flex items-start gap-2">
                    <i class="fas fa-tag text-[#fcd34d] text-base sm:text-lg mt-1 flex-shrink-0"></i>
                    <h2 class="text-lg sm:text-2xl font-bold text-[#2d3748] break-words">{{ $message->subject }}</h2>
                </div>
            </div>

            <!-- Message Body -->
            <div class="prose max-w-none mb-6">
                <div class="text-sm sm:text-base text-gray-700 whitespace-pre-wrap break-words leading-relaxed">{{ $message->message }}</div>
            </div>

            <!-- Reply Button -->
            <div class="pt-6 border-t border-gray-200">
                <a href="{{ route('messages.create') }}?receiver_id={{ $message->sender_id }}&subject=Re: {{ $message->subject }}" 
                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 sm:py-2.5 bg-[#0066cc] text-white rounded-lg hover:bg-[#003d82] transition font-semibold text-sm gap-2">
                    <i class="fas fa-reply text-[#fcd34d]"></i>
                    <span>Reply</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection