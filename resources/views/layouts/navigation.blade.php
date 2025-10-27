@php
    // Prepare a safe notifications array for the inline JS (empty if not available)
    $navNotifications = [];
    if (isset($notifications) && $notifications instanceof \Illuminate\Support\Collection) {
        $navNotifications = $notifications->map(function($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'created_at' => $notif->created_at->diffForHumans(),
                'is_read' => (bool) $notif->is_read,
                'link' => $notif->link ?? null, // Add this line
            ];
        })->toArray();
    }
    
    // Determine dashboard route based on user role
    $dashboardRoute = match(Auth::user()->role) {
        'admin' => route('admin.dashboard'),
        'instructor' => route('instructors.dashboard'),
        'student' => route('students.dashboard'),
        default => '/',
    };
@endphp

<div x-data="navigationComponent()" x-init="init()">
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-pink-200/50 dark:border-pink-800/50 shadow-lg bg-gradient-to-r from-pink-500 via-pink-400 to-pink-500 dark:from-pink-900 dark:via-pink-800 dark:to-pink-900"
         style="background: linear-gradient(90deg, #D5006D 0%, #E0007A 50%, #D5006D 100%);"
         @scroll.window="isScrolled = (window.pageYOffset > 10)"
         :class="{ 'backdrop-blur-md bg-pink-400/90 dark:bg-pink-900/90': isScrolled }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Side -->
                <div class="flex items-center gap-4">
                    <!-- Toggle Button -->
                    <button @click="$store.sidebar.toggle()" 
                        class="p-3 rounded-xl bg-gradient-to-br from-pink-400 to-pink-500 hover:from-pink-500 hover:to-pink-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 group"
                        style="background: linear-gradient(135deg, #D5006D 0%, #B0005A 100%);">
                        <i class="fas fa-bars-staggered text-white transition-transform duration-200 group-hover:rotate-12"></i>
                    </button>

                    <!-- Brand -->
                    <div class="flex items-center gap-4">
                        <a href="{{ $dashboardRoute }}" class="flex items-center gap-4">
                            <div class="h-12 w-12 bg-gradient-to-br from-pink-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                            style="background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);">
                                <img src="{{ asset('assets/app_logo.PNG') }}" 
                                    alt="GAS Logo" 
                                    class="h-8 w-8 object-contain" />
                            </div>
                            <div>
                                <span class="text-2xl font-black tracking-tight text-white dark:text-gray-200">
                                    GAS System
                                </span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Right Side - Notification Bell and Profile Menu -->
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button @click="toggleNotifications()" 
                                class="relative p-2 rounded-xl hover:bg-white/10 transition-colors duration-200">
                            <i class="fas fa-bell text-white text-xl"></i>

                            <div x-show="unreadCount > 0" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-0"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-0"
                                 class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                                <span class="text-white text-xs font-bold" x-text="unreadCount"></span>
                            </div>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="notificationOpen" 
                            @click.away="notificationOpen = false"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90 translate-y-[-20px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90 translate-y-[-10px]"
                            class="absolute right-0 top-16 w-80 rounded-2xl shadow-2xl border-0 backdrop-blur-xl overflow-hidden z-50"
                            style="background: linear-gradient(145deg, rgba(255,255,255,0.98) 0%, rgba(255,240,247,0.95) 50%, rgba(255,228,242,0.98) 100%);">

                            <!-- Header -->
                            <div class="px-4 py-3 bg-gradient-to-r from-pink-50/80 to-purple-50/80 border-b border-pink-100/30 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                <div class="flex items-center gap-2">
                                    <div x-show="loading" class="flex items-center gap-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-pink-600"></div>
                                        <span class="text-xs text-pink-600">Updating...</span>
                                    </div>
                                    <div x-show="!loading && allRead" class="flex items-center gap-1">
                                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                        <span class="text-xs text-green-600">All read!</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification List -->
                            <div class="max-h-[400px] overflow-y-auto">
                                <template x-for="notif in notifications" :key="notif.id">
                                    <!-- Make the entire notification clickable -->
                                    <a :href="notif.link || '#'" 
                                    @click="notif.link ? handleNotificationClick($event, notif) : $event.preventDefault()"
                                    class="block p-4 hover:bg-pink-50/50 border-b border-pink-100/30 transition-all duration-200 cursor-pointer"
                                    :class="{ 'bg-blue-50/30': !notif.is_read, 'opacity-75': notif.is_read }">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                                :class="notif.is_read ? 'bg-gray-100' : 'bg-blue-100'">
                                                <i class="fas fa-bell text-sm transition-colors duration-200"
                                                :class="notif.is_read ? 'text-gray-500' : 'text-blue-600'"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-800">
                                                    <span class="font-semibold" x-text="notif.title"></span>  
                                                    - <span x-text="notif.message"></span>
                                                </p>
                                                <span class="text-xs text-gray-500" x-text="notif.created_at"></span>
                                            </div>

                                            <!-- Read indicator -->
                                            <div class="flex items-center">
                                                <div x-show="!notif.is_read" 
                                                    class="w-2 h-2 bg-blue-500 rounded-full animate-pulse">
                                                </div>
                                                <div x-show="notif.is_read" 
                                                    class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-check text-green-500 text-xs"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </template>

                                <div x-show="notifications.length === 0" 
                                    class="p-8 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-bell-slash text-gray-400 text-xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">No notifications</p>
                                </div>
                            </div>

                            <a href="{{ route('notifications.index') }}" 
                            class="block px-4 py-3 text-sm text-center text-pink-600 hover:text-pink-800 hover:bg-pink-50/50 border-t border-pink-100/30 transition-colors duration-200">
                                View All Notifications
                            </a>
                        </div>
                    </div>

                    <!-- Profile Menu -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" 
                            class="flex items-center gap-4 px-4 py-2 rounded-xl transition-all duration-300 bg-white/10 hover:bg-white/20 group relative">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                                <span class="text-sm font-bold text-white" x-text="userInitial">
                                    {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="text-sm font-bold text-white dark:text-gray-300 group-hover:text-pink-100 transition-colors duration-300" x-text="userName">
                                        {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                                    </span>
                                    <p class="text-xs text-pink-100 dark:text-gray-400">User</p>
                                </div>
                                <i class="fas fa-chevron-down text-sm text-white opacity-75 group-hover:text-pink-100 transform group-hover:rotate-180 transition-all duration-300" 
                                   :class="{ 'rotate-180': profileOpen }"></i>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" @click.away="profileOpen = false"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90 translate-y-[-20px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90 translate-y-[-10px]"
                            class="absolute right-0 mt-4 w-80 rounded-3xl shadow-2xl border-0 backdrop-blur-xl overflow-hidden z-50"
                            style="background: linear-gradient(145deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 240, 247, 0.95) 50%, rgba(255, 228, 242, 0.98) 100%); 
                                    box-shadow: 0 25px 50px -12px rgba(213, 0, 109, 0.25), 0 0 0 1px rgba(213, 0, 109, 0.1);">
                            
                            <!-- Decorative Top Bar -->
                            <div class="h-1 bg-gradient-to-r from-[#D5006D] via-[#E0007A] to-[#D5006D]"></div>
                            
                            <!-- Profile Header with Enhanced Design -->
                            <div class="relative px-6 py-5 bg-gradient-to-br from-pink-50/80 to-rose-50/60">
                                <!-- Decorative Background Elements -->
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-pink-200/30 to-transparent rounded-full blur-xl"></div>
                                <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-rose-200/20 to-transparent rounded-full blur-lg"></div>
                                
                                <div class="relative flex items-center space-x-4">
                                    <!-- Enhanced Avatar -->
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-400 via-pink-500 to-rose-500 flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300" 
                                            style="background: linear-gradient(135deg, #D5006D 0%, #B0005A 50%, #8C0047 100%);">
                                            <span class="text-base font-bold text-white" x-text="userInitial">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <!-- Status Indicator -->
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full animate-pulse"></div>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-1" x-text="userName">{{ Auth::user()->name }}</h3>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-3 py-1 text-white text-xs font-medium rounded-full shadow-sm" style="background: linear-gradient(135deg, #D5006D 0%, #B0005A 100%);">
                                                ✨ System User
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Menu Items with Card-like Design -->
                            <div class="px-3 py-4 space-y-2">
                                <!-- Profile Card -->
                                <a :href="profileEditRoute" 
                                class="group relative block p-4 rounded-2xl bg-gradient-to-br from-white/80 to-pink-50/50 hover:from-pink-100/80 hover:to-rose-100/60 border border-pink-100/50 hover:border-pink-200/80 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                                <i class="fas fa-user text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-700 transition-colors duration-200">My Profile</h4>
                                            <p class="text-xs text-gray-500 mt-1">Manage your account settings</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all duration-200"></i>
                                        </div>
                                    </div>
                                </a>

                                <!-- Dark Mode Toggle Card -->
                                <button @click="$store.darkMode.toggle()"
                                        class="group relative w-full block p-4 rounded-2xl bg-gradient-to-br from-white/80 to-pink-50/50 hover:from-purple-100/80 hover:to-indigo-100/60 border border-pink-100/50 hover:border-purple-200/80 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                                <template x-if="$store.darkMode.on">
                                                    <i class="fas fa-sun text-amber-300 text-sm animate-spin-slow"></i>
                                                </template>
                                                <template x-if="!$store.darkMode.on">
                                                    <i class="fas fa-moon text-white text-sm"></i>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-purple-700 transition-colors duration-200" x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'"></h4>
                                            <p class="text-xs text-gray-500 mt-1">Switch theme appearance</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <!-- Toggle Switch -->
                                            <div class="relative">
                                                <div class="w-10 h-6 bg-gray-300 rounded-full shadow-inner transition-colors duration-200" :class="$store.darkMode.on ? 'bg-purple-500' : 'bg-gray-300'">
                                                    <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-200 mt-1 ml-1" :class="$store.darkMode.on ? 'translate-x-4' : ''"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>

                                <!-- Elegant Divider -->
                                <div class="relative my-4">
                                    <div class="absolute inset-0 flex items-center">
                                        <div class="w-full border-t border-gradient-to-r from-transparent via-pink-200 to-transparent"></div>
                                    </div>
                                    <div class="relative flex justify-center">
                                        <span class="px-3 bg-white text-xs text-gray-400">•••</span>
                                    </div>
                                </div>

                                <!-- Logout Card -->
                                <button @click="logout()" 
                                        class="group relative w-full block p-4 rounded-2xl bg-gradient-to-br from-white/80 to-pink-50/50 hover:from-red-100/80 hover:to-pink-100/60 border border-pink-100/50 hover:border-red-200/80 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-300">
                                                <i class="fas fa-sign-out-alt text-white text-sm group-hover:animate-pulse"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-red-700 transition-colors duration-200">Sign Out</h4>
                                            <p class="text-xs text-gray-500 mt-1">End your current session</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-red-500 group-hover:translate-x-1 transition-all duration-200"></i>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            
                            <!-- Footer with Gradient -->
                            <div class="px-6 py-3 bg-gradient-to-r from-pink-50/50 to-rose-50/50 border-t border-pink-100/30">
                                <p class="text-center text-xs text-gray-400">GAS System v2.0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>

<script>
function navigationComponent() {
    return {
        // State
        isScrolled: false,
        notificationOpen: false,
        profileOpen: false,
        loading: false,
        allRead: false,
        
        // Data
        userName: @json(Auth::check() ? Auth::user()->name : 'Guest'),
        userInitial: @json(Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G'),
        unreadCount: @json($unreadCount ?? 0),
        notifications: @json($navNotifications),
        
        // Routes
        profileEditRoute: '{{ route("profile.edit") }}',
        notificationsIndexRoute: '{{ route("notifications.index") }}',
        markAllReadRoute: '{{ route("notifications.readAll") }}',
        markReadRoute: '{{ route("notifications.read", ":id") }}',
        logoutRoute: '{{ route("logout") }}',
        
        // CSRF Token
        csrfToken: '{{ csrf_token() }}',
        
        init() {
            this.updateUnreadCount();
            this.updateAllReadStatus();
        },
        
        async toggleNotifications() {
            this.notificationOpen = !this.notificationOpen;
            
            // Auto-mark all notifications as read when opening
            if (this.notificationOpen && this.unreadCount > 0) {
                await this.markAllAsReadAuto();
            }
        },

        handleNotificationClick(event, notif) {
            // Mark as read if not already
            if (!notif.is_read) {
                notif.is_read = true;
                this.updateUnreadCount();
            }
            
            // Close the dropdown
            this.notificationOpen = false;
            
            // Let the link navigate naturally
            // The event will propagate and follow the href
        },
        
        async markAllAsReadAuto() {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const response = await fetch(this.markAllReadRoute, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Mark all notifications as read locally
                    this.notifications.forEach(notification => {
                        if (!notification.is_read) {
                            notification.is_read = true;
                        }
                    });
                    
                    this.updateUnreadCount();
                    this.updateAllReadStatus();
                    
                    // Show success feedback briefly
                    this.allRead = true;
                    setTimeout(() => {
                        this.allRead = false;
                    }, 2000);
                }
            } catch (error) {
                console.error('Error marking notifications as read:', error);
            } finally {
                this.loading = false;
            }
        },
        
        updateUnreadCount() {
            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        },
        
        updateAllReadStatus() {
            this.allRead = this.notifications.length > 0 && this.unreadCount === 0;
        },
        
        logout() {
            if (confirm('Are you sure you want to log out?')) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.logoutRoute;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = this.csrfToken;
                
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>