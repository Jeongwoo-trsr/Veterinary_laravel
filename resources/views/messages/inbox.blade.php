@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Messages</h1>
                    <p class="text-blue-100 text-sm">You have {{ $unreadCount }} unread message(s)</p>
                </div>
                <a href="{{ route('messages.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> New Message
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 bg-gray-50">
            <div class="flex px-6">
                <a href="{{ route('messages.inbox') }}" class="px-4 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                    <i class="fas fa-inbox mr-2"></i>Inbox
                    @if($unreadCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('messages.sent') }}" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-paper-plane mr-2"></i>Sent Mail
                </a>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex items-center gap-4">
            <button type="button" onclick="markAsRead()" class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded flex items-center gap-2">
                <i class="fas fa-check"></i> Mark as read
            </button>
            <button type="button" onclick="markAsUnread()" class="text-sm text-gray-700 hover:bg-gray-100 px-3 py-2 rounded flex items-center gap-2">
                <i class="fas fa-times"></i> Mark as unread
            </button>
            <button type="button" onclick="deleteMessages()" class="text-sm text-red-600 hover:bg-red-50 px-3 py-2 rounded flex items-center gap-2">
                <i class="fas fa-trash"></i> Delete
            </button>

            <!-- Search -->
            <form method="GET" action="{{ route('messages.inbox') }}" class="ml-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search messages..." 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Messages List -->
        <form id="messagesForm">
            @csrf
            <div class="divide-y divide-gray-200">
                @forelse($messages as $message)
                    <div class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition {{ !$message->is_read ? 'bg-blue-50' : '' }}" 
                         onclick="window.location='{{ route('messages.show', $message) }}'">
                        <div class="flex items-center gap-4">
                            <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" 
                                   class="w-4 h-4 text-blue-600 message-checkbox" 
                                   onclick="event.stopPropagation()">
                            
                            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">
                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-gray-900 {{ !$message->is_read ? 'font-bold' : '' }}">
                                            {{ $message->sender->name }}
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

                            @if(!$message->is_read)
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            @else
                                <i class="fas fa-check text-green-500"></i>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No messages found</p>
                    </div>
                @endforelse
            </div>
        </form>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function getSelectedIds() {
    return Array.from(document.querySelectorAll('.message-checkbox:checked'))
                .map(cb => cb.value);
}

function markAsRead() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one message');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("messages.mark-read") }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'message_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function markAsUnread() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one message');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("messages.mark-unread") }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'message_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function deleteMessages() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select at least one message');
        return;
    }
    
    if (!confirm('Are you sure you want to delete the selected message(s)?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("messages.destroy") }}';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'message_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
