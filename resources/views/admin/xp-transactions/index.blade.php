@section('title', 'XP Transactions')
<x-app-layout>
    {{-- Add XP Transaction Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">XP Transactions Management</h2>
        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.xp-transactions.create') }}" 
               class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Add XP Transaction
            </a>
        </div>
    </div>

    {{-- Enhanced Search Form --}}
    <div class="px-4 sm:px-8 mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-search text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Search XP Transactions</h3>
            </div>
            
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="xp-search"
                           placeholder="Search by student, type, source, or date..." 
                           class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                </div>
                <div class="flex justify-end">
                    <span class="text-xs text-gray-500" id="xp-counter">
                        Showing {{ $transactions->count() }} transactions
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
                    @if (session('success'))
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
                                                <th scope="col" class="py-3 px-4">
                                                    Amount
                                                </th>
                                                <th scope="col" class="hidden sm:table-cell py-3 px-4">
                                                    Type
                                                </th>
                                                <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                    Source
                                                </th>
                                                <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                    Date
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="xp-table-body">
                                            @forelse ($transactions as $transaction)
                                                <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                    <td class="py-3 px-4 font-medium">
                                                        {{ $transaction->student->user->name ?? 'N/A' }}
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $transaction->student->user->email ?? '' }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex items-center gap-2">
                                                            <i class="fas fa-star text-yellow-500 text-xs"></i>
                                                            <span class="font-medium">{{ $transaction->amount }} XP</span>
                                                        </div>
                                                    </td>
                                                    <td class="hidden sm:table-cell py-3 px-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            @if($transaction->type === 'earned') bg-green-100 text-green-800
                                                            @elseif($transaction->type === 'spent') bg-red-100 text-red-800
                                                            @else bg-blue-100 text-blue-800
                                                            @endif">
                                                            {{ ucfirst($transaction->type) }}
                                                        </span>
                                                    </td>
                                                    <td class="hidden md:table-cell py-3 px-4">
                                                        <span class="text-sm capitalize">{{ str_replace('_', ' ', $transaction->source) }}</span>
                                                    </td>
                                                    <td class="hidden lg:table-cell py-3 px-4">
                                                        <div class="text-sm">
                                                            {{ $transaction->processed_at->format('M d, Y') }}
                                                            <div class="text-xs text-gray-500">
                                                                {{ $transaction->processed_at->format('h:i A') }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <a href="{{ route('admin.xp-transactions.show', $transaction) }}" 
                                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="ml-2 sm:hidden">View</span>
                                                            </a>
                                                            <a href="{{ route('admin.xp-transactions.edit', $transaction) }}" 
                                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                <i class="fas fa-edit"></i>
                                                                <span class="ml-2 sm:hidden">Edit</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-exchange-alt text-4xl mb-4"></i>
                                                            <p class="text-lg font-medium text-gray-900 mb-1">No transactions found</p>
                                                            <p class="text-gray-600">Add a new XP transaction to get started</p>
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
                    @if($transactions->hasPages())
                        <div class="mt-4 sm:mt-6">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Client-side Search Script --}}
    <script>
        const xpSearch = document.getElementById("xp-search");
        const xpTableBody = document.getElementById("xp-table-body");
        const xpRows = xpTableBody.getElementsByTagName("tr");
        const xpCounter = document.getElementById("xp-counter");

        xpSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            for (let i = 0; i < xpRows.length; i++) {
                let rowText = xpRows[i].textContent.toLowerCase();

                if (rowText.includes(searchValue)) {
                    xpRows[i].style.display = "";
                    visibleCount++;
                } else {
                    xpRows[i].style.display = "none";
                }
            }

            xpCounter.textContent = `Showing ${visibleCount} transaction${visibleCount !== 1 ? 's' : ''}`;
        });
    </script>
</x-app-layout>