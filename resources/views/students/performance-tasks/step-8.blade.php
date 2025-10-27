<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>

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
                    <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 8 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 80%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">80%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Balance Sheet
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Prepare the balance sheet showing assets, liabilities, and owner's equity as of the end of the period.
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

            <div class="py-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div id="spreadsheet" class="overflow-x-auto"></div>

                    <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 8]) }}">
                        @csrf
                        <input type="hidden" name="submission_data" id="submission_data">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition"
                            {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                            💾 Save and Continue
                        </button>
                    </form>
                </div>
            </div>

            <script>
                let hot;
                document.addEventListener('DOMContentLoaded', function () {
                    const container = document.getElementById('spreadsheet');

                    // Student's saved answers
                    const savedData = @json($submission->submission_data ?? null);
                    const initialData = savedData
                        ? JSON.parse(savedData)
                        : Array.from({ length: 15 }, () => Array(20).fill(''));

                    // Instructor's correct data
                    const correctData = @json($answerSheet->correct_data ?? null);
                    const submissionStatus = @json($submission->status ?? null);

                    // Initialize HyperFormula for Excel-like formulas
                    const hyperformulaInstance = HyperFormula.buildEmpty({
                        licenseKey: 'internal-use-in-handsontable',
                    });

                    // Initialize Handsontable
                    hot = new Handsontable(container, {
                        data: initialData,
                        rowHeaders: true,
                        // Using nested headers like Step 5
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
                                '', // Sub-column 2 under Date
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
                        height: 500,
                        minSpareRows: 1,
                        licenseKey: 'non-commercial-and-evaluation',
                        formulas: { engine: hyperformulaInstance },
                        contextMenu: true,
                        undo: true,
                        manualColumnResize: true,
                        manualRowResize: true,
                        fillHandle: true,
                        autoColumnSize: false,
                        autoRowSize: false,
                        copyPaste: true,
                        enterMoves: { row: 1, col: 0 },
                        tabMoves: { row: 0, col: 1 },
                        outsideClickDeselects: false,
                        selectionMode: 'multiple',
                        cells: function(row, col) {
                            const cellProperties = {};
                            
                            // Only apply correct/incorrect coloring if submission has been graded
                            if (submissionStatus && correctData && savedData) {
                                const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                                const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                                
                                const studentValue = parsedStudent[row]?.[col];
                                const correctValue = parsedCorrect[row]?.[col];
                                
                                // Only compare non-empty cells that the STUDENT filled in
                                if (studentValue !== null && studentValue !== undefined && studentValue !== '') {
                                    // Normalize values for comparison (trim whitespace, case-insensitive)
                                    const normalizedStudent = String(studentValue).trim().toLowerCase();
                                    const normalizedCorrect = String(correctValue || '').trim().toLowerCase();
                                    
                                    if (normalizedStudent === normalizedCorrect) {
                                        cellProperties.className = 'cell-correct';
                                    } else {
                                        cellProperties.className = 'cell-wrong';
                                    }
                                }
                            }
                            
                            return cellProperties;
                        },
                        afterRenderer: function (TD, row, col, prop, value, cellProperties) {
                            // Make the border after Credit column (now index 5) bold
                            if (col === 5) {
                                TD.style.borderRight = '3px solid #000000ff';
                            }
                        }
                    });

                    // Keep data synced before form submission
                    const form = document.getElementById('saveForm');
                    form.addEventListener('submit', function (e) {
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
                    background-color: #ffffff; /* Default white background */
                }
                .handsontable th { background-color: #f3f4f6; font-weight: 600; }
                .handsontable .area { background-color: rgba(59,130,246,0.1); }
                .handsontable { position: relative; z-index: 1; }
                #spreadsheet { isolation: isolate; }

                /* Correct/Incorrect answer styling - consistent with Step 6 */
                .handsontable td.cell-correct {
                    background-color: #dcfce7 !important; /* Light green */
                    border: 2px solid #16a34a !important; /* Green border */
                    color: #166534;
                }

                .handsontable td.cell-wrong {
                    background-color: #fee2e2 !important; /* Light red */
                    border: 2px solid #dc2626 !important; /* Red border */
                    color: #991b1b;
                }

                /* Prevent selected cells from overriding colors */
                .handsontable td.cell-correct.area,
                .handsontable td.cell-correct.current {
                    background-color: #bbf7d0 !important; /* Slightly darker green when selected */
                }

                .handsontable td.cell-wrong.area,
                .handsontable td.cell-wrong.current {
                    background-color: #fecaca !important; /* Slightly darker red when selected */
                }
            </style>
        </div>
    </div>
</x-app-layout>