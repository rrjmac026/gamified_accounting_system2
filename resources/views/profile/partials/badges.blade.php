<style>
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
    
    .badge-locked {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px dashed rgba(156, 163, 175, 0.5);
        filter: grayscale(40%);
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 30px rgba(34, 197, 94, 0.4); }
        50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.6); }
    }

    /* Toggle Switch */
    .toggle-wrapper {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 24px;
        background: #e5e7eb;
        border-radius: 9999px;
        transition: all 0.3s;
    }
    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 9999px;
        transition: all 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-switch {
        background: #34d399;
    }
    input:checked + .toggle-switch::after {
        transform: translateX(26px);
    }

    .privacy-form {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid rgba(156, 163, 175, 0.2);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .student-badge-icon {
        position: relative;
        width: 48px;
        height: 48px;
        margin: 0 auto;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .student-badge-icon img {
        width: 100%;
        height: 100%;
        object-cover;
        border-radius: 50%;
    }
</style>

<section>
    <header class="flex justify-between items-start">
        <div>
            <h2 class="text-2xl font-semibold text-[#FF92C2]">
                <i class="fas fa-trophy mr-2"></i>My Achievements
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Unlock badges as you progress and gain XP!
            </p>
        </div>
        
        <!-- Experience Level Display -->
        <div class="bg-gradient-to-r from-[#FF92C2] to-purple-500 text-white px-4 py-2 rounded-full shadow-lg">
            <div class="flex items-center space-x-2">
                <i class="fas fa-star text-yellow-300"></i>
                <span class="font-bold">Level {{ $student->level ?? 1 }}</span>
            </div>
            <div class="text-xs opacity-90 text-center">
                {{ $totalXp ?? 0 }} XP
            </div>
        </div>
    </header>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>
            @foreach($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <!-- Leaderboard Privacy Toggle -->
    <div class="privacy-form mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">
            <i class="fas fa-shield-alt mr-2"></i>Privacy Settings
        </h3>
        
        <form id="privacyForm" action="{{ route('students.updateLeaderboardPrivacy') }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <label class="toggle-wrapper" for="hide_from_leaderboard">
                        <input type="checkbox" 
                               id="hide_from_leaderboard"
                               name="hide_from_leaderboard" 
                               class="sr-only" 
                               {{ $student->hide_from_leaderboard ? 'checked' : '' }}
                               onchange="updateToggleState()">
                        <span class="toggle-switch"></span>
                    </label>
                    <div>
                        <label for="hide_from_leaderboard" class="text-gray-700 font-medium cursor-pointer">
                            Hide my name on the leaderboard
                        </label>
                        <p class="text-sm text-gray-500">
                            When enabled, your achievements will be private and won't appear on public rankings.
                        </p>
                    </div>
                </div>
                
                <button type="submit" id="saveButton"
                        class="badge-card ml-4 bg-gradient-to-r from-pink-200 to-pink-100 border-pink-300 px-6 py-2 rounded-2xl text-pink-700 font-semibold shadow-md hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6">
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($badges as $badge)
                <div class="badge-card {{ $badge->earned ? 'badge-earned' : 'badge-locked' }} p-4 rounded-2xl text-center group">
                    <div class="relative mb-3">
                        <div class="student-badge-icon {{ $badge->earned ? 'bg-white' : 'bg-gray-100' }}">
                            @if($badge->icon_path)
                                <img src="{{ asset('storage/' . $badge->icon_path) }}" 
                                     alt="{{ $badge->name }}" 
                                     class="{{ $badge->earned ? '' : 'grayscale' }}"
                                     onerror="this.onerror=null; this.outerHTML='<div class=\'w-full h-full rounded-full bg-gradient-to-br from-pink-200 to-purple-200 flex items-center justify-center\'><i class=\'fas fa-medal {{ $badge->earned ? 'text-yellow-500' : 'text-gray-400' }} text-lg\'></i></div>';">
                            @else
                                <div class="w-full h-full rounded-full bg-gradient-to-br from-pink-200 to-purple-200 flex items-center justify-center">
                                    <i class="fas fa-medal {{ $badge->earned ? 'text-yellow-500' : 'text-gray-400' }} text-lg"></i>
                                </div>
                            @endif
                        </div>
                        
                        @if($badge->earned)
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h3 class="font-bold {{ $badge->earned ? 'text-green-800' : 'text-gray-700' }} text-sm mb-1">
                        {{ $badge->name }}
                    </h3>
                    <p class="{{ $badge->earned ? 'text-green-700' : 'text-gray-500' }} text-xs mb-2">
                        {{ $badge->description }}
                    </p>

                    @if($badge->earned)
                        <div class="bg-green-500/20 border border-green-300 rounded-full px-2 py-1">
                            <span class="text-green-800 text-xs font-medium">
                                <i class="fas fa-calendar-check mr-1"></i>
                                Earned
                            </span>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-full px-2 py-1">
                            <span class="text-gray-500 text-xs">
                                <i class="fas fa-lock mr-1"></i>
                                Locked
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <div class="text-[#FF92C2] mb-2">
                        <i class="fas fa-trophy text-4xl opacity-50"></i>
                    </div>
                    <p class="text-gray-500">No badges earned yet. Keep going!</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Badge card click effects
            const badgeCards = document.querySelectorAll('.badge-card:not(#saveButton)');
            badgeCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });

            // Form submission handling
            const form = document.getElementById('privacyForm');
            const saveButton = document.getElementById('saveButton');
            
            form.addEventListener('submit', function() {
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            });
        });

        function updateToggleState() {
            const checkbox = document.getElementById('hide_from_leaderboard');
            const saveButton = document.getElementById('saveButton');
            
            // Visual feedback that changes are pending
            saveButton.classList.add('bg-gradient-to-r', 'from-blue-200', 'to-blue-100', 'border-blue-300', 'text-blue-700');
            saveButton.classList.remove('from-pink-200', 'to-pink-100', 'border-pink-300', 'text-pink-700');
            
            // Optional: Show immediate feedback
            console.log('Checkbox state changed:', checkbox.checked);
        }
    </script>
</section>