@php
    $navNotifications = [];
    if (isset($notifications) && $notifications instanceof \Illuminate\Support\Collection) {
        $navNotifications = $notifications->map(function($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->title,
                'message' => $notif->message,
                'created_at' => $notif->created_at->diffForHumans(),
                'is_read' => (bool) $notif->is_read,
                'link' => $notif->link ?? null,
            ];
        })->toArray();
    }

    $dashboardRoute = match(Auth::user()->role) {
        'admin' => route('admin.dashboard'),
        'instructor' => route('instructors.dashboard'),
        'student' => route('students.dashboard'),
        default => '/',
    };
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .nav-root * { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Glassmorphism dropdown base */
    .glass-dropdown {
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border: 1px solid rgba(213, 0, 109, 0.08);
        box-shadow:
            0 4px 6px -1px rgba(213, 0, 109, 0.06),
            0 20px 60px -10px rgba(213, 0, 109, 0.18),
            0 0 0 1px rgba(255, 255, 255, 0.9) inset;
    }

    /* Notification item hover */
    .notif-item {
        position: relative;
        transition: all 0.2s ease;
    }
    .notif-item::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #D5006D, #ff4d9e);
        border-radius: 0 2px 2px 0;
        transform: scaleY(0);
        transition: transform 0.2s ease;
    }
    .notif-item:hover::before { transform: scaleY(1); }
    .notif-item:hover { background: rgba(213, 0, 109, 0.04); }

    /* Profile menu item */
    .profile-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
        text-decoration: none;
        color: inherit;
    }
    .profile-item:hover {
        background: rgba(213, 0, 109, 0.06);
        transform: translateX(2px);
    }
    .profile-item.danger:hover {
        background: rgba(239, 68, 68, 0.06);
    }
    .profile-item .icon-wrap {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
        transition: transform 0.2s ease;
    }
    .profile-item:hover .icon-wrap { transform: scale(1.1); }

    /* Notification badge pulse */
    @keyframes badge-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
        50% { box-shadow: 0 0 0 5px rgba(239, 68, 68, 0); }
    }
    .badge-pulse { animation: badge-pulse 2s ease infinite; }

    /* Scrollbar for notifications */
    .notif-scroll::-webkit-scrollbar { width: 4px; }
    .notif-scroll::-webkit-scrollbar-track { background: transparent; }
    .notif-scroll::-webkit-scrollbar-thumb {
        background: rgba(213, 0, 109, 0.2);
        border-radius: 99px;
    }
    .notif-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(213, 0, 109, 0.4);
    }

    /* Toggle switch */
    .toggle-track {
        width: 38px; height: 22px;
        border-radius: 99px;
        background: #e5e7eb;
        position: relative;
        transition: background 0.25s ease;
        flex-shrink: 0;
    }
    .toggle-track.active { background: #D5006D; }
    .toggle-thumb {
        position: absolute;
        top: 3px; left: 3px;
        width: 16px; height: 16px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .toggle-track.active .toggle-thumb { transform: translateX(16px); }

    /* Dropdown open animation */
    [x-cloak] { display: none !important; }
</style>

<div class="nav-root" x-data="navigationComponent()" x-init="init()">
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
         style="background: linear-gradient(90deg, #C2005F 0%, #D5006D 40%, #C2005F 100%); box-shadow: 0 2px 20px rgba(194,0,95,0.3);"
         @scroll.window="isScrolled = (window.pageYOffset > 10)"
         :class="{ 'shadow-xl': isScrolled }">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Left Side -->
                <div class="flex items-center gap-3">
                    <button @click="$store.sidebar.toggle()"
                        class="p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition-all duration-200 active:scale-95">
                        <i class="fas fa-bars text-base"></i>
                    </button>

                    <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 group">
                        <div class="h-9 w-9 rounded-xl bg-white flex items-center justify-center shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-200">
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="GAS Logo" class="h-6 w-6 object-contain" />
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight hidden sm:block">GAS System</span>
                    </a>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-2">

                    <!-- ─── Notification Bell ─── -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; open && markAllRead()"
                            class="relative p-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition-all duration-200 active:scale-95">
                            <i class="fas fa-bell text-base"></i>
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount > 9 ? '9+' : unreadCount"
                                  class="badge-pulse absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="glass-dropdown absolute right-0 top-[calc(100%+12px)] w-[340px] rounded-2xl overflow-hidden z-50">

                            <!-- Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(213,0,109,0.1);">
                                        <i class="fas fa-bell text-xs" style="color: #D5006D;"></i>
                                    </div>
                                    <span class="text-sm font-700 text-gray-800 font-bold">Notifications</span>
                                    <span x-show="unreadCount > 0"
                                          x-text="unreadCount"
                                          class="px-1.5 py-0.5 text-[10px] font-bold text-white rounded-full leading-none"
                                          style="background: #D5006D;">
                                    </span>
                                </div>
                                <div x-show="loading" class="flex items-center gap-1.5">
                                    <div class="w-3.5 h-3.5 border-2 border-pink-300 border-t-pink-600 rounded-full animate-spin"></div>
                                    <span class="text-[11px] text-pink-600">Syncing</span>
                                </div>
                            </div>

                            <!-- Notification List -->
                            <div class="notif-scroll max-h-[360px] overflow-y-auto">
                                <template x-for="notif in notifications" :key="notif.id">
                                    <a :href="notif.link || '#'"
                                       @click="handleNotificationClick($event, notif)"
                                       class="notif-item flex items-start gap-3 px-4 py-3.5 border-b border-gray-50 no-underline">

                                        <!-- Icon -->
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors duration-200"
                                             :class="notif.is_read
                                                 ? 'bg-gray-100'
                                                 : 'bg-pink-100'">
                                            <i class="fas fa-bell text-xs transition-colors duration-200"
                                               :class="notif.is_read ? 'text-gray-400' : 'text-pink-600'"></i>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[13px] text-gray-800 leading-snug">
                                                <span class="font-semibold" x-text="notif.title"></span>
                                                <span class="text-gray-500"> — </span>
                                                <span x-text="notif.message"></span>
                                            </p>
                                            <span class="text-[11px] text-gray-400 mt-1 block" x-text="notif.created_at"></span>
                                        </div>

                                        <!-- Unread dot -->
                                        <div class="flex-shrink-0 mt-2">
                                            <div x-show="!notif.is_read"
                                                 class="w-2 h-2 rounded-full"
                                                 style="background: #D5006D;"></div>
                                            <i x-show="notif.is_read"
                                               class="fas fa-check text-[10px] text-green-400"></i>
                                        </div>
                                    </a>
                                </template>

                                <!-- Empty state -->
                                <div x-show="notifications.length === 0" class="flex flex-col items-center justify-center py-10 px-4 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mb-3">
                                        <i class="fas fa-bell-slash text-gray-300 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">You're all caught up!</p>
                                    <p class="text-xs text-gray-400 mt-1">No new notifications</p>
                                </div>
                            </div>

                            <!-- Footer -->
                            <a href="{{ route('notifications.index') }}"
                               class="flex items-center justify-center gap-2 px-4 py-3 text-xs font-semibold border-t border-gray-100 transition-colors duration-200 no-underline"
                               style="color: #D5006D;"
                               onmouseover="this.style.background='rgba(213,0,109,0.05)'"
                               onmouseout="this.style.background=''">
                                View all notifications
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>

                    <!-- ─── Profile Menu ─── -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen"
                            class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-xl hover:bg-white/15 transition-all duration-200 active:scale-95 group">

                            <!-- Avatar -->
                            <div class="w-8 h-8 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center">
                                <span class="text-xs font-bold text-white" x-text="userInitial">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </span>
                            </div>

                            <div class="hidden sm:block text-left">
                                <p class="text-xs font-semibold text-white leading-tight" x-text="userName">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-white/60 leading-tight capitalize">{{ Auth::user()->role }}</p>
                            </div>

                            <i class="fas fa-chevron-down text-[10px] text-white/60 transition-transform duration-200"
                               :class="{ 'rotate-180': profileOpen }"></i>
                        </button>

                        <!-- Profile Dropdown -->
                        <div x-show="profileOpen"
                             @click.away="profileOpen = false"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="glass-dropdown absolute right-0 top-[calc(100%+12px)] w-[260px] rounded-2xl overflow-hidden z-50">

                            <!-- Profile Header -->
                            <div class="px-4 pt-4 pb-3 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-white text-sm shadow-md"
                                             style="background: linear-gradient(135deg, #D5006D, #8C0047);">
                                            <span x-text="userInitial">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800" x-text="userName">{{ Auth::user()->name }}</p>
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold text-white rounded-full mt-0.5 capitalize"
                                              style="background: linear-gradient(135deg, #D5006D, #B0005A);">
                                            {{ Auth::user()->role }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="px-2 py-2 space-y-0.5">
                                <a :href="profileEditRoute" class="profile-item">
                                    <div class="icon-wrap" style="background: rgba(59,130,246,0.1);">
                                        <i class="fas fa-user-circle" style="color: #3B82F6;"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">My Profile</p>
                                        <p class="text-[11px] text-gray-400">Account settings</p>
                                    </div>
                                </a>

                                <!-- Dark Mode Toggle -->
                                <button @click="$store.darkMode.toggle()" class="profile-item">
                                    <div class="icon-wrap" style="background: rgba(139,92,246,0.1);">
                                        <i :class="$store.darkMode.on ? 'fas fa-sun text-amber-400' : 'fas fa-moon text-violet-500'"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700" x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'"></p>
                                        <p class="text-[11px] text-gray-400">Toggle appearance</p>
                                    </div>
                                    <!-- Toggle -->
                                    <div class="toggle-track ml-auto" :class="{ 'active': $store.darkMode.on }">
                                        <div class="toggle-thumb"></div>
                                    </div>
                                </button>

                                <!-- Divider -->
                                <div class="my-1.5 border-t border-gray-100 mx-1"></div>

                                <!-- Logout -->
                                <button @click="logout()" class="profile-item danger">
                                    <div class="icon-wrap" style="background: rgba(239,68,68,0.1);">
                                        <i class="fas fa-arrow-right-from-bracket" style="color: #EF4444;"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Sign Out</p>
                                        <p class="text-[11px] text-gray-400">End session</p>
                                    </div>
                                </button>
                            </div>

                            <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                                <p class="text-[10px] text-gray-300 font-medium tracking-wider uppercase">GAS System v2.0</p>
                            </div>
                        </div>
                    </div>
                </div><!-- end right side -->
            </div>
        </div>
    </nav>
</div>

<script>
function navigationComponent() {
    return {
        isScrolled: false,
        profileOpen: false,
        loading: false,

        userName: @json(Auth::check() ? Auth::user()->name : 'Guest'),
        userInitial: @json(Auth::check() ? substr(Auth::user()->name, 0, 1) : 'G'),
        unreadCount: @json($unreadCount ?? 0),
        notifications: @json($navNotifications),

        profileEditRoute: '{{ route("profile.edit") }}',
        markAllReadRoute: '{{ route("notifications.readAll") }}',
        markReadRoute: '{{ route("notifications.read", ":id") }}',
        logoutRoute: '{{ route("logout") }}',
        csrfToken: '{{ csrf_token() }}',

        init() {
            this.updateUnreadCount();
        },

        async markAllRead() {
            if (!this.notifications.some(n => !n.is_read)) return;
            this.loading = true;
            try {
                const res = await fetch(this.markAllReadRoute, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.updateUnreadCount();
                }
            } catch(e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        handleNotificationClick(event, notif) {
            if (!notif.is_read) {
                notif.is_read = true;
                this.updateUnreadCount();
            }
        },

        updateUnreadCount() {
            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        },

        logout() {
            if (confirm('Are you sure you want to sign out?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.logoutRoute;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = this.csrfToken;
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
}
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>