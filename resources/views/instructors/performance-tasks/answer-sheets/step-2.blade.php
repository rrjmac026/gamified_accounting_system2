<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

    <div class="py-4 sm:py-6 lg:py-8">
        {{-- Flash/Error/Success Messages --}}
        @if (session('error'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                        <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                        <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Enhanced Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="relative">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Answer Key - Step 2 of 10</span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                        Answer Key: Journalizing Entries
                    </h1>
                    <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                        Create the correct answer key for Journalizing Entries. This will be used to automatically grade student submissions.
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-sm text-purple-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Task: {{ $task->title }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6 bg-purple-50 border-b border-purple-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-purple-900 mb-1">Instructions for Creating Answer Key</h3>
                        <p class="text-xs sm:text-sm text-purple-800">
                            Fill in the correct answers below. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                        </p>
                    </div>
                </div>
            </div>

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 2]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>
                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">
                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Answer Sheets
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Answer Key & Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let hot;
            document.addEventListener("DOMContentLoaded", function () {
                const container = document.getElementById('spreadsheet');
                const savedData = @json($sheet->correct_data ?? null);
                const initialData = savedData ? JSON.parse(savedData) : Array.from({ length: 15 }, () => Array(20).fill('')); // Changed to 20 columns
                
                hot = new Handsontable(container, {
                    data: initialData,
                    rowHeaders: true,
                    nestedHeaders: [
                        [
                            {label: 'Date', colspan: 2}, // Date spans 2 columns
                            'Account Titles and Explanation', 
                            'Account Number', 
                            'Debit (₱)', 
                            'Credit (₱)',
                            '',
                            'Cash', 
                            'Accounts Receivable', 
                            'Supplies', 
                            'Furniture & Fixtures', 
                            'Land', 
                            'Equipment', 
                            'Accounts Payable', 
                            'Notes Payable', 
                            'Capital', 
                            'Withdrawal', 
                            'Service Revenue', 
                            'Rent Expense', 
                            'Paid Licenses', 
                            'Salaries Expense'
                        ],
                        [
                            '', // Sub-column 1 under Date
                            '',   // Sub-column 2 under Date
                            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
                        ]
                    ],
                    columns: [
                        { type: 'text', width: 100 }, // Month
                        { type: 'text', width: 100 }, // Day
                        { type: 'text', width: 400 },
                        { type: 'text', width: 100 },
                        { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 150 },
                        { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 150 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 120 },
                        { type: 'text', width: 100 },
                        { type: 'text', width: 120 }
                    ],
                    stretchH: 'all',
                    height: 'auto',
                    licenseKey: 'non-commercial-and-evaluation',
                    contextMenu: true,
                    manualColumnResize: true,
                    manualRowResize: true,
                    minSpareRows: 1,
                    afterRenderer: function (TD, row, col, prop, value, cellProperties) {
                        // Make the border after Credit column (now index 5) bold
                        if (col === 5) {
                            TD.style.borderRight = '3px solid #000000ff';
                        }
                    }
                });
                
                const answerKeyForm = document.getElementById("answerKeyForm");
                if (answerKeyForm) {
                    answerKeyForm.addEventListener("submit", function (e) {
                        e.preventDefault();
                        document.getElementById("correctData").value = JSON.stringify(hot.getData());
                        this.submit();
                    });
                }
            });
    </script>
    <style>
        body { overflow-x: hidden; }
        .handsontable td { border-color: #d1d5db; }
        .handsontable .area { background-color: rgba(147, 51, 234, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
    </style>
</x-app-layout>