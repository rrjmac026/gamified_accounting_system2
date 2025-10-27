{{-- My Subjects Page --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">My Subjects</h2>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        @forelse($subjects as $subject)
                            <div class="bg-[#FFF6FD] hover:bg-[#FFD9FF] transition-colors duration-300 border border-[#FFC8FB] rounded-lg p-4 sm:p-6 shadow-sm hover:shadow-md">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex-1">
                                        <a href="{{ route('students.subjects.show', $subject->id) }}" 
                                           class="text-lg font-semibold text-[#FF92C2] hover:text-[#ff6fb5] transition-colors duration-200">
                                            {{ $subject->subject_code }} - {{ $subject->subject_name }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">{{ $subject->description }}</p>
                                    </div>
                                    <div class="mt-3 sm:mt-0 sm:ml-4">
                                        <a href="{{ route('students.subjects.show', $subject->id) }}" 
                                           class="inline-flex items-center px-3 py-2 text-xs font-medium text-[#FF92C2] bg-white border border-[#FFC8FB] rounded-md hover:bg-[#FFF0FA] hover:text-[#ff6fb5] transition-colors duration-200">
                                            View Details
                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="text-[#FF92C2] mb-4">
                                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No subjects assigned yet</h3>
                                <p class="text-gray-600">Your subjects will appear here once they are assigned by your instructor.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>