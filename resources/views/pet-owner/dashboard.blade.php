@extends('layouts.app')

@section('title', 'Pet Owner Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-blue-100 shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Pet Owner Dashboard</h1>
        <p class="text-gray-600">Welcome, {{ Auth::user()->name }}</p>
    </div>

    <!-- Top Section: Statistics Cards + Announcements Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Statistics Cards in 2x2 Grid (Takes 2 columns) -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-2 gap-6">
                <!-- My Pets Card -->
                <a href="{{ route('pet-owner.pets') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-paw text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">My Pets</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_pets'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Upcoming Appointments Card -->
                <a href="{{ route('pet-owner.appointments', ['status' => 'scheduled']) }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Appointments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['upcoming_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Medical Records Card -->
                <a href="{{ route('pet-owner.medical-records') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-medical text-purple-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Medical Records</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_medical_records'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Total Appointments Card -->
                <a href="{{ route('pet-owner.appointments') }}" class="bg-blue-100 overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200 cursor-pointer transform hover:scale-105 transition-transform">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Appointments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_appointments'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Right: Announcements Container (Takes 1 column) -->
        <div class="lg:col-span-1">
            <div class="bg-blue-100 shadow rounded-lg h-full" style="display: flex; flex-direction: column;">
                <div class="px-4 py-3 border-b border-gray-400">
                    <h3 class="text-base font-bold text-gray-900">
                        Announcements
                    </h3>
                </div>
                
                <div class="px-4 py-3 space-y-2 overflow-y-auto" style="flex: 1; max-height: 400px;">
                    @forelse($announcements as $announcement)
                        <button 
                            type="button"
                            onclick="showAnnouncement('{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->content) }}', '{{ addslashes($announcement->creator->name) }}', '{{ $announcement->created_at->format('F d, Y') }}')"
                            class="text-left w-full bg-white hover:bg-gray-50 px-3 py-2.5 rounded border border-gray-400 transition-colors duration-150 block">
                            <h4 class="font-normal text-gray-900 text-sm">
                                {{ $announcement->title }}
                            </h4>
                        </button>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-bullhorn text-3xl mb-2 text-gray-300"></i>
                            <p class="text-xs">No announcements yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments Section (Full Width Below) -->
    <div class="bg-blue-100 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Appointments</h3>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-blue-200">
                    <thead class="bg-blue-100">
                        <tr>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Pet</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Doctor</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Service</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Date & Time</th>
                            <th class="bg-blue-500 px-6 py-3 text-left text-xs font-bold text-black-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-blue-100 divide-y divide-gray-200">
                        @forelse($recent_appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $appointment->pet->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->doctor->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold
                                    @if($appointment->status === 'scheduled') text-yellow-600
                                    @elseif($appointment->status === 'confirmed') text-blue-600
                                    @elseif($appointment->status === 'in_progress') text-purple-600
                                    @elseif($appointment->status === 'completed') text-green-600
                                    @elseif($appointment->status === 'pending') text-orange-600
                                    @else text-red-600
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No recent appointments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('pet-owner.pets') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                    <i class="fas fa-paw mr-2"></i>
                    My Pets
                </a>
                <a href="{{ route('pet-owner.appointments') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    My Appointments
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div id="announcementModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 99999; overflow-y: auto;">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
        <div style="background: white; border-radius: 12px; max-width: 700px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative;">
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 20px 24px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalTitle" style="color: white; font-size: 20px; font-weight: bold; margin: 0;"></h3>
                <button onclick="hideModal()" style="color: white; background: none; border: none; font-size: 28px; cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
            
            <!-- Content -->
            <div style="padding: 24px; max-height: 60vh; overflow-y: auto;">
                <div id="modalContent" style="color: #374151; font-size: 16px; line-height: 1.7; white-space: pre-wrap;"></div>
            </div>
            
            <!-- Footer -->
            <div style="background: #f9fafb; padding: 16px 24px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div style="color: #6b7280; font-size: 14px;">
                    <strong>Posted by:</strong> <span id="modalCreator" style="color: #111827;"></span>
                </div>
                <div id="modalDate" style="color: #6b7280; font-size: 14px;"></div>
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
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    console.log('Modal should be visible now');
}

function hideModal() {
    console.log('Closing modal');
    var modal = document.getElementById('announcementModal');
    modal.style.display = 'none';
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
