@section('title', 'System Reports')

<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg sm:rounded-2xl">
                <div class="p-4 sm:p-6 text-gray-700">
                    <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">System Reports</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <!-- Performance Report Card -->
                        <div class="bg-[#FFF6FD] rounded-xl p-4 sm:p-6 shadow-sm hover:shadow transition-all duration-200 border border-[#FFC8FB]/50">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-[#FFC8FB] rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-[#FF92C2]"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-[#FF92C2]">Student Reports</h3>
                            </div>
                            <p class="text-gray-600 mb-4 text-sm">View detailed student performance and progress reports.</p>
                            <form action="{{ route('admin.reports.students') }}" method="GET" class="space-y-3">
                                <div class="space-y-2">
                                    <input type="date" name="date_from" 
                                           class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-[#FF92C2] focus:ring focus:ring-pink-200 text-sm">
                                    <input type="date" name="date_to" 
                                           class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-[#FF92C2] focus:ring focus:ring-pink-200 text-sm">
                                </div>
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                               text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                    Generate Report
                                </button>
                            </form>
                        </div>

                        <!-- Add similar cards for other report types with updated styling -->
                        <!-- ...existing report cards... -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
