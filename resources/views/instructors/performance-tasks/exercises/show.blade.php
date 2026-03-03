{{-- resources/views/instructors/performance-tasks/exercises/show.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ $task->title }}</h1>
                <p class="text-gray-500">Manage exercises per step — add to any step independently</p>
            </div>
            <a href="{{ route('instructors.performance-tasks.index') }}" class="btn btn-ghost">← Back</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="grid gap-4">
            @foreach($stepTitles as $stepNum => $stepTitle)
                @php $exercises = $exercisesByStep->get($stepNum, collect()); @endphp

                <div class="card bg-base-100 shadow border">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="card-title text-lg">
                                    <span class="badge badge-primary">Step {{ $stepNum }}</span>
                                    {{ $stepTitle }}
                                </h2>
                                <p class="text-sm text-gray-500">
                                    {{ $exercises->count() }} exercise(s) added
                                </p>
                            </div>
                            <a href="{{ route('instructors.performance-tasks.exercises.create', [$task, $stepNum]) }}"
                               class="btn btn-sm btn-primary">
                                + Add Exercise
                            </a>
                        </div>

                        @if($exercises->isNotEmpty())
                            <div class="mt-3 space-y-2">
                                @foreach($exercises as $exercise)
                                    <div class="flex items-center justify-between bg-base-200 rounded px-3 py-2">
                                        <div>
                                            <span class="font-medium">{{ $exercise->title }}</span>
                                            @if($exercise->description)
                                                <p class="text-xs text-gray-400">{{ $exercise->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('instructors.performance-tasks.exercises.edit', [$task, $exercise]) }}"
                                               class="btn btn-xs btn-ghost">Edit</a>
                                            <form method="POST"
                                                  action="{{ route('instructors.performance-tasks.exercises.destroy', [$task, $exercise]) }}"
                                                  onsubmit="return confirm('Delete this exercise?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-error btn-ghost">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>