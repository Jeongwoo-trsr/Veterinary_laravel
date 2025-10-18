<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Veterinary Clinic') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-100 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center">
                            <i class="fas fa-paw text-blue-600 text-2xl mr-2"></i>
                            <span class="text-xl font-bold text-gray-800">VetClinic</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                            <!-- Add this to your navbar in layouts/app.blade.php, before the logout button -->

<style>
.notification-bell {
    position: relative;
    cursor: pointer;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -8px;
    background: #ef4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    text-align: center;
}

.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 10px;
    width: 360px;
    max-height: 500px;
    overflow-y: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    z-index: 1000;
}

.notification-dropdown.show {
    display: block;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    cursor: pointer;
    transition: background 0.2s;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #eff6ff;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.icon-blue { background: #dbeafe; color: #2563eb; }
.icon-green { background: #d1fae5; color: #059669; }
.icon-red { background: #fee2e2; color: #dc2626; }
.icon-purple { background: #f3e8ff; color: #7c3aed; }
.icon-gray { background: #f3f4f6; color: #6b7280; }
</style>

<div class="notification-bell" id="notificationBell">
    <i class="fas fa-bell text-gray-700 text-xl"></i>
    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
    
    <div class="notification-dropdown" id="notificationDropdown">
        <div style="padding: 16px; border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-weight: 700; font-size: 16px; color: #111827;">Notifications</h3>
                <button onclick="markAllAsRead()" style="background: none; border: none; color: #2563eb; font-size: 12px; cursor: pointer; font-weight: 600;">
                    Mark all as read
                </button>
            </div>
        </div>
        
        <div id="notificationList">
            <div style="padding: 40px; text-align: center; color: #9ca3af;">
                <i class="fas fa-bell-slash" style="font-size: 40px; margin-bottom: 12px;"></i>
                <p>No notifications</p>
            </div>
        </div>
        
        <div style="padding: 12px; border-top: 2px solid #e5e7eb; text-align: center; background: #f9fafb;">
            <a href="{{ route('notifications.index') }}" style="color: #2563eb; font-size: 13px; font-weight: 600; text-decoration: none;">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
let notificationDropdown = document.getElementById('notificationDropdown');
let notificationBell = document.getElementById('notificationBell');
let notificationBadge = document.getElementById('notificationBadge');

// Toggle dropdown
notificationBell.addEventListener('click', function(e) {
    e.stopPropagation();
    notificationDropdown.classList.toggle('show');
    if (notificationDropdown.classList.contains('show')) {
        loadNotifications();
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!notificationBell.contains(e.target)) {
        notificationDropdown.classList.remove('show');
    }
});

// Load notifications
function loadNotifications() {
    fetch('{{ route("notifications.index") }}')
        .then(response => response.text())
        .then(html => {
            // Extract notifications from the full page HTML
            // For simplicity, we'll create a separate API endpoint
            loadNotificationsAPI();
        });
}

function loadNotificationsAPI() {
    // We'll create this endpoint
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            displayNotifications(data.notifications);
            updateBadge(data.unread_count);
        });
}

function displayNotifications(notifications) {
    const listContainer = document.getElementById('notificationList');
    
    if (notifications.length === 0) {
        listContainer.innerHTML = `
            <div style="padding: 40px; text-align: center; color: #9ca3af;">
                <i class="fas fa-bell-slash" style="font-size: 40px; margin-bottom: 12px;"></i>
                <p>No notifications</p>
            </div>
        `;
        return;
    }
    
    listContainer.innerHTML = notifications.map(notif => `
        <div class="notification-item ${!notif.is_read ? 'unread' : ''}" onclick="markAsRead(${notif.id})">
            <div style="display: flex; gap: 12px;">
                <div class="notification-icon icon-${notif.color}">
                    <i class="fas ${notif.icon}"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; font-size: 14px; color: #111827; margin-bottom: 4px;">
                        ${notif.title}
                    </div>
                    <div style="font-size: 13px; color: #6b7280; margin-bottom: 4px;">
                        ${notif.message}
                    </div>
                    <div style="font-size: 11px; color: #9ca3af;">
                        ${notif.time_ago}
                    </div>
                </div>
                ${!notif.is_read ? '<div style="width: 8px; height: 8px; background: #2563eb; border-radius: 50%;"></div>' : ''}
            </div>
        </div>
    `).join('');
}

function updateBadge(count) {
    if (count > 0) {
        notificationBadge.textContent = count > 99 ? '99+' : count;
        notificationBadge.style.display = 'block';
    } else {
        notificationBadge.style.display = 'none';
    }
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            loadNotificationsAPI();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(() => {
        loadNotificationsAPI();
    });
}

// Check for new notifications every 30 seconds
setInterval(loadNotificationsAPI, 30000);

// Load on page load
document.addEventListener('DOMContentLoaded', loadNotificationsAPI);
</script>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        @auth
        <div class="flex">
            <div class="w-64 bg-gray-800 shadow-lg min-h-screen">
                <div class="p-4">
                    <nav class="space-y-2">
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.pet-owners') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-users mr-3"></i>
                                Pet Owners
                            </a>
                            <a href="{{ route('admin.pets') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-paw mr-3"></i>
                                Pets
                            </a>
                            <!-- <a href="{{ route('admin.doctors') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-user-md mr-3"></i>
                                Doctors
                            </a> -->
                            <a href="{{ route('admin.appointments') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                Appointments
                            </a>
                            <a href="{{ route('admin.services') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-cogs mr-3"></i>
                                Services
                            </a>
                            <a href="{{ route('admin.medical-records') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-file-medical mr-3"></i>
                                Medical Records
                            </a>
                            <a href="{{ route('admin.inventory') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-file-medical mr-3"></i>
                                Inventory
                            </a>
                            <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-chart-bar mr-3"></i>
                                Reports
                            </a>


                        @elseif(Auth::user()->isPetOwner())
                            <a href="{{ route('pet-owner.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('pet-owner.pets') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-paw mr-3"></i>
                                My Pets
                            </a>
                            <a href="{{ route('pet-owner.appointments') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                My Appointments
                            </a>
                            <!-- <a href="{{ route('pet-owner.appointments.create') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-plus mr-3"></i>
                                Schedule Appointment
                            </a> -->
                            <a href="{{ route('pet-owner.medical-records') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-file-medical mr-3"></i>
                                Medical Records
                            </a>
                            <a href="{{ route('pet-owner.bills') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-file-medical mr-3"></i>
                                Bills
                            </a>


                        @elseif(Auth::user()->isDoctor())
                            <a href="{{ route('doctor.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('doctor.appointments') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                Appointments
                            </a>
                            <a href="{{ route('doctor.medical-records') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-file-medical mr-3"></i>
                                Medical Records
                            </a>
                            <a href="{{ route('doctor.patients') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-users mr-3"></i>
                                Patients
                            </a>

                             <a href="{{ route('doctor.bills') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-dollar-sign mr-3"></i>
                                Billing
                            </a>

                        
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-8">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        @else
            <div class="p-8">
                @yield('content')
            </div>
        @endauth
    </div>
     @yield('modals')
</body>
</html>
