<x-app-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .badge-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
            border: 2px solid rgba(236, 72, 153, 0.2);
            box-shadow: 0 8px 32px rgba(236, 72, 153, 0.1);
        }
        
        .badge-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(236, 72, 153, 0.2);
            border-color: rgba(236, 72, 153, 0.4);
        }
        
        .badge-earned {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border: 2px solid #22c55e;
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
            animation: pulse-glow 2s infinite;
        }
        
        .badge-earned:hover {
            box-shadow: 0 20px 40px rgba(34, 197, 94, 0.3);
        }
        
        .badge-locked {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed rgba(156, 163, 175, 0.5);
            filter: grayscale(40%);
        }
        
        .badge-locked:hover {
            filter: grayscale(20%);
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-color: rgba(156, 163, 175, 0.7);
        }
        
        .badge-near-complete {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.2);
        }
        
        .badge-near-complete:hover {
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.3);
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 30px rgba(34, 197, 94, 0.4); }
            50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.6); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating { animation: float 3s ease-in-out infinite; }
        
        .progress-bar {
            background: linear-gradient(90deg, #ec4899, #f97316);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
            transition: width 1s ease-in-out;
        }
        
        .success-message {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            animation: slideIn 0.6s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.3);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .badge-icon-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .badge-icon-container img {
            width: 100%;
            height: 100%;
            object-cover;
            border-radius: 50%;
        }
    </style>

    <div class="py-8 px-4">
        <!-- Floating Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-pink-100 rounded-full opacity-30 floating" style="animation-delay: 0s;"></div>
            <div class="absolute top-3/4 right-1/4 w-48 h-48 bg-purple-100 rounded-full opacity-30 floating" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-blue-100 rounded-full opacity-30 floating" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto relative">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="glass-panel p-8 mb-8">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2">
                        <i class="fas fa-trophy mr-4 text-4xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2"></i>My Achievements
                    </h1>
                    <p class="text-gray-600 text-lg">Unlock your potential and showcase your accomplishments</p>
                    
                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="glass-panel p-4">
                            <div class="text-3xl font-bold text-gray-800">{{ $totalXp }}</div>
                            <div class="text-gray-600">Total XP</div>
                        </div>
                        <div class="glass-panel p-4">
                            <div class="text-3xl font-bold text-gray-800">{{ $badges->where('earned', true)->count() }}/{{ $badges->count() }}</div>
                            <div class="text-gray-600">Badges Earned</div>
                        </div>
                        <div class="glass-panel p-4">
                            <div class="text-3xl font-bold text-gray-800">Level {{ floor($totalXp / 1000) + 1 }}</div>
                            <div class="text-gray-600">Current Level</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-8">
                    <div class="success-message p-6 rounded-2xl text-white shadow-2xl">
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-300 text-2xl mr-4"></i>
                            <div>
                                <h3 class="font-semibold text-lg">Congratulations!</h3>
                                <p class="opacity-90">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Badges Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($badges as $badge)
        @php
            $isNearComplete = !$badge->earned && 
                ((is_array($badge->criteria) && in_array('xp', $badge->criteria)) || $badge->criteria == 'xp') && 
                $totalXp >= ($badge->xp_threshold * 0.8);
        @endphp

                    <div class="badge-card {{ $badge->earned ? 'badge-earned' : ($isNearComplete ? 'badge-near-complete' : 'badge-locked') }} p-6 rounded-2xl text-center group">
                        <div class="relative mb-4">
                            <div class="badge-icon-container {{ $badge->earned ? 'bg-white' : 'bg-gray-100' }}">
                                @if($badge->icon_path)
                                    <img src="{{ asset('storage/' . $badge->icon_path) }}" 
                                         alt="{{ $badge->name }}" 
                                         class="{{ $badge->earned ? '' : 'grayscale' }}"
                                         onerror="this.onerror=null; this.outerHTML='<div class=\'w-full h-full rounded-full bg-gradient-to-br from-pink-200 to-purple-200 flex items-center justify-center\'><i class=\'fas fa-medal {{ $badge->earned ? 'text-yellow-500' : 'text-gray-400' }} text-3xl\'></i></div>';">
                                @else
                                    <div class="w-full h-full rounded-full bg-gradient-to-br from-pink-200 to-purple-200 flex items-center justify-center">
                                        <i class="fas fa-medal {{ $badge->earned ? 'text-yellow-500' : 'text-gray-400' }} text-3xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            @if($badge->earned)
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            @elseif((is_array($badge->criteria) && in_array('xp', $badge->criteria)) || $badge->criteria == 'xp')
                                @if($totalXp >= $badge->xp_threshold * 0.8)
                                    <div class="absolute -top-1 -right-1 animate-bounce">
                                        <div class="w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">!</span>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <h3 class="font-bold {{ $badge->earned ? 'text-green-800' : ($isNearComplete ? 'text-orange-800' : 'text-gray-700') }} text-lg mb-2">
                            {{ $badge->name }}
                        </h3>
                        <p class="{{ $badge->earned ? 'text-green-700' : ($isNearComplete ? 'text-orange-700' : 'text-gray-500') }} text-sm mb-4">
                            {{ $badge->description }}
                        </p>

                        @if($badge->earned)
                            <div class="bg-green-500/20 border border-green-300 rounded-full px-4 py-2">
                                <span class="text-green-800 text-sm font-medium">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Earned {{ \Carbon\Carbon::parse($badge->earned_at)->format('M d, Y') }}
                                </span>
                            </div>
                        @else
                            @if((is_array($badge->criteria) && in_array('xp', $badge->criteria)) || $badge->criteria == 'xp')
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Progress</span>
                                        <span class="font-semibold text-pink-600">{{ $totalXp }} / {{ $badge->xp_threshold }} XP</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div class="progress-bar h-full rounded-full" 
                                             style="width: {{ min(100, ($totalXp / $badge->xp_threshold) * 100) }}%"></div>
                                    </div>
                                    @if($totalXp < $badge->xp_threshold)
                                        <p class="{{ $isNearComplete ? 'text-orange-700' : 'text-pink-600' }} text-sm font-medium">
                                            {{ $badge->xp_threshold - $totalXp }} XP to go!
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="bg-gray-100 rounded-full px-4 py-2">
                                    <span class="text-gray-500 text-sm">
                                        <i class="fas fa-lock mr-2"></i>
                                        Locked
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Footer Section -->
            <div class="text-center mt-16">
                <div class="glass-panel p-6">
                    <p class="text-gray-600">Keep pushing your limits and unlock more achievements!</p>
                    <div class="flex justify-center space-x-4 mt-4">
                        <div class="w-3 h-3 bg-pink-400 rounded-full animate-pulse"></div>
                        <div class="w-3 h-3 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                        <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bars on load
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });

            // Add click interactions
            const badgeCards = document.querySelectorAll('.badge-card');
            badgeCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Add sparkle effect for earned badges
            function createSparkle(element) {
                const sparkle = document.createElement('div');
                sparkle.className = 'absolute w-2 h-2 bg-yellow-300 rounded-full pointer-events-none';
                sparkle.style.left = Math.random() * 100 + '%';
                sparkle.style.top = Math.random() * 100 + '%';
                sparkle.style.animation = 'sparkle 1.5s ease-out forwards';
                
                element.appendChild(sparkle);
                
                setTimeout(() => {
                    sparkle.remove();
                }, 1500);
            }

            // Add sparkle animation to CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes sparkle {
                    0% { opacity: 1; transform: scale(0); }
                    50% { opacity: 1; transform: scale(1); }
                    100% { opacity: 0; transform: scale(0); }
                }
            `;
            document.head.appendChild(style);

            // Create sparkles on earned badges periodically
            const earnedBadges = document.querySelectorAll('.badge-earned');
            earnedBadges.forEach(badge => {
                setInterval(() => {
                    createSparkle(badge);
                }, 2000 + Math.random() * 3000);
            });
        });
    </script>
</x-app-layout>