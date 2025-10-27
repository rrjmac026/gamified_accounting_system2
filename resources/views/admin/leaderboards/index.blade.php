@section('title', 'Leaderboards')

<x-app-layout>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Leaderboard Rankings</h2>
        
        {{-- Export Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" 
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                <i class="fas fa-download mr-2"></i>
                Export
                <i class="fas fa-chevron-down ml-2"></i>
            </button>
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
                <div class="py-1">
                    <a href="{{ route('admin.leaderboards.export', ['format' => 'csv'] + request()->all()) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#FFD9FF] transition-colors">
                        <i class="fas fa-file-csv mr-2 text-green-600"></i>Export as CSV
                    </a>
                    <a href="{{ route('admin.leaderboards.export', ['format' => 'pdf'] + request()->all()) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#FFD9FF] transition-colors">
                        <i class="fas fa-file-pdf mr-2 text-red-600"></i>Export as PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Filters Section --}}
    <div class="px-4 sm:px-8 mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-filter text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Filter Rankings</h3>
            </div>
            
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Period Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                        <select name="period" 
                                class="w-full rounded-xl border-[#FFC8FB]/50 bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200">
                            @foreach(['overall' => 'Overall', 'weekly' => 'This Week', 'monthly' => 'This Month', 'semester' => 'This Semester'] as $value => $label)
                                <option value="{{ $value }}" {{ $periodType === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Section Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                        <select name="section" 
                                class="w-full rounded-xl border-[#FFC8FB]/50 bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>
                                    {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Course Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select name="course" 
                                class="w-full rounded-xl border-[#FFC8FB]/50 bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" 
                                class="w-full rounded-xl border-[#FFC8FB]/50 bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200">
                            <option value="xp" {{ $sort === 'xp' ? 'selected' : '' }}>XP Earned</option>
                            <option value="tasks" {{ $sort === 'tasks' ? 'selected' : '' }}>Tasks Completed</option>
                            <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Student Name</option>
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    {{-- Responsive Table --}}
                    <div class="relative">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow-md rounded-lg">
                                    <table class="min-w-full divide-y divide-[#FFC8FB]">
                                        <thead class="bg-[#FFC8FB] text-xs uppercase">
                                            <tr>
                                                <th scope="col" class="py-3 px-4">
                                                    Rank
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Student
                                                </th>
                                                <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                    XP Earned
                                                </th>
                                                <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                    Tasks Completed
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ranked as $rank)
                                                <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                    <td class="py-3 px-4">
                                                        @if($rank['rank_position'] <= 3)
                                                            <span class="flex items-center font-semibold">
                                                                @if($rank['rank_position'] === 1)
                                                                    <i class="fas fa-trophy text-yellow-400 text-lg mr-2"></i>
                                                                @elseif($rank['rank_position'] === 2)
                                                                    <i class="fas fa-medal text-gray-400 text-lg mr-2"></i>
                                                                @else
                                                                    <i class="fas fa-award text-amber-600 text-lg mr-2"></i>
                                                                @endif
                                                                <span class="text-lg">{{ $rank['rank_position'] }}</span>
                                                            </span>
                                                        @else
                                                            <span class="font-medium text-gray-700">{{ $rank['rank_position'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4 font-medium">
                                                        {{ $rank['name'] }}
                                                    </td>
                                                    <td class="hidden md:table-cell py-3 px-4">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 font-semibold">
                                                            <i class="fas fa-star mr-2"></i>
                                                            {{ number_format($rank['total_xp']) }}
                                                        </span>
                                                    </td>
                                                    <td class="hidden lg:table-cell py-3 px-4">
                                                        <span class="inline-flex items-center text-gray-700">
                                                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                                            {{ $rank['tasks_completed'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-8 px-4 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-chart-line text-4xl mb-4"></i>
                                                            <p class="text-lg font-medium text-gray-900 mb-1">No rankings found</p>
                                                            <p class="text-gray-600">No data available for the selected period</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>