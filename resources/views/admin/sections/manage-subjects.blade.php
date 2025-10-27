@section('title', 'Manage Subjects')
<x-app-layout>
    <div class="flex justify-between items-center px-4 sm:px-8 mt-4">
        <h1 class="text-xl sm:text-2xl font-bold text-[#FF92C2]">
            Manage Subjects for {{ $section->name }}
        </h1>
        <a href="{{ route('admin.sections.index') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 hover:bg-gray-600 rounded-lg shadow-sm transition-all duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Sections
        </a>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg sm:rounded-2xl p-6">

                {{-- Flash message --}}
                @if (session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-6">
                    <div class="relative">
                        <input type="text" 
                               id="subject-search" 
                               placeholder="Search subjects by name or code..." 
                               class="w-full pl-10 pr-4 py-2 rounded-lg border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.sections.subjects.update', $section->id) }}">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="subjects-grid">
                        @foreach ($subjects as $subject)
                            <label class="flex items-center space-x-3 p-3 border border-[#FFC8FB]/50 rounded-lg hover:bg-[#FFD9FF] cursor-pointer transition-colors duration-150">
                                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                       class="form-checkbox h-5 w-5 text-[#FF92C2] border-gray-300 rounded"
                                       {{ $section->subjects->contains($subject->id) ? 'checked' : '' }}>
                                <span class="text-gray-700 font-medium">{{ $subject->subject_name }}</span>
                                <span class="text-gray-500 text-sm">{{ $subject->subject_code }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <a href="{{ route('admin.sections.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>

                        <button type="submit" 
                                class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] shadow transition-all duration-200">
                            Save Subjects
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('subject-search');
        const subjectsGrid = document.getElementById('subjects-grid');
        const subjectItems = subjectsGrid.getElementsByTagName('label');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            Array.from(subjectItems).forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
