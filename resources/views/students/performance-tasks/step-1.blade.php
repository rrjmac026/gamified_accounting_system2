<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>
    
    <!-- Main Container with proper spacing -->
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
            
            <!-- Flash Messages Container -->
            <div class="mb-6 space-y-4">
                @if (session('error'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                                <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                            </div>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                                <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                            </div>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

            <!-- Enhanced Header Container with Card Design -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <!-- Colored Top Bar -->
                    <div class="h-2 bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 1 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 10%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">10%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Analyzing Transactions
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Identify which accounts are affected by each transaction and determine whether they should be debited or credited before recording them in the journal.
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
                                    <p class="text-lg font-bold text-amber-900">{{ $performanceTask->max_attempts - ($submission->attempts ?? 0) }}/{{ $performanceTask->max_attempts }}</p>
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
                <div class="p-4 sm:p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-blue-900 mb-1">Instructions</h3>
                            <div class="text-sm text-blue-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'No instructions provided by your instructor.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <form id="taskForm" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => $step ?? 1]) }}" method="POST">
                    @csrf
                    <!-- Spreadsheet Section -->
                    <div class="p-3 sm:p-4 lg:p-6">
                        <!-- Spreadsheet Container with Scroll -->
                        <div class="border-2 border-gray-300 rounded-xl shadow-inner bg-gray-50 overflow-hidden">
                            <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                                <div id="spreadsheet" class="bg-white min-w-full"></div>
                            </div>
                            <input type="hidden" name="submission_data" id="submissionData" required>
                        </div>

                        <!-- Mobile Scroll Hint -->
                        <div class="mt-3 flex items-center justify-center gap-2 text-xs text-gray-500 sm:hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>Swipe to scroll spreadsheet</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                            <button type="button" onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all text-sm font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </button>
                            <button type="submit" id="submitButton" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ ($submission->attempts ?? 0) >= $performanceTask->max_attempts ? 'disabled' : '' }}>
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

    <!-- Keep all your existing scripts and styles -->
<script>
    let hot;
    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById('spreadsheet');
        const savedData = @json($submission->submission_data ?? null);

        // Header rows as editable data
        const headerRow1 = ['', 'ASSETS', '', '', '', '', '', 'LIABILITIES', '', "OWNER'S EQUITY", '', '', 'EXPENSES', '', ''];
        const headerRow2 = ['', 'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixtures', 'Land', 'Equipment', 'Accounts Payable', 'Notes Payable', 'Capital', 'Withdrawal', 'Service Revenue', 'Rent Expense', 'Utilities Expense', 'Salaries Expense', 'Misc. Expense'];
        const blankRows = Array(15).fill(null).map(() => Array(15).fill(''));

        const initialData = savedData ? JSON.parse(savedData) : [headerRow1, headerRow2, ...blankRows];

        const correctData = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);
        const maxAttempts = @json($performanceTask->max_attempts);
        const currentAttempts = @json($submission->attempts ?? 0);
        const isReadOnly = currentAttempts >= maxAttempts;

        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        hot = new Handsontable(container, {
            data: initialData,
            colHeaders: false,
            rowHeaders: true,
            width: '100%',
            height: isMobile ? 350 : (isTablet ? 450 : 500),
            licenseKey: 'non-commercial-and-evaluation',
            readOnly: isReadOnly,

            columns: Array(15).fill({ type: 'text' }),
            colWidths: isMobile ? 100 : (isTablet ? 110 : 120),

            // Merge cells to replicate the colspan behavior of nestedHeaders
            mergeCells: [
                { row: 0, col: 1, rowspan: 1, colspan: 6 },  // ASSETS
                { row: 0, col: 7, rowspan: 1, colspan: 2 },  // LIABILITIES
                { row: 0, col: 9, rowspan: 1, colspan: 3 },  // OWNER'S EQUITY
                { row: 0, col: 12, rowspan: 1, colspan: 4 }, // EXPENSES
            ],

            contextMenu: !isReadOnly,
            undo: !isReadOnly,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: !isReadOnly,
            manualRowMove: !isReadOnly,
            fillHandle: !isReadOnly,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: !isReadOnly,
            minRows: 17,
            minCols: 15,
            maxRows: 52,
            maxCols: 20,
            stretchH: 'none',
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            comments: true,
            customBorders: true,

            afterRenderer: function (TD, row, col, prop, value, cellProperties) {

                // Style row 0 and row 1 — plain, bold, centered, no color
                if (row === 0 || row === 1) {
                    TD.style.fontWeight = 'bold';
                    TD.style.textAlign = 'center';
                    TD.style.verticalAlign = 'middle';
                    TD.style.whiteSpace = 'normal';
                    TD.style.wordBreak = 'break-word';
                    return; // Skip answer checking for header rows
                }

                // Answer checking styling for data rows (row 2 onwards)
                if (submissionStatus && correctData && savedData) {
                    try {
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
                    } catch (error) {
                        console.warn('Error applying answer styling:', error);
                    }
                }
            }
        });

        // Responsive resize handler
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                const newIsMobile = window.innerWidth < 640;
                const newIsTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
                const newHeight = newIsMobile ? 350 : (newIsTablet ? 450 : 500);
                hot.updateSettings({
                    height: newHeight,
                    colWidths: newIsMobile ? 100 : (newIsTablet ? 110 : 120)
                });
            }, 250);
        });

        // Capture spreadsheet data on submit
        const taskForm = document.getElementById("taskForm");
        if (taskForm && !isReadOnly) {
            taskForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const data = hot.getData();
                document.getElementById("submissionData").value = JSON.stringify(data);
                this.submit();
            });
        }

        // Add CSS for answer styling only
        const style = document.createElement('style');
        style.textContent = `
            .cell-correct {
                background-color: #dcfce7 !important;
                border: 2px solid #16a34a !important;
                color: #166534 !important;
            }
            .cell-wrong {
                background-color: #fee2e2 !important;
                border: 2px solid #dc2626 !important;
                color: #991b1b !important;
            }

            /* Prevent selected cells from overriding correct/wrong colors */
            .handsontable td.cell-correct.area,
            .handsontable td.cell-correct.current {
                background-color: #bbf7d0 !important;
            }
            .handsontable td.cell-wrong.area,
            .handsontable td.cell-wrong.current {
                background-color: #fecaca !important;
            }
        `;
        document.head.appendChild(style);
    });
</script>

    <style>
        .cell-correct {
            background-color: #dcfce7 !important;
            border: 2px solid #16a34a !important;
        }
        .cell-wrong {
            background-color: #fee2e2 !important;
            border: 2px solid #dc2626 !important;
        }
        .handsontable td.cell-correct.area,
        .handsontable td.cell-correct.current {
            background-color: #bbf7d0 !important;
        }
        .handsontable td.cell-wrong.area,
        .handsontable td.cell-wrong.current {
            background-color: #fecaca !important;
        }
        .handsontable.readOnly td {
            background-color: #f9fafb;
            cursor: not-allowed;
        }
        body {
            overflow-x: hidden;
        }
        .handsontable .font-bold {
            font-weight: bold;
        }
        .handsontable .bg-gray-100 {
            background-color: #f3f4f6 !important;
        }
        .handsontable .bg-blue-50 {
            background-color: #eff6ff !important;
        }
        .handsontable td {
            border-color: #d1d5db;
        }
        .handsontable .area {
            background-color: rgba(59, 130, 246, 0.1);
        }
        .border-red-500 {
            border-color: #ef4444 !important;
        }
        .handsontable {
            position: relative;
            z-index: 1;
        }
        #spreadsheet {
            isolation: isolate;
        }
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
        @media (max-width: 640px) {
            .handsontable {
                font-size: 12px;
            }
            .handsontable th,
            .handsontable td {
                padding: 4px;
            }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable {
                font-size: 13px;
            }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
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