<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-0">Instructor Details</h2>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Name</label>
                        <div class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                  border border-[#FFC8FB] text-gray-800 dark:text-black-200 px-4 py-2">
                            {{ $instructor->user->name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Email</label>
                        <div class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                  border border-[#FFC8FB] text-gray-800 dark:text-black-200 px-4 py-2">
                            {{ $instructor->user->email }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Employee ID</label>
                        <div class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                  border border-[#FFC8FB] text-gray-800 dark:text-black-200 px-4 py-2">
                            {{ $instructor->employee_id }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Department</label>
                            <div class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                      border border-[#FFC8FB] text-gray-800 dark:text-black-200 px-4 py-2">
                                {{ $instructor->department }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Specialization</label>
                            <div class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                      border border-[#FFC8FB] text-gray-800 dark:text-black-200 px-4 py-2">
                                {{ $instructor->specialization }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-[#FF92C2] mb-4">Subjects</h3>
                    
                    @if($instructor->subjects->isEmpty())
                        <p class="text-gray-600">No subjects assigned to this instructor.</p>
                    @else
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($instructor->subjects as $subject)
                                <li class="px-4 py-2 bg-white border border-[#FFC8FB] rounded-lg shadow-sm">
                                    <span class="font-semibold">{{ $subject->subject_code }}</span> - 
                                    {{ $subject->subject_name }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                    <a href="{{ route('admin.instructors.edit', $instructor) }}" 
                       class="px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                              text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        Edit
                    </a>
                    <form action="{{ route('admin.instructors.destroy', $instructor) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200"
                                onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
