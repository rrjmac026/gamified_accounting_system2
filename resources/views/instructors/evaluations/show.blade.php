<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB]">Evaluation Details</h2>
                    <a href="{{ route('evaluations.index') }}" class="text-[#FF92C2] hover:text-[#ff6fb5]">Back to List</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Student</h3>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $evaluation->student->user->name }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Instructor</h3>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $evaluation->instructor->user->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Course</h3>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $evaluation->course->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Submitted At</h3>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $evaluation->submitted_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-4">Evaluation Responses</h3>
                        <div class="space-y-4">
                            @foreach($evaluation->responses as $criterion => $rating)
                                <div class="bg-white dark:bg-[#4a4949] p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-700 dark:text-[#FFC8FB] mb-2">{{ $criterion }}</h4>
                                    <div class="flex space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $i <= $rating ? 'bg-[#FF92C2] text-white' : 'bg-gray-100 text-gray-400' }}">
                                                {{ $i }}
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-2">Additional Comments</h3>
                        <div class="bg-white dark:bg-[#4a4949] p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-[#FFC8FB] whitespace-pre-line">{{ $evaluation->comments }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
