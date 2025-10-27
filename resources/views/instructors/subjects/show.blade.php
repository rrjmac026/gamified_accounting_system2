<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 text-gray-700">
                    <div class="mb-6">
                        <nav class="flex items-center space-x-2 text-sm">
                            <a href="{{ route('instructors.subjects.index') }}" class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                My Subjects
                            </a>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-gray-600">{{ $subject->subject_code }}</span>
                        </nav>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#FF92C2] mb-2">{{ $subject->subject_code }} - {{ $subject->subject_name }}</h2>
                        <p class="text-gray-600">{{ $subject->description }}</p>
                    </div>

                    <!-- Students -->
                    <div class="bg-white rounded-xl shadow-sm border border-[#FFC8FB]/30 p-6">
                        <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                            <i class="fas fa-users w-5 h-5 mr-2"></i>
                            Students ({{ $subject->students->count() }})
                        </h3>
                        
                        @if($subject->students->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#FFC8FB]/50">
                                    <thead class="bg-gradient-to-r from-[#FFC8FB]/80 to-[#FFC8FB]/60">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#FFC8FB]/30">
                                        @foreach($subject->students as $student)
                                            <tr class="hover:bg-[#FFF6FD] transition-colors">
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $student->user->name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    {{ $student->user->email }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-user-plus text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No students enrolled in this subject yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>