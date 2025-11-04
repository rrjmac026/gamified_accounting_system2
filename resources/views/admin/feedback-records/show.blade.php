@section('title', 'Feedback Details')

<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    
                    {{-- Header Section --}}
                    <div class="mb-6 pb-4 border-b border-[#FFC8FB]/50">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Feedback Details</h2>
                    </div>

                    {{-- Main Content Grid --}}
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Student Info --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-user-graduate mr-2"></i>Student
                                </label>
                                <p class="text-gray-700 font-medium">{{ $feedbackRecord->student->user->name }}</p>
                            </div>

                            {{-- Task Info --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-tasks mr-2"></i>Performance Task
                                </label>
                                <p class="text-gray-700 font-medium">{{ $feedbackRecord->performanceTask->title ?? 'N/A' }}</p>
                            </div>

                            {{-- Feedback Type --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-tag mr-2"></i>Feedback Type
                                </label>
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full capitalize
                                    {{ $feedbackRecord->feedback_type === 'general' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $feedbackRecord->feedback_type === 'improvement' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $feedbackRecord->feedback_type === 'question' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ $feedbackRecord->feedback_type }}
                                </span>
                            </div>

                            {{-- Rating --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-star mr-2"></i>Rating
                                </label>
                                @if($feedbackRecord->rating)
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedbackRecord->rating ? 'text-yellow-400' : 'text-gray-300' }} text-lg"></i>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600 font-medium">({{ $feedbackRecord->rating }}/5)</span>
                                    </div>
                                @else
                                    <p class="text-gray-500">No rating provided</p>
                                @endif
                            </div>

                            {{-- Generated Date --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-calendar-alt mr-2"></i>Generated Date
                                </label>
                                <p class="text-gray-700 font-medium">
                                    {{ $feedbackRecord->generated_at ? $feedbackRecord->generated_at->format('F j, Y g:i A') : $feedbackRecord->created_at->format('F j, Y g:i A') }}
                                </p>
                            </div>

                            {{-- Status --}}
                            <div class="bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>Status
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $feedbackRecord->is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $feedbackRecord->is_read ? 'Read' : 'Unread' }}
                                    </span>
                                    @if($feedbackRecord->is_anonymous)
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-user-secret mr-1"></i>Anonymous
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Feedback Content --}}
                            <div class="col-span-2 bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-3">
                                    <i class="fas fa-comment-dots mr-2"></i>Feedback Content
                                </label>
                                <div class="bg-white rounded-lg p-4 border border-[#FFC8FB]/30 shadow-sm">
                                    <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $feedbackRecord->feedback_text }}</p>
                                </div>
                            </div>

                            {{-- Recommendations --}}
                            @if($feedbackRecord->recommendations && count($feedbackRecord->recommendations) > 0)
                                <div class="col-span-2 bg-gradient-to-br from-white to-[#FFF0FA] p-4 rounded-xl border border-[#FFC8FB]/20">
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-3">
                                        <i class="fas fa-lightbulb mr-2"></i>Recommendations
                                    </label>
                                    <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-lg p-4 border border-pink-200 shadow-sm">
                                        <ul class="space-y-3">
                                            @foreach($feedbackRecord->recommendations as $recommendation)
                                                <li class="flex items-start bg-white/60 rounded-lg p-3 hover:bg-white/80 transition-colors duration-200">
                                                    <i class="fas fa-check-circle text-[#FF92C2] mt-1 mr-3 flex-shrink-0"></i>
                                                    <span class="text-gray-700">{{ $recommendation }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-[#FFC8FB]/50 gap-4">
                        <div class="flex flex-wrap gap-3">
                            @if(!$feedbackRecord->is_read)
                                <form action="{{ route('admin.feedback-records.mark-read', $feedbackRecord) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 text-sm font-medium">
                                        <i class="fas fa-check mr-2"></i>Mark as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.feedback-records.index') }}" 
                               class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back
                            </a>
                            <a href="{{ route('admin.feedback-records.edit', $feedbackRecord) }}" 
                               class="px-4 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] text-white rounded-lg hover:from-[#ff6fb5] hover:to-[#ffb3e6] shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>