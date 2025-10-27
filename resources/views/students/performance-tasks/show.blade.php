<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#595758] leading-tight">
            {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/80 p-6 rounded-2xl shadow-md border border-[#FFC8FB]/30">
            <p class="text-gray-700 mb-4">{{ $task->description ?? 'No description available.' }}</p>

            <div class="mb-6">
                <h3 class="font-semibold text-[#595758] mb-2">Progress</h3>
                <div class="flex flex-wrap gap-3">
                    @for ($i = 1; $i <= 10; $i++)
                        <a href="{{ route('students.performance-tasks.step', ['id' => $task->id, 'step' => $i]) }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300
                           {{ in_array($i, $progress['completed_steps']) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-[#FFF0F5]' }}">
                            Step {{ $i }}
                        </a>
                    @endfor
                </div>
            </div>

            <a href="{{ route('students.performance-tasks.step', ['id' => $task->id, 'step' => $progress['current_step'] ?? 1]) }}"
               class="inline-flex items-center gap-2 bg-[#FF92C2] text-white px-5 py-2 rounded-xl shadow hover:scale-[0.98] transition-all">
                <i class="fas fa-play"></i> Continue Task
            </a>
        </div>
    </div>
</x-app-layout>
