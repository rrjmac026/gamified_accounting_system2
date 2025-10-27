<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Edit XP Transaction</h2>

                <form action="{{ route('admin.xp-transactions.update', $xpTransaction) }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Student</label>
                            <select name="student_id" required class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $xpTransaction->student_id == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Amount (XP)</label>
                            <input type="number" name="amount" value="{{ $xpTransaction->amount }}" required 
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Type</label>
                            <select name="type" required class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ $xpTransaction->type == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Source</label>
                            <select name="source" required class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                @foreach($sources as $source)
                                    <option value="{{ $source }}" {{ $xpTransaction->source == $source ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $source)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Description</label>
                        <textarea name="description" required class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" rows="3">{{ $xpTransaction->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Process Date</label>
                        <input type="datetime-local" name="processed_at" value="{{ $xpTransaction->processed_at->format('Y-m-d\TH:i') }}" required 
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                            text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.xp-transactions.index') }}" 
                           class="w-full sm:w-auto px-4 sm:px-6 py-2 text-center bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                       text-white text-sm sm:text-base font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Update Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
