<x-app-layout>
    <!-- Header Section with Actions -->
    <div class="bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 rounded-xl border-b border-pink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Quiz Management</h1>
                    <p class="text-gray-600">Create, manage, and organize your quiz questions</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('instructors.quizzes.create') }}" 
                       class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>
                        Create Question
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center shadow-sm">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- No quizzes found -->
        @if($quizzes->isEmpty())
            <div class="text-center py-20">
                <div class="bg-white rounded-2xl shadow-lg p-12 max-w-md mx-auto border border-gray-100">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clipboard-question text-3xl text-[#FF92C2]"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">No Quiz Questions Yet</h3>
                    <p class="text-gray-600 mb-6">Start building your quiz library by creating your first question</p>
                    <a href="{{ route('instructors.quizzes.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white font-semibold rounded-xl hover:from-[#ff6fb5] hover:to-[#FF92C2] shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Quiz
                    </a>
                </div>
            </div>
        @else
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-100 rounded-lg">
                            <i class="fas fa-question-circle text-[#FF92C2]"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Questions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $quizzes->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-[#FFC8FB] rounded-lg">
                            <i class="fas fa-star text-[#FF92C2]"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Points</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $quizzes->sum('points') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-list text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Question Types</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $quizzes->pluck('type')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-rose-100 rounded-lg">
                            <i class="fas fa-tasks text-rose-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Associated Tasks</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $quizzes->whereNotNull('task_id')->pluck('task_id')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-[#FFF0FA] rounded-2xl shadow-lg border border-pink-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-pink-100 bg-gradient-to-r from-[#FFF0FA] to-[#FFC8FB]/30">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-[#FF92C2]">Quiz Questions</h2>
                            <p class="text-sm text-gray-600 mt-1">Manage and organize your quiz questions</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-pink-200">
                                {{ $quizzes->count() }} questions
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-[#FFC8FB]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Points</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#FFC8FB]">
                            @foreach($quizzes as $quiz)
                                <tr class="hover:bg-[#FFF6FD] transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="flex items-center justify-center w-8 h-8 bg-[#FFC8FB] text-[#FF92C2] text-sm font-semibold rounded-full">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($quiz->task)
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-[#FF92C2] rounded-full mr-3"></div>
                                                <span class="text-sm font-medium text-gray-900">{{ $quiz->task->title }}</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                No Task
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        <div class="text-sm text-gray-900 font-medium truncate" title="{{ $quiz->question_text }}">
                                            {{ Str::limit($quiz->question_text, 60) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#FFC8FB]/50 text-[#FF92C2] border border-[#FFC8FB]">
                                            {{ str_replace('_', ' ', $quiz->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-1 text-xs"></i>
                                            <span class="text-sm font-semibold text-gray-900">{{ $quiz->points }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $quiz->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('instructors.quizzes.show', $quiz) }}" 
                                               class="text-[#FF92C2] hover:text-[#ff6fb5] transition-colors p-2 hover:bg-pink-50 rounded-lg" 
                                               title="View Question">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('instructors.quizzes.edit', $quiz) }}" 
                                               class="text-[#FF92C2] hover:text-[#ff6fb5] transition-colors p-2 hover:bg-pink-50 rounded-lg" 
                                               title="Edit Question">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($quiz->quiz_file_path)
                                                <a href="{{ route('instructors.quizzes.download', $quiz) }}" 
                                                   class="text-green-600 hover:text-green-700 transition-colors p-2 hover:bg-green-50 rounded-lg" 
                                                   title="Download Quiz File">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            @if($quiz->csv_template_headers)
                                                <a href="{{ route('instructors.quizzes.downloadTemplate', $quiz) }}" 
                                                   class="text-purple-600 hover:text-purple-700 transition-colors p-2 hover:bg-purple-50 rounded-lg" 
                                                   title="Download Template">
                                                    <i class="fas fa-file-csv"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bulk Import Section -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg border border-pink-100 overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-pink-50 to-purple-50 border-b border-pink-100">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-100 rounded-lg mr-4">
                            <i class="fas fa-upload text-[#FF92C2]"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[#FF92C2]">Bulk Import Questions</h3>
                            <p class="text-sm text-gray-600">Upload CSV or Excel files to import multiple questions at once</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('instructors.quizzes.import', $quizzes->first()->task_id ?? 1) }}" 
                          method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quizzes->first()->id ?? '' }}">

                        <div class="flex flex-col sm:flex-row gap-6">
                            <div class="flex-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Choose File</label>
                                <div class="relative">
                                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-pink-50 file:to-purple-50 file:text-[#FF92C2] hover:file:from-pink-100 hover:file:to-purple-100 border border-pink-200 rounded-lg focus:ring-2 focus:ring-[#FF92C2] focus:border-[#FF92C2]">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Supported formats: CSV, XLSX, XLS</p>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-upload mr-2"></i>
                                    Import Questions
                                </button>
                            </div>
                        </div>
                    </form>

                    @error('file')
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-3"></i>
                                <div class="text-sm text-red-800">
                                    @if(is_array($message))
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($message as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $message }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @enderror
                </div>
            </div>
        @endif
    </div>
</x-app-layout>