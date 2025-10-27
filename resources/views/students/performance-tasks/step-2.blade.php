<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

    <!-- Main Container with proper spacing -->
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
            
            <!-- Flash Messages Container -->
            <div class="mb-6 space-y-4">
                @if (session('error'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                                <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                                <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

            <!-- Enhanced Header Container with Card Design -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <!-- Colored Top Bar -->
                    <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-full text-sm font-semibold border border-indigo-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 2 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-full rounded-full transition-all duration-500" style="width: 20%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">20%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Journalizing Entries
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Record transactions in the journal with proper account titles, numbers, debits, and credits.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Meta Information Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <!-- Attempts Card -->
                            <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-amber-600 font-medium">Attempts Remaining</p>
                                    <p class="text-lg font-bold text-amber-900">{{ 2 - ($submission->attempts ?? 0) }}/2</p>
                                </div>
                            </div>
                            
                            <!-- Status Card (if applicable) -->
                            @if($submission && $submission->status)
                            <div class="flex items-center gap-3 p-3 {{ $submission->status === 'correct' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} rounded-lg border">
                                <div class="flex-shrink-0 w-10 h-10 {{ $submission->status === 'correct' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $submission->status === 'correct' ? 'text-green-600' : 'text-red-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        @if($submission->status === 'correct')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        @else
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs {{ $submission->status === 'correct' ? 'text-green-600' : 'text-red-600' }} font-medium">Status</p>
                                    <p class="text-lg font-bold {{ $submission->status === 'correct' ? 'text-green-900' : 'text-red-900' }}">{{ ucfirst($submission->status) }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Score Card (if applicable) -->
                            @if(isset($submission->score))
                            <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-600 font-medium">Score</p>
                                    <p class="text-lg font-bold text-purple-900">{{ $submission->score }}%</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <!-- Instructions Section -->
                <div class="p-4 sm:p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-indigo-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-indigo-900 mb-1">Journal Entry Instructions</h3>
                            <div class="text-sm text-indigo-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Record each transaction with the date, account titles, account numbers, and the corresponding debit and credit amounts.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 2]) }}">
                    @csrf

                    <!-- Spreadsheet -->
                    <div class="p-3 sm:p-4 lg:p-6">
                        <div class="border-2 border-gray-300 rounded-xl shadow-inner bg-gray-50 overflow-hidden">
                            <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                                <div id="spreadsheet" class="bg-white min-w-full"></div>
                            </div>
                            <input type="hidden" name="submission_data" id="submission_data" required>
                        </div>

                        <div class="mt-3 flex items-center justify-center gap-2 text-xs text-gray-500 sm:hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>Swipe to scroll spreadsheet</span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                            <button type="button" onclick="window.history.back()" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all text-sm font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </button>

                            <button type="submit" id="submitButton" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save and Continue
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
    let hot;
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('spreadsheet');

        // Student's saved answers
        const savedData = @json($submission->submission_data ?? null);
        const initialData = savedData ? JSON.parse(savedData) : Array.from({ length: 15 }, () => Array(21).fill(''));

        // Instructor's correct data
        const correctData = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);

        // Create columns config
        const columnsConfig = [
            { type: 'text', width: 100 }, // Month
            { type: 'text', width: 100 }, // Day
            { type: 'text', width: 400 },
            { type: 'text', width: 100 },
            { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 150 },
            { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 150 },
            { type: 'text', width: 100 }, // Blank Column
            { type: 'text', width: 100 }, // Cash
            { type: 'text', width: 120 }, // Accounts Receivable
            { type: 'text', width: 100 }, // Supplies
            { type: 'text', width: 120 }, // Furniture & Fixtures
            { type: 'text', width: 100 }, // Land
            { type: 'text', width: 100 }, // Equipment
            { type: 'text', width: 120 }, // Accounts Payable
            { type: 'text', width: 120 }, // Notes Payable
            { type: 'text', width: 100 }, // Capital
            { type: 'text', width: 100 }, // Withdrawal
            { type: 'text', width: 120 }, // Service Revenue
            { type: 'text', width: 120 }, // Rent Expense
            { type: 'text', width: 100 }, // Paid Licenses
            { type: 'text', width: 120 }, // Salaries Expense
        ];

        // Initialize Handsontable with nested headers
        hot = new Handsontable(container, {
            data: initialData,
            columns: columnsConfig,
            rowHeaders: true,
            nestedHeaders: [
                [
                    {label: 'Date', colspan: 2},
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
                    'Month',
                    'Day',
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
                ]
            ],
            stretchH: 'all',
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',
            contextMenu: true,
            manualColumnResize: true,
            manualRowResize: true,
            minSpareRows: 1,
            afterRenderer: function (TD, row, col, prop, value, cellProperties) {
                if (col === 6) {
                    TD.style.borderLeft = '3px solid #000000ff';
                }
                
                if (submissionStatus && correctData && savedData) {
                    const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                    const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                    
                    const studentValue = parsedStudent[row]?.[col];
                    const correctValue = parsedCorrect[row]?.[col];
                    
                    if (studentValue !== null && studentValue !== undefined && studentValue !== '') {
                        const normalizedStudent = String(studentValue).trim().toLowerCase();
                        const normalizedCorrect = String(correctValue || '').trim().toLowerCase();
                        
                        if (normalizedStudent === normalizedCorrect) {
                            TD.classList.add('cell-correct');
                        } else {
                            TD.classList.add('cell-wrong');
                        }
                    }
                }
            }
        });

        // Save submission data
        const form = document.getElementById('saveForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('submission_data').value = JSON.stringify(hot.getData());
            this.submit();
        });
    });
</script>

    <style>
        body { overflow-x: hidden; }
        .handsontable td { 
            border-color: #d1d5db;
            background-color: #ffffff;
        }
        .handsontable .area { background-color: rgba(99, 102, 241, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }

        /* Correct/Incorrect answer styling */
        .handsontable td.cell-correct {
            background-color: #dcfce7 !important;
            border: 2px solid #16a34a !important;
            color: #166534;
        }

        .handsontable td.cell-wrong {
            background-color: #fee2e2 !important;
            border: 2px solid #dc2626 !important;
            color: #991b1b;
        }

        /* Prevent selected cells from overriding colors */
        .handsontable td.cell-correct.area,
        .handsontable td.cell-correct.current {
            background-color: #bbf7d0 !important;
        }

        .handsontable td.cell-wrong.area,
        .handsontable td.cell-wrong.current {
            background-color: #fecaca !important;
        }

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }

        /* Animation for flash messages */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
    </style>
</x-app-layout>