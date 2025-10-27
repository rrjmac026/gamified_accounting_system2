<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">XP Transaction Details</h2>

                <div class="space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Student</label>
                            <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $xpTransaction->student->user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Amount</label>
                            <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $xpTransaction->amount }} XP</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Type</label>
                            <p class="text-gray-700 dark:text-[#FFC8FB] capitalize">{{ $xpTransaction->type }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Source</label>
                            <p class="text-gray-700 dark:text-[#FFC8FB] capitalize">{{ str_replace('_', ' ', $xpTransaction->source) }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Description</label>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $xpTransaction->description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Processed At</label>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $xpTransaction->processed_at->format('F j, Y g:i A') }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.xp-transactions.index') }}" 
                           class="w-full sm:w-auto px-4 sm:px-6 py-2 text-center bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Back
                        </a>
                        <a href="{{ route('admin.xp-transactions.edit', $xpTransaction) }}" 
                           class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                  text-white text-center font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

