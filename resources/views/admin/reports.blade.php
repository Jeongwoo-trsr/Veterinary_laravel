@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="text-gray-600">Clinic statistics and performance metrics</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportToPDF()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </button>
            <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-blue-100 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ${{ number_format($revenue_by_service->sum('total_revenue'), 2) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-100 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed Appointments</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $appointments_by_status->where('status', 'completed')->first()->count ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-100 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-paw text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pets</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $pets_by_species->sum('count') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-100 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Clients</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                {{ $pets_by_species->sum('count') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Service Chart -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Revenue by Service</h3>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Appointments by Status Chart -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Appointments by Status</h3>
                <canvas id="appointmentsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Pets by Species Chart -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pets by Species</h3>
                <canvas id="petsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Monthly Appointments Trend -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Appointments Trend</h3>
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Service Table -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Revenue by Service</h3>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-blue-100 divide-y divide-gray-200">
                            @forelse($revenue_by_service as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $service->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($service->total_revenue, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No revenue data available.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Appointments by Status Table -->
        <div class="bg-blue-100 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Appointments by Status</h3>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($appointments_by_status as $status)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($status->status === 'scheduled') bg-yellow-100 text-yellow-800
                                        @elseif($status->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($status->status === 'in_progress') bg-purple-100 text-purple-800
                                        @elseif($status->status === 'completed') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $status->count }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No appointment data available.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Revenue by Service Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($revenue_by_service->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($revenue_by_service->pluck('total_revenue')) !!},
            backgroundColor: [
                '#3B82F6',
                '#10B981',
                '#F59E0B',
                '#EF4444',
                '#8B5CF6',
                '#06B6D4'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Appointments by Status Chart
const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
const appointmentsChart = new Chart(appointmentsCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($appointments_by_status->pluck('status')->map(function($status) { return ucfirst(str_replace('_', ' ', $status)); })) !!},
        datasets: [{
            data: {!! json_encode($appointments_by_status->pluck('count')) !!},
            backgroundColor: [
                '#F59E0B',
                '#3B82F6',
                '#8B5CF6',
                '#10B981',
                '#EF4444'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Pets by Species Chart
const petsCtx = document.getElementById('petsChart').getContext('2d');
const petsChart = new Chart(petsCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($pets_by_species->pluck('species')) !!},
        datasets: [{
            label: 'Number of Pets',
            data: {!! json_encode($pets_by_species->pluck('count')) !!},
            backgroundColor: '#3B82F6',
            borderColor: '#1D4ED8',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Monthly Appointments Trend (Sample data)
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Appointments',
            data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 28, 24, 20],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Export functions
function exportToPDF() {
    window.location.href = '{{ route("admin.reports.export", ["format" => "pdf"]) }}';
}

function exportToExcel() {
    window.location.href = '{{ route("admin.reports.export", ["format" => "excel"]) }}';
}
</script>
@endsection
