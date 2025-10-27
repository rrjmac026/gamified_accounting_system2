<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Performance Task
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <!-- Display Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructors.performance-tasks.store') }}" method="POST" id="taskForm">
                    @csrf

                    <!-- Task Title -->
                    <div class="mb-4">
                        <label for="title" class="block font-medium text-sm text-gray-700">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input id="title" name="title" type="text" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('title') border-red-500 @enderror" 
                               value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject_id" class="block font-medium text-sm text-gray-700">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select id="subject_id" name="subject_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('subject_id') border-red-500 @enderror" 
                                required>
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" 
                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Section -->
                    <div class="mb-4">
                        <label for="section_id" class="block font-medium text-sm text-gray-700">
                            Section <span class="text-red-500">*</span>
                        </label>
                        <select id="section_id" name="section_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('section_id') border-red-500 @enderror" 
                                required>
                            <option value="">-- Select Section --</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" 
                                    {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('section_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- XP Reward -->
                    <div class="mb-4">
                        <label for="xp_reward" class="block font-medium text-sm text-gray-700">
                            XP Reward <span class="text-red-500">*</span>
                        </label>
                        <input id="xp_reward" name="xp_reward" type="number" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('xp_reward') border-red-500 @enderror" 
                               value="{{ old('xp_reward', 50) }}" min="0" required>
                        @error('xp_reward')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Attempts -->
                    <div class="mb-4">
                        <label for="max_attempts" class="block font-medium text-sm text-gray-700">
                            Max Attempts <span class="text-red-500">*</span>
                        </label>
                        <input id="max_attempts" name="max_attempts" type="number" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 @error('max_attempts') border-red-500 @enderror" 
                               value="{{ old('max_attempts', 2) }}" min="1" required>
                        @error('max_attempts')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    
                </form>
            </div>
        </div>
    </div>


</x-app-layout>