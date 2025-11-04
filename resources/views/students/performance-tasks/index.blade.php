<x-app-layout>

    @if (session('success'))
        <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Search Bar --}}
            <div class="mb-8">
                <div class="relative bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <i class="fas fa-search absolute left-7 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search performance tasks..."
                        class="w-full pl-10 border-0 focus:ring-2 focus:ring-[#FF92C2]/20 bg-transparent text-sm text-gray-700 placeholder:text-gray-400 rounded-lg"
                    />
                </div>
            </div>

            {{-- Performance Task List --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="taskList">
                @forelse ($performanceTasks as $task)
                    <a href="{{ route('students.performance-tasks.show', $task->id) }}"
                        class="group block bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-1">
                        
                        {{-- Colored Top Border Based on Status --}}
                        <div class="h-1 bg-gradient-to-r 
                            {{ $task->status === 'completed' || $task->status === 'graded' ? 'from-green-400 to-emerald-500' : 'from-[#FF92C2] to-[#FFC8FB]' }}">
                        </div>

                        <div class="p-6">
                            {{-- Header with Icon & Status Badge --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start gap-3 flex-1">
                                    <div class="bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] text-white p-2.5 rounded-lg shadow-sm shrink-0">
                                        <i class="fas fa-file-alt text-base"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-base text-gray-800 mb-1 line-clamp-2 group-hover:text-[#FF92C2] transition-colors">
                                            {{ $task->title }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <div class="mb-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full
                                    {{ $task->status === 'completed' || $task->status === 'graded' 
                                        ? 'bg-green-50 text-green-700 border border-green-200' 
                                        : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    <i class="fas {{ $task->status === 'completed' || $task->status === 'graded' ? 'fa-check-circle' : 'fa-clock' }}"></i>
                                    {{ ucfirst($task->status) ?? 'Pending' }}
                                </span>
                            </div>

                            {{-- Description --}}
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                {!! $task->description ?? '<p>No description available.</p>' !!}
                            </p>

                            {{-- Subject & Instructor Info --}}
                            <div class="space-y-2.5 mb-4">
                                @if($task->subject)
                                    <div class="flex items-center text-xs text-gray-600">
                                        <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center mr-2.5 shrink-0">
                                            <i class="fas fa-book text-purple-500 text-xs"></i>
                                        </div>
                                        <span class="font-medium truncate">{{ $task->subject->subject_name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                @if($task->instructor)
                                    <div class="flex items-center text-xs text-gray-600">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center mr-2.5 shrink-0">
                                            <i class="fas fa-user-tie text-blue-500 text-xs"></i>
                                        </div>
                                        <span class="truncate">{{ $task->instructor->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                @if($task->section)
                                    <div class="flex items-center text-xs text-gray-600">
                                        <div class="w-7 h-7 rounded-lg bg-pink-50 flex items-center justify-center mr-2.5 shrink-0">
                                            <i class="fas fa-users text-pink-500 text-xs"></i>
                                        </div>
                                        <span class="truncate">{{ $task->section->name ?? 'N/A' }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 my-4"></div>

                            {{-- Progress Bar --}}
                            @if($task->status !== 'graded' && $task->status !== 'completed')
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-semibold text-gray-700">Progress</span>
                                        <span class="text-xs text-gray-500">{{ $task->progress ?? 0 }}/{{ $task->totalSteps ?? 10 }} steps</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] h-2 rounded-full transition-all duration-500 shadow-sm" 
                                             style="width: {{ $task->progressPercentage ?? 0 }}%"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Final Grade Display (Improved) --}}
                            @if($task->status === 'graded' || $task->status === 'completed')
                                <div class="mb-4 p-4 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                                    {{-- Score Section --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-sm"></i>
                                            </div>
                                            <span class="font-semibold text-gray-800">Final Grade</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">
                                                {{ number_format($task->score ?? 0, 0) }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                out of {{ $task->max_score ?? 1000 }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- XP Earned Badge --}}
                                    <div class="flex items-center justify-between pt-3 border-t border-green-200">
                                        <span class="text-xs font-medium text-gray-600">XP Earned</span>
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-full border border-green-200">
                                            <i class="fas fa-star text-amber-400"></i>
                                            <span class="text-sm font-bold text-gray-800">{{ $task->xp_earned ?? $task->xp_reward ?? 0 }}</span>
                                            <span class="text-xs text-gray-500">XP</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Footer Info --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-[#FF92C2]/10 to-[#FFC8FB]/10 rounded-full">
                                    <i class="fas fa-star text-[#FF92C2] text-xs"></i>
                                    <span class="text-xs font-semibold text-gray-700">{{ $task->xp_reward ?? $task->xp ?? 0 }} XP</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-tasks text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No performance tasks assigned yet.</p>
                        <p class="text-gray-400 text-sm mt-1">Check back later for new assignments.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Client-side Search Filter --}}
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#taskList a').forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const desc = card.querySelector('p').textContent.toLowerCase();
                card.style.display = title.includes(query) || desc.includes(query) ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout>