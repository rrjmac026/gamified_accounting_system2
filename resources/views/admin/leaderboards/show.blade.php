<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Leaderboard Details</h2>
                        <a href="{{ route('admin.leaderboards.index') }}" 
                           class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Leaderboard
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Information -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Student Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Name:</dt>
                                    <dd class="text-gray-900">{{ $leaderboard->student->user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Course:</dt>
                                    <dd class="text-gray-900">{{ $leaderboard->student->course->course_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Period:</dt>
                                    <dd class="text-gray-900">{{ $leaderboard->period_label }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Performance Stats -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Performance Statistics</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Rank:</dt>
                                    <dd class="text-gray-900 font-bold">{{ $leaderboard->rank_text }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Total XP:</dt>
                                    <dd class="text-yellow-600">
                                        <i class="fas fa-star mr-1"></i>
                                        {{ number_format($leaderboard->total_xp) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Tasks Completed:</dt>
                                    <dd class="text-gray-900">{{ $leaderboard->tasks_completed }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
