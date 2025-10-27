<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Gamified Accounting System') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Theme toggle functionality
            function initTheme() {
                if (localStorage.getItem('theme') === 'dark' || 
                    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            }

            function toggleTheme() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }

            // Initialize theme immediately
            initTheme();
        </script>
    </head>
    <body class="bg-gradient-to-br from-[#FFE4F3] via-[#FFEEF2] to-[#FFF0F5] dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border-b border-gray-200/20 dark:border-gray-700/20 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 sm:h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex items-center gap-2 sm:gap-4">
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="GAS Logo" 
                                 class="w-10 h-10 sm:w-12 sm:h-12 object-contain transform hover:scale-110 transition-transform duration-300">
                            <div>
                                <span class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-[#D5006D] to-[#FF6F91] bg-clip-text text-transparent">GAS</span>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 -mt-1">Gamified Accounting</div>
                            </div>
                        </div>
                    </div>                    

                    <!-- Auth Buttons -->
                    @if (Route::has('login'))
                        <div class="flex items-center gap-2 sm:gap-4">
                            @auth
                                @php
                                    $role = Auth::user()->role ?? null;
                                    $dashboardRoute = match($role) {
                                        'admin' => 'admin.dashboard',
                                        'instructor' => 'instructors.dashboard',
                                        'student' => 'students.dashboard',
                                        default => 'dashboard'
                                    };
                                @endphp
                                <a href="{{ route($dashboardRoute) }}" 
                                   class="px-4 sm:px-6 py-3 rounded-xl bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white hover:from-[#FF6F91] hover:to-[#D5006D] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 font-semibold text-sm sm:text-base">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="px-4 sm:px-6 py-2 text-gray-600 dark:text-gray-300 hover:text-[#D5006D] transition-colors font-medium hidden sm:block">
                                    
                                </a>
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" 
                                       class="px-4 sm:px-6 py-3 rounded-xl bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white hover:from-[#FF6F91] hover:to-[#D5006D] transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 font-semibold text-sm sm:text-base">
                                        Sign In
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-24 sm:pt-32 pb-16 sm:pb-20 overflow-hidden">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-20 right-20 w-32 h-32 bg-[#FF9AAB]/30 rounded-full mix-blend-multiply filter blur-xl animate-float"></div>
                <div class="absolute top-40 left-20 w-24 h-24 bg-[#FF6F91]/30 rounded-full mix-blend-multiply filter blur-xl animate-float-delay"></div>
                <div class="absolute bottom-20 right-40 w-20 h-20 bg-[#FF9AAB]/40 rounded-full mix-blend-multiply filter blur-xl animate-float-slow"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Main Hero Content -->
                    <div class="mb-8">
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-[#FF9AAB]/30 mb-6">
                            <span class="w-2 h-2 bg-[#D5006D] rounded-full animate-pulse mr-2"></span>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Revolutionary Learning Experience</span>
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold mb-6 sm:mb-8 leading-tight">
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#D5006D] to-[#FF6F91] block">
                                Master Accounting
                            </span>
                            <span class="text-4xl md:text-5xl text-gray-700 dark:text-gray-200 block mt-2">
                                Through Epic Gameplay
                            </span>
                        </h1>
                        
                        <p class="text-lg sm:text-xl md:text-2xl text-white mb-8 sm:mb-12 max-w-4xl mx-auto leading-relaxed">
                            Transform your learning journey with our gamified accounting system. Earn XP, unlock achievements, and master financial concepts through interactive challenges designed for the modern learner.
                        </p>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row justify-center gap-4 md:gap-6 mb-16">
                        <a href="{{ route('login') }}" 
                           class="group px-8 py-4 rounded-xl bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white text-lg font-semibold hover:from-[#FF6F91] hover:to-[#D5006D] transform hover:scale-105 transition-all duration-200 shadow-xl hover:shadow-2xl">
                            <i class="fas fa-rocket mr-2 group-hover:animate-bounce"></i>
                            Start Your Journey
                        </a>
                        <a href="#features" 
                           class="px-8 py-4 rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-2 border-[#FF9AAB] text-[#D5006D] text-lg font-semibold hover:bg-[#FF9AAB] hover:text-white transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <i class="fas fa-compass mr-2"></i>
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-16 sm:py-20 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold bg-gradient-to-r from-[#D5006D] to-[#FF6F91] bg-clip-text text-transparent mb-4">
                        Why Choose GAS?
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Experience the future of accounting education with our innovative features designed to make learning engaging, effective, and enjoyable.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <!-- Feature 1 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#D5006D] to-[#FF6F91] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-gamepad text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Gamified Learning</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Transform complex accounting concepts into exciting quests and challenges. Level up your skills through interactive gameplay that makes learning addictive.
                        </p>
                        <div class="mt-4 flex items-center text-[#D5006D] font-semibold">
                            <span>Learn More</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#D5006D] to-[#FF6F91] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Smart Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Track your progress with detailed analytics and personalized insights. Identify strengths and areas for improvement with AI-powered recommendations.
                        </p>
                        <div class="mt-4 flex items-center text-[#D5006D] font-semibold">
                            <span>Learn More</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/30">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#D5006D] to-[#FF6F91] rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                            <i class="fas fa-trophy text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Achievement System</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Earn badges, unlock achievements, and compete with peers. Our comprehensive reward system keeps you motivated throughout your learning journey.
                        </p>
                        <div class="mt-4 flex items-center text-[#D5006D] font-semibold">
                            <span>Learn More</span>
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Developers Team Section -->
        <div class="py-20 bg-gradient-to-r from-[#D5006D]/10 to-[#FF9AAB]/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-6">
                        Meet Our Development Team
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-800 mb-8 max-w-3xl mx-auto">
                        The passionate individuals behind the Gamified Accounting System, dedicated to revolutionizing education through innovation.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Developer 1 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/50 text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg overflow-hidden ring-4 ring-[#D5006D]/20">
                            <img src="{{ asset('images/team/nica.jpg') }}" alt="Nica Christina P. Aguelo" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Nica Christina P. Aguelo</h3>
                        <p class="text-[#D5006D] font-semibold mb-3">Full Stack Engineer</p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Architecting innovative solutions and leading the technical vision of GAS.
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#D5006D] hover:text-white transition-colors">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#D5006D] hover:text-white transition-colors">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Developer 2 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/50 text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg overflow-hidden ring-4 ring-[#FF6F91]/20">
                            <img src="{{ asset('images/team/rovi.jpg') }}" alt="Rovi Hannz Tabigne" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Rovi Hannz Tabigne</h3>
                        <p class="text-[#D5006D] font-semibold mb-3">Design Lead</p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Crafting beautiful and intuitive user experiences that delight.
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#FF6F91] hover:text-white transition-colors">
                                <i class="fab fa-dribbble"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#FF6F91] hover:text-white transition-colors">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Developer 3 -->
                    <div class="group bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-white/50 hover:border-[#FF9AAB]/50 text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg overflow-hidden ring-4 ring-[#D5006D]/20">
                            <img src="{{ asset('images/team/rako.jpg') }}" alt="Ton Rako" class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Ton Rako</h3>
                        <p class="text-[#D5006D] font-semibold mb-3">Systems Architect</p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Building robust and scalable backend infrastructure.
                        </p>
                        <div class="flex justify-center gap-3">
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#D5006D] hover:text-white transition-colors">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="#" class="w-8 h-8 bg-[#FAF3F3] dark:bg-gray-700 rounded-lg flex items-center justify-center hover:bg-[#D5006D] hover:text-white transition-colors">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-6">
                        Want to join our team or collaborate on a project?
                    </p>
                    <a href="#" 
                    class="inline-flex items-center px-8 py-4 rounded-xl bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white text-lg font-semibold hover:from-[#FF6F91] hover:to-[#D5006D] transform hover:scale-105 transition-all duration-200 shadow-xl hover:shadow-2xl">
                        <i class="fas fa-envelope mr-2"></i>
                        Get In Touch
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-t border-gray-200/20 dark:border-gray-700/20 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center mb-6 md:mb-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#D5006D] to-[#FF6F91] rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-calculator text-white"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold bg-gradient-to-r from-[#D5006D] to-[#FF6F91] bg-clip-text text-transparent">
                                Gamified Accounting System
                            </span>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Making learning fun and effective</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} GAS. All rights reserved. Made with ❤️ for learners.
                    </div>
                </div>
            </div>
        </footer>

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            @keyframes float-delay {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            @keyframes float-slow {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            .animate-float { animation: float 6s ease-in-out infinite; }
            .animate-float-delay { animation: float-delay 4s ease-in-out infinite 2s; }
            .animate-float-slow { animation: float-slow 8s ease-in-out infinite 1s; }

            /* Additional responsive styles */
            @media (max-width: 640px) {
                .container {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }
                
                .hero-text {
                    font-size: clamp(2rem, 5vw, 4rem);
                }
                
                .feature-card {
                    padding: 1.5rem;
                }
            }
        </style>
    </body>
</html>