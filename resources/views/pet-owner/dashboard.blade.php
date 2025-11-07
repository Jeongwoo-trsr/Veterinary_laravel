@extends('layouts.app')

@section('title', 'Pet Owner Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="bg-[#1e3a5f] shadow-lg rounded-lg p-4 sm:p-6">
        <h1 class="text-xl sm:text-2xl font-bold text-white">
            <i class="fas fa-home text-[#fcd34d] mr-2"></i>Pet Owner Dashboard
        </h1>
        <p class="text-sm sm:text-base text-gray-200 mt-1">Welcome, {{ Auth::user()->name }}</p>
    </div>

    <!-- Top Section: Statistics Cards + Announcements -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Left: Statistics Cards in Responsive Grid -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6">
                <!-- My Pets Card -->
                <a href="{{ route('pet-owner.pets') }}" class="bg-[#0066cc] overflow-hidden shadow-lg rounded-lg hover:shadow-xl hover:bg-[#003d82] transition-all duration-200 cursor-pointer">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-paw text-[#fcd34d] text-xl sm:text-2xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-5 flex-1 min-w-0">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-200 truncate">My Pets</dt>
                                    <dd class="text-base sm:text-lg font-bold text-white">{{ $stats['total_pets'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Upcoming Appointments Card -->
                <a href="{{ route('pet-owner.appointments', ['status' => 'scheduled']) }}" class="bg-[#d4931d] overflow-hidden shadow-lg rounded-lg hover:shadow-xl hover:bg-[#fcd34d] transition-all duration-200 cursor-pointer">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-white text-xl sm:text-2xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-5 flex-1 min-w-0">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-white truncate">Upcoming Appointments</dt>
                                    <dd class="text-base sm:text-lg font-bold text-white">{{ $stats['upcoming_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Medical Records Card -->
                <a href="{{ route('pet-owner.medical-records') }}" class="bg-[#fcd34d] overflow-hidden shadow-lg rounded-lg hover:shadow-xl hover:bg-[#d4931d] transition-all duration-200 cursor-pointer">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-medical text-[#2d3748] text-xl sm:text-2xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-5 flex-1 min-w-0">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-[#2d3748] truncate">Medical Records</dt>
                                    <dd class="text-base sm:text-lg font-bold text-[#2d3748]">{{ $stats['total_medical_records'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Total Appointments Card -->
                <a href="{{ route('pet-owner.appointments') }}" class="bg-[#2d3748] overflow-hidden shadow-lg rounded-lg hover:shadow-xl hover:bg-[#1e3a5f] transition-all duration-200 cursor-pointer">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-alt text-[#fcd34d] text-xl sm:text-2xl"></i>
                            </div>
                            <div class="ml-3 sm:ml-5 flex-1 min-w-0">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-200 truncate">Total Appointments</dt>
                                    <dd class="text-base sm:text-lg font-bold text-white">{{ $stats['total_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Right: Announcements Container -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg flex flex-col h-full border border-gray-200">
                <div class="px-4 py-3 bg-[#1e3a5f] rounded-t-lg">
                    <h3 class="text-sm sm:text-base font-bold text-white">
                        <i class="fas fa-bullhorn text-[#fcd34d] mr-2"></i>Announcements
                    </h3>
                </div>
                
                <div class="px-3 sm:px-4 py-3 space-y-2 overflow-y-auto flex-1" style="max-height: 400px;">
                    @forelse($announcements as $announcement)
                        <button 
                            type="button"
                            onclick="showAnnouncement('{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->content) }}', '{{ addslashes($announcement->creator->name) }}', '{{ $announcement->created_at->format('F d, Y') }}')"
                            class="text-left w-full bg-gray-50 hover:bg-[#0066cc] hover:text-white px-2.5 sm:px-3 py-2 sm:py-2.5 rounded border border-gray-200 hover:border-[#0066cc] transition-all duration-150 block group">
                            <h4 class="font-medium text-gray-900 group-hover:text-white text-xs sm:text-sm break-words">
                                {{ $announcement->title }}
                            </h4>
                        </button>
                    @empty
                        <div class="text-center py-6 sm:py-8 text-gray-500">
                            <i class="fas fa-bullhorn text-2xl sm:text-3xl mb-2 text-gray-300"></i>
                            <p class="text-xs">No announcements yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments Section (Full Width Below) -->
    <div class="bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="px-3 sm:px-4 py-4 sm:py-5 lg:p-6">
            <h3 class="text-base sm:text-lg leading-6 font-bold text-[#2d3748] mb-3 sm:mb-4">
                <i class="fas fa-history text-[#fcd34d] mr-2"></i>Recent Appointments
            </h3>
            
            <!-- Mobile View: Cards -->
            <div class="block md:hidden space-y-3">
                @forelse($recent_appointments as $appointment)
                    <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-[#0066cc] hover:shadow-lg transition-shadow">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs text-gray-500">Pet</p>
                                    <p class="text-sm font-medium text-[#2d3748]">{{ $appointment->pet->name }}</p>
                                </div>
                                <span class="text-xs font-bold"
                                    style="color: 
                                    @if($appointment->status === 'scheduled') #d68910
                                    @elseif($appointment->status === 'completed') #52be80
                                    @elseif($appointment->status === 'pending') #2471a3
                                    @elseif($appointment->status === 'cancelled') #ec7063
                                    @else #5d6d7e
                                    @endif;">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </div>
                            
                            <div>
                                <p class="text-xs text-gray-500">Doctor</p>
                                <p class="text-sm text-gray-700">{{ $appointment->doctor->user->name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-xs text-gray-500">Service</p>
                                <p class="text-sm text-gray-700">{{ $appointment->service->name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-xs text-gray-500">Date & Time</p>
                                <p class="text-sm text-gray-700">{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 rounded-lg shadow p-6 text-center border border-gray-200">
                        <p class="text-sm text-gray-500">No recent appointments found.</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1e3a5f]">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Pet</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Doctor</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Service</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Date & Time</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm font-medium text-[#2d3748]">
                                {{ $appointment->pet->name }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm text-gray-700">
                                {{ $appointment->doctor->user->name }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm text-gray-700">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4 text-sm text-gray-700">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-4 lg:px-6 py-3 sm:py-4">
                                <span class="text-xs sm:text-sm font-bold"
                                    style="color: 
                                    @if($appointment->status === 'scheduled') #d68910
                                    @elseif($appointment->status === 'completed') #52be80
                                    @elseif($appointment->status === 'pending') #2471a3
                                    @elseif($appointment->status === 'cancelled') #ec7063
                                    @else #5d6d7e
                                    @endif;">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 lg:px-6 py-4 text-sm text-gray-500 text-center">
                                No recent appointments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<!-- Announcement Modal -->
<div id="announcementModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.7);">
    <div class="flex items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="bg-white rounded-lg sm:rounded-xl max-w-2xl w-full shadow-2xl relative border-2 border-[#0066cc]">
            <!-- Header -->
            <div class="bg-[#1e3a5f] p-4 sm:p-6 rounded-t-lg sm:rounded-t-xl flex justify-between items-center">
                <h3 id="modalTitle" class="text-white text-base sm:text-xl font-bold pr-4 break-words flex-1"></h3>
                <button onclick="hideModal()" class="text-white hover:text-[#fcd34d] text-3xl sm:text-4xl font-light leading-none flex-shrink-0 w-8 h-8 flex items-center justify-center transition-colors">&times;</button>
            </div>
            
            <!-- Content -->
            <div class="p-4 sm:p-6 max-h-[60vh] overflow-y-auto">
                <div id="modalContent" class="text-gray-700 text-sm sm:text-base leading-relaxed whitespace-pre-wrap break-words"></div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 p-4 sm:p-5 rounded-b-lg sm:rounded-b-xl border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-4">
                <div class="text-gray-600 text-xs sm:text-sm">
                    <strong class="text-[#2d3748]">Posted by:</strong> <span id="modalCreator" class="text-gray-900"></span>
                </div>
                <div id="modalDate" class="text-gray-600 text-xs sm:text-sm font-medium"></div>
            </div>
        </div>
    </div>
</div>

<script>
function showAnnouncement(title, content, creator, date) {
    console.log('Button clicked!');
    console.log('Title:', title);
    console.log('Content:', content);
    
    // Set content
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalContent').innerText = content;
    document.getElementById('modalCreator').innerText = creator;
    document.getElementById('modalDate').innerText = date;
    
    // Show modal
    var modal = document.getElementById('announcementModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    console.log('Modal should be visible now');
}

function hideModal() {
    console.log('Closing modal');
    var modal = document.getElementById('announcementModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Click outside to close
document.getElementById('announcementModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideModal();
    }
});

// Escape key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideModal();
    }
});
</script>
@endsection