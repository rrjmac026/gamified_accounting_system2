<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2]">Badge Details</h2>
                    <a href="{{ route('admin.badges.index') }}" 
                       class="w-full sm:w-auto px-4 py-2 text-center bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Badges
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Badge Information</h3>
                        <div class="flex items-center gap-4 mb-4">
                            @if($badge->icon_path)
                                <img src="{{ Storage::url($badge->icon_path) }}" 
                                     alt="{{ $badge->name }}" 
                                     class="w-20 h-20 rounded-lg object-cover">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-[#FFC8FB] flex items-center justify-center">
                                    <i class="fas fa-medal text-3xl text-[#FF92C2]"></i>
                                </div>
                            @endif
                        </div>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Name:</dt>
                                <dd class="text-gray-900">{{ $badge->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Type:</dt>
                                <dd class="text-gray-900">{{ ucfirst($badge->criteria) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">XP Required:</dt>
                                <dd class="text-gray-900">{{ $badge->xp_threshold }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Description</h3>
                        <p class="text-gray-700">{{ $badge->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

