<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-xl sm:rounded-2xl">
                <div class="p-4 sm:p-6 text-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2]">Student Details</h2>
                        <a href="{{ route('admin.student.edit', $student) }}" 
                           class="w-full sm:w-auto px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] text-center">
                            Edit Student
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Basic Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Name:</dt>
                                    <dd class="text-gray-900">{{ $student->user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Email:</dt>
                                    <dd class="text-gray-900">{{ $student->user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">ID Number:</dt>
                                    <dd class="text-gray-900">{{ $student->student_number }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Course:</dt>
                                    <dd class="text-gray-900">{{ $student->course->course_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Year Level:</dt>
                                    <dd class="text-gray-900">{{ $student->year_level }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Section:</dt>
                                    <dd class="text-gray-900">
                                        @if ($student->sections->isNotEmpty())
                                            {{ $student->sections->pluck('name')->join(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </dd>

                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Status:</dt>
                                    <dd>
                                        @if($student->user->is_active)
                                            <span class="text-green-600 font-medium">Active</span>
                                        @else
                                            <span class="text-red-600 font-medium">Inactive</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <!-- Leaderboard Performance -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Leaderboard Performance</h3>
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Total XP:</dt>
                                    <dd class="text-gray-900 font-semibold">
                                        {{ $student->xpTransactions->sum('amount') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Current Rank:</dt>
                                    <dd class="text-gray-900 font-semibold">
                                        {{ $rank ?? '—' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Badges Earned:</dt>
                                    <dd class="text-gray-900 font-semibold">
                                        {{ $student->badges->count() }}
                                    </dd>
                                </div>
                            </dl>

                            @if($student->badges->count() > 0)
                                <div class="mt-4 flex flex-wrap gap-4">
                                    @foreach($student->badges as $badge)
                                        <div class="flex flex-col items-center bg-[#FF92C2] text-white rounded-lg p-2">
                                            <!-- Badge Name -->
                                            <span class="text-sm font-medium">{{ $badge->name }}</span>

                                            <!-- Badge Icon -->
                                            @if($badge->icon_path)
                                                <img src="{{ Storage::url($badge->icon_path) }}" 
                                                     alt="{{ $badge->name }}"  alt="{{ $badge->name }} Icon" 
                                                    class="mt-1 w-8 h-8 object-contain">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Enrolled Subjects</h3>
                            @if($student->subjects->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-[#FFC8FB]">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Subject Code</th>
                                                <th class="py-2 px-4 text-left">Subject Name</th>
                                                <th class="py-2 px-4 text-left">Instructor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#FFC8FB]">
                                            @foreach($student->subjects as $subject)
                                                <tr class="hover:bg-[#FFD9FF]">
                                                    <td class="py-2 px-4">{{ $subject->subject_code }}</td>
                                                    <td class="py-2 px-4">{{ $subject->subject_name }}</td>
                                                    <td class="py-2 px-4">{{ $subject->instructor->user->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500">No subjects enrolled.</p>
                            @endif       
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

