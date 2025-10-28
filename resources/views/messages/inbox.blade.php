@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white">Messages</h1>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">You have {{ $unreadCount }} unread message(s)</p>
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
                <a href="{{ route('messages.inbox') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap flex items-center">
                    <i class="fas fa-inbox mr-2"></i>
                    <span>Inbox</span>
                    @if($unreadCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 sm:py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('messages.sent') }}" class="px-3 sm:px-4 py-3 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 whitespace-nowrap flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    <span>Sent Mail</span>
                </a>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-white">
            <!-- Desktop Actions -->
            <div class="hidden md:flex items-center justify-between">
                <div class="flex items-center gap-2 lg:gap-4">
                    <button type="button" onclick="markAsRead()" class="text-xs sm:text-sm text-gray-700 hover:bg-gray-100 px-2 sm:px-3 py-2 rounded flex items-center gap-1 sm:gap-2">
                        <i class="fas fa-check"></i> 
                        <span>Mark as read</span>
                    </button>
                    <button type="button" onclick="markAsUnread()" class="text-xs sm:text-sm text-gray-700 hover:bg-gray-100 px-2 sm:px-3 py-2 rounded flex items-center gap-1 sm:gap-2">
                        <i class="fas fa-times"></i> 
                        <span>Mark as unread</span>
                    </button>
                    <button type="button" onclick="deleteMessages()" class="text-xs sm:text-sm text-red-600 hover:bg-red-50 px-2 sm:px-3 py-2 rounded flex items-center gap-1 sm:gap-2">
                        <i class="fas fa-trash"></i> 
                        <span>Delete</span>
                    </button>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('messages.inbox') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search messages..." 
                           class="px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-40 lg:w-auto">
                    <button type="submit" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Mobile Actions -->
            <div class="md:hidden space-y-3">
                <!-- Search Bar -->
                <form method="GET" action="{{ route('messages.inbox') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search messages..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button type="button" onclick="markAsRead()" class="flex-shrink-0 text-xs text-gray-700 hover:bg-gray-100 px-3 py-2 rounded flex items-center gap-1 border border-gray-300">
                        <i class="fas fa-check"></i> 
                        <span>Read</span>
                    </button>
                    <button type="button" onclick="markAsUnread()" class="flex-shrink-0 text-xs text-gray-700 hover:bg-gray-100 px-3 py-2 rounded flex items-center gap-1 border border-gray-300">
                        <i class="fas fa-times"></i> 
                        <span>Unread</span>
                    </button>
                    <button type="button" onclick="deleteMessages()" class="flex-shrink-0 text-xs text-red-600 hover:bg-red-50 px-3 py-2 rounded flex items-center gap-1 border border-red-300">
                        <i class="fas fa-trash"></i> 
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <form id="messagesForm">
            @csrf
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" 
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 message-checkbox"
                                       onclick="toggleSelectAll(this)">
                            </th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Read?</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($messages as $message)
                            <tr class="hover:bg-gray-50 cursor-pointer transition {{ !$message->is_read ? 'bg-blue-50' : '' }}" 
                                onclick="window.location='{{ route('messages.show', $message) }}'">
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                    <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" 
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 message-checkbox">
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm {{ !$message->is_read ? 'font-bold text-gray-900' : 'font-medium text-gray-700' }}">
                                            {{ $message->sender->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 lg:px-6 py-4">
                                    <div class="text-sm {{ !$message->is_read ? 'font-semibold text-blue-600' : 'text-gray-900' }}">
                                        {{ $message->subject }}
                                    </div>
                                    <div class="text-sm text-gray-500 truncate max-w-md">
                                        {{ Str::limit($message->message, 80) }}
                                    </div>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $message->created_at->format('M d') }}</div>
                                    <div class="text-xs">{{ $message->created_at->format('g:i a') }}</div>
                                </td>
                                <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-center">
                                    @if($message->is_read)
                                        <i class="fas fa-check text-green-500"></i>
                                    @else
                                        <div class="inline-block w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl sm:text-6xl mb-4"></i>
                                    <p class="text-gray-500 text-base sm:text-lg">No messages found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($messages as $message)
                    <div class="p-4 {{ !$message->is_read ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <!-- Checkbox -->
                            <div class="pt-1" onclick="event.stopPropagation()">
                                <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" 
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 message-checkbox">
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0" onclick="window.location='{{ route('messages.show', $message) }}'">
                                <!-- Header: Avatar, Name, Date -->
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                            {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm {{ !$message->is_read ? 'font-bold text-gray-900' : 'font-medium text-gray-700' }} truncate">
                                                {{ $message->sender->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $message->created_at->format('M d, g:i a') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Read Status -->
                                    <div class="flex-shrink-0 pt-1">
                                        @if($message->is_read)
                                            <i class="fas fa-check text-green-500 text-sm"></i>
                                        @else
                                            <div class="w-2.5 h-2.5 bg-blue-600 rounded-full"></div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Subject -->
                                <h3 class="text-sm {{ !$message->is_read ? 'font-semibold text-blue-600' : 'font-medium text-gray-900' }} mb-1 line-clamp-1">
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
                    <div class="p-8 text-center">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-500 text-base">No messages found</p>
                    </div>
                @endforelse
            </div>
        </form>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.message-checkbox {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 16px;
    height: 16px;
    border: 2px solid #d1d5db;
    border-radius: 3px;
    cursor: pointer;
    position: relative;
    background-color: white;
    flex-shrink: 0;
}

.message-checkbox:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}

.message-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2.5px 2.5px 0;
    transform: translate(-50%, -60%) rotate(45deg);
}

.message-checkbox:hover {
    border-color: #2563eb;
}

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

<script>
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.message-checkbox:not(#selectAll)');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.message-checkbox:checked:not(#selectAll)'))
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
