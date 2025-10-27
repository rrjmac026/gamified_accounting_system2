<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:from-[#4B4B4B] dark:to-[#3B3B3B] 
                        backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FF92C2] mb-6">Create New Badge</h2>

                <form action="{{ route('admin.badges.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Badge Name</label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-3">Badge Icon</label>
                        
                        <!-- Icon Selection Method -->
                        <div class="mb-4">
                            <label class="inline-flex items-center mr-6">
                                <input type="radio" name="icon_type" value="preset" class="text-[#FF92C2] focus:ring-pink-200" checked onclick="toggleIconInput('preset')">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Choose from presets</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="icon_type" value="upload" class="text-[#FF92C2] focus:ring-pink-200" onclick="toggleIconInput('upload')">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Upload custom icon</span>
                            </label>
                        </div>

                        <!-- Preset Icons Grid -->
                        <div id="preset-icons" class="grid grid-cols-5 gap-3 mb-4">
                            @for ($i = 1; $i <= 15; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="preset_icon" value="badge-{{ $i }}.png" class="hidden peer" {{ old('preset_icon') == "badge-{$i}.png" ? 'checked' : '' }}>
                                    <div class="border-4 border-gray-300 peer-checked:border-[#FF92C2] peer-checked:ring-4 peer-checked:ring-pink-200 rounded-full p-2 hover:border-pink-300 transition-all duration-200 aspect-square flex items-center justify-center">
                                        <img src="{{ asset('storage/badges/presets/badge-' . $i . '.png') }}" 
                                             alt="Badge {{ $i }}" 
                                             class="w-full h-full object-cover rounded-full"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23ddd%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2230%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23666%22%3E{{ $i }}%3C/text%3E%3C/svg%3E'">
                                    </div>
                                </label>
                            @endfor
                        </div>

                        <!-- Custom Upload Input -->
                        <div id="custom-upload" class="hidden">
                            <input type="file" name="icon" accept="image/*"
                                   class="w-full rounded-lg shadow-sm bg-white dark:bg-[#595758] border-[#FFC8FB] 
                                          focus:border-pink-400 focus:ring focus:ring-pink-200 file:mr-4 file:py-2 
                                          file:px-4 file:border-0 file:bg-[#FF92C2] file:text-white 
                                          hover:file:bg-[#ff6fb5]">
                            <p class="mt-1 text-sm text-gray-500">Recommended size: 128x128px. Supported formats: PNG, JPG, SVG</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Description</label>
                        <textarea name="description" rows="3" required
                                  class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                        text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">XP Required</label>
                            <input type="number" name="xp_threshold" required min="0" value="{{ old('xp_threshold') }}"
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
                                <option value="achievement" {{ old('criteria') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                                <option value="skill" {{ old('criteria') == 'skill' ? 'selected' : '' }}>Skill</option>
                                <option value="participation" {{ old('criteria') == 'participation' ? 'selected' : '' }}>Participation</option>
                                <option value="milestone" {{ old('criteria') == 'milestone' ? 'selected' : '' }}>Milestone</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.badges.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                        <button type="submit" 
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Create Badge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleIconInput(type) {
            const presetDiv = document.getElementById('preset-icons');
            const uploadDiv = document.getElementById('custom-upload');
            const presetRadios = document.querySelectorAll('input[name="preset_icon"]');
            const uploadInput = document.querySelector('input[name="icon"]');

            if (type === 'preset') {
                presetDiv.classList.remove('hidden');
                uploadDiv.classList.add('hidden');
                uploadInput.removeAttribute('required');
                if (uploadInput) uploadInput.value = '';
            } else {
                presetDiv.classList.add('hidden');
                uploadDiv.classList.remove('hidden');
                presetRadios.forEach(radio => radio.checked = false);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const iconType = document.querySelector('input[name="icon_type"]:checked').value;
            toggleIconInput(iconType);
        });
    </script>
</x-app-layout>