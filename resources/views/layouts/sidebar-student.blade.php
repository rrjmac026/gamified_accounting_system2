<nav class="p-4 space-y-4">
    {{-- Resolve current task + its enabled steps once, used throughout the sidebar --}}
    @php
        $currentTaskId = request()->route('id')
            ?? request()->route('taskId')
            ?? optional(auth()->user()->student->performanceTasks()->latest()->first())->id;

        $currentTask = $currentTaskId
            ? \App\Models\PerformanceTask::find($currentTaskId)
            : null;

        // Only the steps the instructor enabled; falls back to all 10 for legacy tasks
        $enabledSteps = $currentTask ? $currentTask->enabled_steps_list : [];

        $allStepTitles = [
            1  => 'Analyze Transactions',
            2  => 'Journalize Transactions',
            3  => 'Post to Ledger Accounts',
            4  => 'Prepare Trial Balance',
            5  => 'Journalize & Post Adjusting Entries',
            6  => 'Prepare Adjusted Trial Balance',
            7  => 'Prepare Financial Statements',
            8  => 'Journalize & Post Closing Entries',
            9  => 'Prepare Post-Closing Trial Balance',
            10 => 'Reverse (Optional Step)',
        ];

        // Short labels for the sidebar
        $stepShortLabels = [
            1  => 'Analyze Transactions',
            2  => 'Journalize Transactions',
            3  => 'Post to Ledger',
            4  => 'Trial Balance',
            5  => 'Adjusting Entries',
            6  => 'Adjusted Trial Balance',
            7  => 'Financial Statements',
            8  => 'Closing Entries',
            9  => 'Post-Closing Trial Balance',
            10 => 'Reverse (Optional)',
        ];
    @endphp

    <!-- ── Main Menu ─────────────────────────────────────────────────── -->
    <div class="mb-8">
        <span class="px-3 text-xs font-semibold text-[#595758] dark:text-[#FFC8FB] uppercase tracking-wider border-l-4 border-[#FF92C2] bg-gradient-to-r from-[#FF92C2]/10 to-transparent rounded-r-lg py-2">
            Menu
        </span>
        <div class="mt-3 space-y-2">
            <a href="{{ route('students.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.dashboard') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-home w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>Dashboard</span>
                @if(request()->routeIs('students.dashboard'))
                    <div class="ml-auto w-2 h-2 bg-[#FF92C2] rounded-full animate-pulse"></div>
                @endif
            </a>
            <a href="{{ route('students.subjects.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.subjects.*') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-book w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>Subjects</span>
            </a>
        </div>
    </div>

    <!-- ── Assessment ────────────────────────────────────────────────── -->
    <div class="mb-6">
        <span class="px-3 text-xs font-semibold text-[#595758] dark:text-[#FFC8FB] uppercase tracking-wider border-l-4 border-[#FF92C2] bg-gradient-to-r from-[#FF92C2]/10 to-transparent rounded-r-lg py-2">
            Assessment
        </span>
        <div class="mt-3 space-y-2">

            {{-- Performance Tasks top-level link --}}
            <a href="{{ route('students.performance-tasks.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.performance-tasks.*') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-tasks w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                </div>
                <span>Performance Tasks</span>
            </a>

            {{-- Steps dropdown — only shown when a task is in context --}}
            @if($currentTaskId)
                <div class="mb-2">
                    {{-- Section label with step count --}}
                    <div class="flex items-center justify-between px-3 mb-2 mt-3">
                        <span class="text-xs font-semibold text-[#595758] dark:text-[#FFC8FB] uppercase tracking-wider border-l-4 border-[#FF92C2] bg-gradient-to-r from-[#FF92C2]/10 to-transparent rounded-r-lg py-2">
                            Steps
                        </span>
                        @if($currentTask)
                            <span class="text-[10px] font-medium text-[#D5006D] bg-pink-50 border border-pink-200 rounded-full px-2 py-0.5">
                                {{ count($enabledSteps) }} active
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1">
                        {{-- Dropdown toggle --}}
                        <button onclick="togglePerformanceSteps()" 
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
                                       text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]
                                       {{ request()->routeIs('students.performance-tasks.step') || request()->routeIs('students.performance-tasks.progress') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 shadow-lg border border-[#FF92C2]/20' : '' }}">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-table w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                                <span>Task Steps</span>
                            </div>
                            <i id="dropdown-icon" class="fas fa-chevron-down text-xs transition-transform duration-300"></i>
                        </button>

                        <div id="performance-steps" class="ml-4 space-y-1 hidden">

                            {{-- Progress overview link --}}
                            <a href="{{ route('students.performance-tasks.progress', ['taskId' => $currentTaskId]) }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm rounded-lg transition-all duration-300 hover:bg-[#FFEEF2]
                               {{ request()->routeIs('students.performance-tasks.progress') ? 'bg-[#FFC8FB]/20 text-[#595758] font-medium' : 'text-[#595758]/70 dark:text-[#FF92C2]/70' }}">
                                <span class="w-6 h-6 rounded-full bg-[#FF92C2]/20 flex items-center justify-center text-xs font-bold">
                                    <i class="fas fa-chart-line text-[#FF92C2]"></i>
                                </span>
                                <span>Overview</span>
                            </a>

                            {{-- Only enabled steps --}}
                            @foreach($enabledSteps as $stepNum)
                                <a href="{{ route('students.performance-tasks.step', ['id' => $currentTaskId, 'step' => $stepNum]) }}" 
                                   class="flex items-center gap-3 px-4 py-2 text-sm rounded-lg transition-all duration-300 hover:bg-[#FFEEF2]
                                   {{ request()->routeIs('students.performance-tasks.step') && (int) request()->route('step') === $stepNum
                                       ? 'bg-[#FFC8FB]/20 text-[#595758] font-medium'
                                       : 'text-[#595758]/70 dark:text-[#FF92C2]/70' }}">
                                    <span class="w-6 h-6 rounded-full bg-[#FF92C2]/20 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ $stepNum }}
                                    </span>
                                    <span class="truncate">{{ $stepShortLabels[$stepNum] ?? "Step $stepNum" }}</span>
                                </a>
                            @endforeach

                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- ── Progress ──────────────────────────────────────────────────── -->
    <div class="mb-6">
        <span class="px-3 text-xs font-semibold text-[#595758] dark:text-[#FFC8FB] uppercase tracking-wider border-l-4 border-[#FF92C2] bg-gradient-to-r from-[#FF92C2]/10 to-transparent rounded-r-lg py-2">
            Progress
        </span>
        <div class="mt-3 space-y-2">
            <a href="{{ route('students.progress') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.progress') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-chart-line w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>My Progress</span>
            </a>
            <a href="{{ route('students.achievements') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.achievements') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-trophy w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>Achievements</span>
            </a>
        </div>
    </div>

    <!-- ── Feedback & Evaluation ──────────────────────────────────────── -->
    <div class="mb-6">
        <span class="px-3 text-xs font-semibold text-[#595758] dark:text-[#FFC8FB] uppercase tracking-wider border-l-4 border-[#FF92C2] bg-gradient-to-r from-[#FF92C2]/10 to-transparent rounded-r-lg py-2">
            Feedback & Evaluation
        </span>
        <div class="mt-3 space-y-2">
            <a href="{{ route('students.feedback.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.feedback.*') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-comments w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>Feedback</span>
            </a>
            <a href="{{ route('students.evaluations.index') }}" 
               class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[0.98] group relative overflow-hidden
               {{ request()->routeIs('students.evaluation.*') ? 'bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2]/30 text-[#595758] shadow-lg border border-[#FF92C2]/20' : 'text-[#595758] dark:text-[#FF92C2] hover:bg-gradient-to-r hover:from-[#FFEEF2] hover:to-[#FFF0F5]' }}">
                <i class="fas fa-clipboard-check w-5 h-5 transition-transform duration-300 group-hover:scale-110"></i>
                <span>Evaluation</span>
            </a>
        </div>
    </div>
</nav>

<script>
    function togglePerformanceSteps() {
        const dropdown = document.getElementById('performance-steps');
        const icon     = document.getElementById('dropdown-icon');
        dropdown.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Auto-expand when on any step or progress page
    document.addEventListener('DOMContentLoaded', function () {
        const path = window.location.pathname;
        if (path.includes('performance-tasks/step') || path.includes('performance-tasks/progress')) {
            const dropdown = document.getElementById('performance-steps');
            const icon     = document.getElementById('dropdown-icon');
            if (dropdown) {
                dropdown.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
        }
    });
</script>