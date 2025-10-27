@section('title', 'Evaluation Page')

<x-app-layout>
    {{-- Header Section with Create Button --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Evaluation Management</h2>
    </div>

    {{-- Enhanced Search Form --}}
    <div class="px-4 sm:px-8 mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-search text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Search Evaluations</h3>
            </div>
            
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="evaluation-search"
                           placeholder="Search by student, instructor, or course..." 
                           class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                </div>
                <div class="flex justify-end">
                    <span class="text-xs text-gray-500" id="evaluation-counter">
                        Showing {{ $evaluations->count() }} evaluation(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    @if(session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Responsive Table --}}
                    <div class="relative">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow-md rounded-lg">
                                    <table class="min-w-full divide-y divide-[#FFC8FB]">
                                        <thead class="bg-[#FFC8FB] text-xs uppercase">
                                            <tr>
                                                <th scope="col" class="py-3 px-4">
                                                    Student
                                                </th>
                                                <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                    Instructor
                                                </th>
                                                <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                    Course
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Submitted
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="evaluation-table-body">
                                            @forelse($evaluations as $evaluation)
                                                <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                    <td class="py-3 px-4 font-medium">
                                                        {{ $evaluation->student->user->name }}
                                                    </td>
                                                    <td class="hidden md:table-cell py-3 px-4">
                                                        {{ $evaluation->instructor->user->name }}
                                                    </td>
                                                    <td class="hidden lg:table-cell py-3 px-4">
                                                        {{ $evaluation->course->course_name }}
                                                    </td>
                                                    <td class="py-3 px-4 text-sm text-gray-600">
                                                        {{ $evaluation->submitted_at->format('M d, Y') }}
                                                        <div class="text-xs text-gray-500">
                                                            {{ $evaluation->submitted_at->format('H:i') }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <a href="{{ route('evaluations.show', $evaluation) }}" 
                                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="ml-2 sm:hidden">View</span>
                                                            </a>
                                                            @if(auth()->user()->role === 'admin')
                                                                <form action="{{ route('admin.evaluations.destroy', $evaluation) }}" 
                                                                      method="POST" 
                                                                      class="inline-flex" 
                                                                      onsubmit="return confirm('Are you sure you want to delete this evaluation?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900">
                                                                        <i class="fas fa-trash"></i>
                                                                        <span class="ml-2 sm:hidden">Delete</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                                                            <p class="text-lg font-medium text-gray-900 mb-1">No evaluations found</p>
                                                            <p class="text-gray-600">Evaluations will appear here once submitted</p>
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

                    {{-- Pagination --}}
                    @if($evaluations->hasPages())
                        <div class="mt-4 sm:mt-6">
                            {{ $evaluations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Client-side Search Script --}}
    <script>
        const evaluationSearch = document.getElementById("evaluation-search");
        const evaluationTableBody = document.getElementById("evaluation-table-body");
        const evaluationRows = evaluationTableBody.getElementsByTagName("tr");
        const evaluationCounter = document.getElementById("evaluation-counter");

        evaluationSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            for (let i = 0; i < evaluationRows.length; i++) {
                let rowText = evaluationRows[i].textContent.toLowerCase();
                if (rowText.includes(searchValue)) {
                    evaluationRows[i].style.display = "";
                    visibleCount++;
                } else {
                    evaluationRows[i].style.display = "none";
                }
            }
            evaluationCounter.textContent = `Showing ${visibleCount} evaluation(s)`;
        });
    </script>
</x-app-layout>