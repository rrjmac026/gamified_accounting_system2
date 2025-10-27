<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:from-[#4B4B4B] dark:to-[#3B3B3B] 
                        backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FF92C2] mb-6">Edit Badge</h2>

                <form action="{{ route('admin.badges.update', $badge) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Badge Name</label>
                        <input type="text" name="name" value="{{ $badge->name }}" required
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Badge Icon</label>
                        @if($badge->icon_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($badge->icon_path) }}" 
                                     alt="{{ $badge->name }}" 
                                     class="w-20 h-20 rounded-lg object-cover">
                            </div>
                        @endif
                        <input type="file" name="icon" accept="image/*"
                               class="w-full rounded-lg shadow-sm bg-white dark:bg-[#595758] border-[#FFC8FB] 
                                      focus:border-pink-400 focus:ring focus:ring-pink-200 file:mr-4 file:py-2 
                                      file:px-4 file:border-0 file:bg-[#FF92C2] file:text-white 
                                      hover:file:bg-[#ff6fb5]">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep current icon</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Description</label>
                        <textarea name="description" rows="3" required
                                  class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">{{ $badge->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">XP Required</label>
                            <input type="number" name="xp_threshold" value="{{ $badge->xp_threshold }}" required min="0"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Badge Type</label>
                            <select name="criteria" required
                                    class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                <option value="achievement" {{ $badge->criteria === 'achievement' ? 'selected' : '' }}>Achievement</option>
                                <option value="skill" {{ $badge->criteria === 'skill' ? 'selected' : '' }}>Skill</option>
                                <option value="participation" {{ $badge->criteria === 'participation' ? 'selected' : '' }}>Participation</option>
                                <option value="milestone" {{ $badge->criteria === 'milestone' ? 'selected' : '' }}>Milestone</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.badges.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                        <button type="submit" 
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Update Badge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
