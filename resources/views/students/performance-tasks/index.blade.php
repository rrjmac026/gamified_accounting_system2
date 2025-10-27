<x-app-layout>

            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Search Bar --}}
            <div class="mb-6">
                <div class="bg-white/80 backdrop-blur-sm p-4 rounded-2xl shadow-md border border-[#FFC8FB]/20 flex items-center gap-3">
                    <i class="fas fa-search text-[#FF92C2]"></i>
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search performance tasks..."
                        class="w-full border-0 focus:ring-0 bg-transparent text-sm text-gray-700 placeholder:text-gray-400"
                    />
                </div>
            </div>

            {{-- Performance Task List --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="taskList">
                @forelse ($performanceTasks as $task)
                    <a href="{{ route('students.performance-tasks.show', $task->id) }}"
                        class="group block bg-gradient-to-br from-white to-[#FFF6FB] hover:from-[#FFE6F1] hover:to-[#FFF0F9] p-6 rounded-2xl shadow-lg border border-[#FFC8FB]/30 transition-all duration-300 hover:scale-[0.99]">
                        
                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-[#FF92C2]/20 text-[#FF92C2] p-3 rounded-full">
                                    <i class="fas fa-file-alt text-lg"></i>
                                </div>
                                <h3 class="font-semibold text-lg text-[#595758]">
                                    {{ $task->title }}
                                </h3>
                            </div>

                            <span class="text-xs px-3 py-1 rounded-full 
                                {{ $task->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($task->status) ?? 'Pending' }}
                            </span>
                        </div>

                        {{-- Description --}}
                        <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                            {!! $task->description ?? '<p>No description available.</p>' !!}
                        </p>

                        {{-- Subject & Instructor Info --}}
                        <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                            @if($task->subject)
                                <div class="flex items-center text-xs text-gray-600">
                                    <i class="fas fa-book mr-2 text-[#FF92C2] w-4"></i>
                                    <span class="font-medium">{{ $task->subject->subject_name ?? 'N/A' }}</span>
                                </div>
                            @endif

                            @if($task->instructor)
                                <div class="flex items-center text-xs text-gray-600">
                                    <i class="fas fa-user-tie mr-2 text-[#FF92C2] w-4"></i>
                                    <span>{{ $task->instructor->name ?? 'N/A' }}</span>
                                </div>
                            @endif

                            @if($task->section)
                                <div class="flex items-center text-xs text-gray-600">
                                    <i class="fas fa-users mr-2 text-[#FF92C2] w-4"></i>
                                    <span>{{ $task->section->name ?? 'N/A' }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-gray-600">Progress</span>
                                <span class="text-xs text-gray-500">{{ $task->progress ?? 0 }}/{{ $task->totalSteps ?? 10 }} steps</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $task->progressPercentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        {{-- Footer Info --}}
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>
                                <i class="far fa-calendar-alt mr-1"></i> 
                                Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-star text-[#FF92C2]"></i> 
                                {{ $task->xp_reward ?? $task->xp ?? 0 }} XP
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-16 text-gray-500">
                        <i class="fas fa-tasks text-3xl mb-3"></i>
                        <p>No performance tasks assigned yet.</p>
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