<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Veterinary Clinic')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom responsive styles */
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 40;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }
        
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
        }
        
        .overlay.active {
            display: block;
        }

        /* Smooth transitions */
        * {
            transition: margin 0.3s ease, padding 0.3s ease;
        }

        /* Responsive container */
        .main-content {
            width: 100%;
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 769px) {
            .main-content {
                margin-left: 16rem; /* w-64 = 16rem */
            }
        }

        /* Notification dropdown responsive */
        @media (max-width: 640px) {
            #notificationDropdown {
                right: 0;
                left: auto;
                min-width: 90vw;
            }
        }

        /* Notification animations */
        .notification-item {
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f3f4f6;
        }

        /* Unread notification highlight */
        .notification-unread {
            background-color: #eff6ff;
            border-left: 3px solid #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="bg-blue-100 shadow-md fixed top-0 left-0 right-0 z-50">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left Section: Menu Button & Logo -->
                    <div class="flex items-center gap-3 sm:gap-4">
                        @auth
                        <!-- Mobile Menu Toggle -->
                        <button id="menuToggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-bars text-xl text-gray-700"></i>
                        </button>
                        @endauth
                        
                        <!-- Logo -->
                        <div class="flex items-center gap-2">
                            <i class="fas fa-paw text-2xl sm:text-3xl text-blue-600"></i>
                            <span class="text-lg sm:text-xl font-bold text-gray-800">PetPro Animal Clinic</span>
                        </div>
                    </div>

                    <!-- Right Section: Auth Actions -->
                    @auth
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- User Name (hidden on very small screens) -->
                        <span class="text-sm sm:text-base text-gray-700 font-medium hidden sm:block truncate max-w-[150px]">
                            {{ Auth::user()->name }}
                        </span>

                        <!-- Profile Icon -->
                        <button id="profileButton" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" title="My Profile">
                            <i class="fas fa-user-circle text-lg sm:text-xl text-gray-700"></i>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notificationButton" class="relative p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-bell text-lg sm:text-xl text-gray-700"></i>
                                <span id="notificationBadge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
                                    0
                                </span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50">
                                <!-- Header -->
                                <div class="p-3 sm:p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Notifications</h3>
                                    <button id="markAllReadBtn" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Mark all read
                                    </button>
                                </div>

                                <!-- Notifications List -->
                                <div id="notificationList" class="divide-y divide-gray-200">
                                    <div class="p-8 text-center">
                                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500">Loading...</p>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="p-3 border-t border-gray-200 text-center bg-gray-50">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 p-2 sm:px-3 sm:py-2 text-gray-700 hover:bg-gray-100 rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-sign-out-alt text-lg"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="flex items-center gap-2 sm:gap-4">
                        <a href="{{ route('login') }}" class="px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base text-gray-700 hover:text-gray-900 font-medium">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Register
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Overlay for mobile menu -->
        <div id="overlay" class="overlay"></div>

        <!-- Main Container -->
        <div class="pt-16"> <!-- Padding top to account for fixed navbar -->
            @auth
            <div class="flex">
                <!-- Sidebar -->
                <aside id="sidebar" class="sidebar w-64 bg-gray-800 shadow-lg min-h-[calc(100vh-4rem)] md:fixed md:block">
                    <div class="p-4 overflow-y-auto h-full">
                        <nav class="space-y-2">
                            @if(Auth::user()->isAdmin())
                                <!-- Admin Navigation -->
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Dashboard</span>
                                </a>
                                <a href="{{ route('admin.pet-owners') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-users mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Pet Owners</span>
                                </a>
                                <a href="{{ route('admin.pets') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-paw mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Pets</span>
                                </a>
                                <a href="{{ route('admin.appointments') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-calendar-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Appointments</span>
                                </a>
                                <a href="{{ route('admin.medical-records') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-file-medical mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Medical Records</span>
                                </a>
                                <a href="{{ route('admin.inventory') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-boxes mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Inventory</span>
                                </a>
                                <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-chart-bar mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Reports</span>
                                </a>
                                <a href="{{ route('messages.inbox') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-envelope mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Messages</span>
                                    @php
                                        $unreadCount = Auth::user()->unreadMessages()->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('announcements.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-bullhorn mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Announcements</span>
                                </a>

                            @elseif(Auth::user()->isPetOwner())
                                <!-- Pet Owner Navigation -->
                                <a href="{{ route('pet-owner.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Dashboard</span>
                                </a>
                                <a href="{{ route('pet-owner.pets') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-paw mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">My Pets</span>
                                </a>
                                <a href="{{ route('pet-owner.appointments') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-calendar-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">My Appointments</span>
                                </a>
                                <a href="{{ route('pet-owner.medical-records') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-file-medical mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Medical Records</span>
                                </a>
                                <a href="{{ route('pet-owner.bills') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-file-invoice-dollar mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Bills</span>
                                </a>
                                <a href="{{ route('pet-owner.clinic-details') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-hospital mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Clinic Details</span>
                                </a>
                                <a href="{{ route('messages.inbox') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-envelope mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Messages</span>
                                    @php
                                        $unreadCount = Auth::user()->unreadMessages()->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                                    @endif
                                </a>

                            @elseif(Auth::user()->isDoctor())
                                <!-- Doctor Navigation -->
                                <a href="{{ route('doctor.dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Dashboard</span>
                                </a>
                                <a href="{{ route('doctor.appointments') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-calendar-alt mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Appointments</span>
                                </a>
                                <a href="{{ route('doctor.medical-records') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-file-medical mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Medical Records</span>
                                </a>
                                <a href="{{ route('doctor.patients') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-users mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Patients</span>
                                </a>
                                <a href="{{ route('doctor.bills') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-dollar-sign mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Billing</span>
                                </a>
                                <a href="{{ route('messages.inbox') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-envelope mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Messages</span>
                                    @php
                                        $unreadCount = Auth::user()->unreadMessages()->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('announcements.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-bullhorn mr-3 w-5"></i>
                                    <span class="text-sm sm:text-base">Announcements</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <main class="main-content flex-1 w-full">
                    <div class="p-4 sm:p-6 lg:p-8 max-w-full overflow-x-hidden">
                        <!-- Success Messages -->
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm sm:text-base">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm sm:text-base">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')
                    </div>
                </main>
            </div>
            @else
                <!-- Guest Content (No Sidebar) -->
                <div class="p-4 sm:p-6 lg:p-8">
                    @yield('content')
                </div>
            @endauth
        </div>

        <!-- Modals -->
        @yield('modals')
        
        <!-- Profile Modal -->
        @auth
        <div id="profileModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">My Profile</h3>
                    <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="profileContent" class="p-6">
                    <div class="flex justify-center items-center py-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div id="editProfileModal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">Edit Profile</h3>
                    <button onclick="closeEditProfileModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="editProfileForm" class="p-6">
                    @csrf
                    <div id="editProfileContent">
                        <div class="flex justify-center items-center py-8">
                            <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endauth
    </div>

    <!-- JavaScript for Mobile Menu & Notifications & Profile -->
    <script>
        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });

            // Close sidebar when clicking a link on mobile
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 769) {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('active');
                    }
                });
            });
        }

        // Notification System
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');
        const markAllReadBtn = document.getElementById('markAllReadBtn');

        // Toggle dropdown
        if (notificationButton && notificationDropdown) {
            notificationButton.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        loadNotifications();
                    }
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                }
            });
        }

        // Load notifications function
        async function loadNotifications() {
            try {
                const response = await fetch('/api/notifications');
                const data = await response.json();
                
                console.log('Notifications loaded:', data);
                
                // Update badge
                const unreadCount = data.unread_count || 0;
                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
                
                // Update notification list
                const notifications = data.notifications || [];
                if (notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="p-8 text-center">
                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No notifications</p>
                        </div>
                    `;
                } else {
                    notificationList.innerHTML = notifications.map(notification => {
                        const iconClass = getNotificationIcon(notification.icon);
                        const colorClass = getNotificationColor(notification.color);
                        const isUnread = !notification.is_read;
                        
                        return `
                            <a href="${notification.url}" 
                               class="notification-item block p-3 sm:p-4 ${isUnread ? 'notification-unread' : ''}">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <i class="fas ${iconClass} ${colorClass}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-sm font-semibold text-gray-900">${notification.title}</p>
                                            ${isUnread ? '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>' : ''}
                                        </div>
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-2">${notification.message}</p>
                                        <p class="text-xs text-gray-400 mt-1">${notification.time_ago}</p>
                                    </div>
                                </div>
                            </a>
                        `;
                    }).join('');
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <div class="p-8 text-center">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-300 mb-2"></i>
                        <p class="text-sm text-red-500">Error loading notifications</p>
                    </div>
                `;
            }
        }

        // Helper function to get icon class
        function getNotificationIcon(icon) {
            return icon || 'fa-bell';
        }

        // Helper function to get color class
        function getNotificationColor(color) {
            const colorMap = {
                'blue': 'text-blue-500',
                'green': 'text-green-500',
                'red': 'text-red-500',
                'yellow': 'text-yellow-500',
                'purple': 'text-purple-500',
                'orange': 'text-orange-500'
            };
            return colorMap[color] || 'text-gray-500';
        }

        // Load notifications on page load
        if (notificationBadge) {
            document.addEventListener('DOMContentLoaded', () => {
                loadNotifications();
                // Refresh every 30 seconds
                setInterval(loadNotifications, 30000);
            });
        }

        // Responsive handling for window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 769) {
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
            }
        });

        // Profile Modal Functions
        const profileButton = document.getElementById('profileButton');
        const profileModal = document.getElementById('profileModal');
        const editProfileModal = document.getElementById('editProfileModal');
        
        if (profileButton) {
            profileButton.addEventListener('click', () => {
                openProfileModal();
            });
        }
        
        function openProfileModal() {
            profileModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            loadProfileData();
        }
        
        function closeProfileModal() {
            profileModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function openEditProfileModal() {
            closeProfileModal();
            editProfileModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            loadEditProfileForm();
        }
        
        function closeEditProfileModal() {
            editProfileModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        async function loadProfileData() {
            try {
                const response = await fetch('/profile', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                
                const user = data.user;
                const roleData = data.role_data;
                
                let roleSpecificHTML = '';
                
                if (user.role === 'pet_owner') {
                    roleSpecificHTML = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <i class="fas fa-paw text-2xl text-blue-600 mb-2"></i>
                                <p class="text-2xl font-bold text-gray-800">${roleData.total_pets || 0}</p>
                                <p class="text-sm text-gray-600">Total Pets</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <i class="fas fa-calendar-alt text-2xl text-green-600 mb-2"></i>
                                <p class="text-2xl font-bold text-gray-800">${roleData.total_appointments || 0}</p>
                                <p class="text-sm text-gray-600">Appointments</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">Emergency Contact</label>
                                <p class="font-medium text-gray-800">${roleData.emergency_contact || 'Not set'}</p>
                            </div>
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">Emergency Phone</label>
                                <p class="font-medium text-gray-800">${roleData.emergency_phone || 'Not set'}</p>
                            </div>
                        </div>
                    `;
                } else if (user.role === 'doctor') {
                    roleSpecificHTML = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <i class="fas fa-user-injured text-2xl text-purple-600 mb-2"></i>
                                <p class="text-2xl font-bold text-gray-800">${roleData.total_patients || 0}</p>
                                <p class="text-sm text-gray-600">Total Patients</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <i class="fas fa-calendar-check text-2xl text-green-600 mb-2"></i>
                                <p class="text-2xl font-bold text-gray-800">${roleData.total_appointments || 0}</p>
                                <p class="text-sm text-gray-600">Appointments</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">Specialization</label>
                                <p class="font-medium text-gray-800">${roleData.specialization || 'Not set'}</p>
                            </div>
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">License Number</label>
                                <p class="font-medium text-gray-800">${roleData.license_number || 'Not set'}</p>
                            </div>
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">Experience</label>
                                <p class="font-medium text-gray-800">${roleData.experience_years || 0} years</p>
                            </div>
                            ${roleData.bio ? `
                            <div class="border-b pb-2">
                                <label class="text-sm text-gray-600">Bio</label>
                                <p class="font-medium text-gray-800">${roleData.bio}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                } else if (user.role === 'admin') {
                    roleSpecificHTML = `
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-lg text-center mb-6">
                            <i class="fas fa-crown text-4xl text-yellow-500 mb-3"></i>
                            <p class="text-lg font-semibold text-gray-800">Administrator Account</p>
                            <p class="text-sm text-gray-600 mt-1">Full system access and management privileges</p>
                        </div>
                    `;
                }
                
                document.getElementById('profileContent').innerHTML = `
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-4">
                            <i class="fas fa-user text-4xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">${user.name}</h2>
                        <p class="text-gray-600">${user.email}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full capitalize">
                            ${user.role.replace('_', ' ')}
                        </span>
                    </div>
                    
                    ${roleSpecificHTML}
                    
                    <div class="mt-6 pt-6 border-t space-y-3">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Phone</span>
                            <span class="font-medium text-gray-800">${user.phone || 'Not set'}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-sm text-gray-600">Address</span>
                            <span class="font-medium text-gray-800 text-right max-w-xs">${user.address || 'Not set'}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="font-medium text-gray-800">${new Date(user.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button onclick="openEditProfileModal()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-edit mr-2"></i>Edit Profile
                        </button>
                    </div>
                `;
            } catch (error) {
                document.getElementById('profileContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-3"></i>
                        <p class="text-red-600">Error loading profile</p>
                    </div>
                `;
            }
        }
        
        async function loadEditProfileForm() {
            try {
                const response = await fetch('/profile', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                const user = data.user;
                const roleData = data.role_data;
                
                let roleSpecificFields = '';
                
                if (user.role === 'pet_owner') {
                    roleSpecificFields = `
                        <div class="col-span-2 border-t pt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Emergency Contact Information</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact" value="${roleData.emergency_contact || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Phone</label>
                            <input type="text" name="emergency_phone" value="${roleData.emergency_phone || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    `;
                } else if (user.role === 'doctor') {
                    roleSpecificFields = `
                        <div class="col-span-2 border-t pt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Professional Information</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specialization *</label>
                            <input type="text" name="specialization" value="${roleData.specialization || ''}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">License Number *</label>
                            <input type="text" name="license_number" value="${roleData.license_number || ''}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Experience (years) *</label>
                            <input type="number" name="experience_years" value="${roleData.experience_years || 0}" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea name="bio" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">${roleData.bio || ''}</textarea>
                        </div>
                    `;
                }
                
                document.getElementById('editProfileContent').innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" value="${user.name}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" value="${user.email}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="${user.phone || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" value="${user.address || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        ${roleSpecificFields}
                        
                        <div class="col-span-2 border-t pt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Change Password (Optional)</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeEditProfileModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                `;
                
                // Handle form submission
                document.getElementById('editProfileForm').onsubmit = async function(e) {
                    e.preventDefault();
                    await saveProfileChanges();
                };
            } catch (error) {
                document.getElementById('editProfileContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-3"></i>
                        <p class="text-red-600">Error loading form</p>
                    </div>
                `;
            }
        }
        
        async function saveProfileChanges() {
            const form = document.getElementById('editProfileForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            
            try {
                const response = await fetch('/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Error updating profile');
                }
                
                closeEditProfileModal();
                alert('Profile updated successfully!');
                window.location.reload();
            } catch (error) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert(error.message || 'Error updating profile. Please try again.');
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == profileModal) {
                closeProfileModal();
            }
            if (event.target == editProfileModal) {
                closeEditProfileModal();
            }
        };
    </script>
</body>
</html>