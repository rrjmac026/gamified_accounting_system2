<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-6 text-gray-700 dark:text-[#FFC8FB]">
                    <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB] mb-6">Evaluations</h2>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#FFC8FB]">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Instructor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Submitted At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#595758] divide-y divide-[#FFC8FB]">
                                @forelse($evaluations as $evaluation)
                                    <tr class="hover:bg-[#FFF6FD] dark:hover:bg-[#6a6869]">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $evaluation->student->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $evaluation->instructor->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $evaluation->course->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $evaluation->submitted_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                            <a href="{{ route('evaluations.show', $evaluation) }}" class="text-[#FF92C2] hover:text-[#ff6fb5]">View</a>
                                            @if(auth()->user()->role === 'admin')
                                                <form class="inline" action="{{ route('evaluations.destroy', $evaluation) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No evaluations found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $evaluations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
