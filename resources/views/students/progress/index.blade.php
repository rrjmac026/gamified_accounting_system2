<x-app-layout>
    <div class="py-6 sm:py-12 bg-gradient-to-br from-pink-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent mb-2">My Progress</h2>
                <p class="text-gray-600">Track your learning journey and achievements</p>
            </div>

            <!-- XP Progress Bar -->
            <div class="mb-8 bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-pink-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-lg font-semibold text-gray-800">Experience Points</p>
                        <p class="text-sm text-gray-600">Keep learning to level up!</p>
                    </div>
                    <div class="text-right">
                        @php
                            // Calculate current level and XP progress
                            $xpPerLevel = 1000;
                            $currentLevel = floor($totalXp / $xpPerLevel) + 1;
                            $xpInCurrentLevel = $totalXp % $xpPerLevel;
                            $nextLevelXp = $xpPerLevel;
                            $progressPercentage = ($xpInCurrentLevel / $nextLevelXp) * 100;
                        @endphp
                        <p class="text-2xl font-bold text-pink-600">{{ $xpInCurrentLevel }}</p>
                        <p class="text-sm text-gray-500">/ {{ $nextLevelXp }} XP</p>
                    </div>
                </div>
                <div class="relative w-full bg-gray-200 rounded-full h-6 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-pink-500 to-purple-500 h-6 rounded-full transition-all duration-1000 ease-out shadow-lg relative overflow-hidden"
                        style="width: {{ $progressPercentage }}%">
                        <div class="absolute inset-0 bg-white/30 animate-pulse"></div>
                    </div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span>Level {{ $currentLevel }} Progress</span>
                    <span>{{ $nextLevelXp - $xpInCurrentLevel }} XP to next level</span>
                </div>

                 <!-- 🔥 XP Breakdown Section -->
                <div class="mt-6">
                    <h3 class="text-md font-semibold text-pink-600 mb-2">XP Breakdown</h3>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>📘 Task Completion: <span class="font-bold">{{ $xpBreakdown['task_completion'] ?? 0 }}</span> XP</li>
                        <li>📝 Quiz Scores: <span class="font-bold">{{ $xpBreakdown['quiz_score'] ?? 0 }}</span> XP</li>
                        <li>🎯 Bonus Activities: <span class="font-bold">{{ $xpBreakdown['bonus_activity'] ?? 0 }}</span> XP</li>
                    </ul>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div class="group bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-green-100 text-center transform transition-all duration-300 hover:scale-105 hover:shadow-xl hover:-translate-y-2 cursor-pointer">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center transform transition-transform group-hover:rotate-12">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-green-600 mb-1">{{ $tasksCompleted }}</p>
                    <p class="text-sm text-gray-600 font-medium">Tasks Completed</p>
                    <div class="mt-2 h-1 bg-green-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full w-3/4 animate-pulse"></div>
                    </div>
                </div>
                
                <div class="group bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-blue-100 text-center transform transition-all duration-300 hover:scale-105 hover:shadow-xl hover:-translate-y-2 cursor-pointer">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center transform transition-transform group-hover:rotate-12">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-blue-600 mb-1">{{ $student->total_score }}</p>
                    <p class="text-sm text-gray-600 font-medium">Total Score</p>
                    <div class="mt-2 h-1 bg-blue-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full w-4/5 animate-pulse"></div>
                    </div>
                </div>
                
                <div class="group bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-yellow-100 text-center transform transition-all duration-300 hover:scale-105 hover:shadow-xl hover:-translate-y-2 cursor-pointer">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center transform transition-transform group-hover:rotate-12">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-yellow-600 mb-1">#{{ $leaderboardRank }}</p>
                    <p class="text-sm text-gray-600 font-medium">Leaderboard Rank</p>
                    <div class="mt-2 h-1 bg-yellow-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full w-2/3 animate-pulse"></div>
                    </div>
                </div>
                
                <div class="group bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-purple-100 text-center transform transition-all duration-300 hover:scale-105 hover:shadow-xl hover:-translate-y-2 cursor-pointer">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center transform transition-transform group-hover:rotate-12">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-purple-600 mb-1">{{ $currentLevel }}</p>
                    <p class="text-sm text-gray-600 font-medium">Level</p>
                    <div class="mt-2 h-1 bg-purple-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-400 to-pink-500 rounded-full animate-pulse" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .animate-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }
    </style>
</x-app-layout>