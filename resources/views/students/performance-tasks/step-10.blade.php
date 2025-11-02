<x-app-layout>
    <!-- Handsontable + HyperFormula -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>

    <style>
        body { overflow-x: hidden; }
        .handsontable td {
            border-color: #d1d5db;
            vertical-align: middle;
            background-color: #ffffff;
        }
        
        .handsontable thead th {
            background-color: #f3f4f6;
            font-weight: 600;
            border-bottom: 2px solid #9ca3af;
        }
        
        .handsontable .htRight {
            text-align: right;
            padding-right: 8px;
        }
        
        .handsontable .htLeft {
            text-align: left;
            padding-left: 8px;
        }
        
        .handsontable .htCenter {
            text-align: center;
        }
        
        .handsontable .htMiddle {
            vertical-align: middle;
        }
        
        .handsontable tbody tr:hover {
            background-color: #f9fafb;
        }

        .handsontable .area { 
            background-color: rgba(147, 51, 234, 0.1); 
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

        /* Header rows styling */
        .handsontable td.header-title,
        .handsontable td.header-date {
            background-color: white !important;
            font-size: 14px;
            padding: 8px;
            font-weight: bold;
        }
        
        .handsontable td.header-columns {
            background-color: #f3f4f6 !important;
            font-weight: 700;
            border-bottom: 2px solid #374151 !important;
        }

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

        .handsontable td.cell-correct.area,
        .handsontable td.cell-correct.current {
            background-color: #bbf7d0 !important;
        }

        .handsontable td.cell-wrong.area,
        .handsontable td.cell-wrong.current {
            background-color: #fecaca !important;
        }

        .handsontable .font-bold { 
            font-weight: bold; 
        }

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
    </style>

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
                                <span>Step 10 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 100%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">100%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Post-Closing Trial Balance
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Prepare the post-closing trial balance to verify equality of debits and credits in permanent accounts after closing entries.
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
                <!-- Instructions Panel -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-800">Post-Closing Trial Balance Requirements:</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc ml-4 space-y-1">
                                    <li>Include only permanent (balance sheet) accounts</li>
                                    <li>List assets, liabilities, and owner's capital</li>
                                    <li>Verify that total debits equal total credits</li>
                                    <li>Temporary accounts should have zero balances</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 500px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                    </div>

                    <!-- Mobile Scroll Hint -->
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>

                    <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 10]) }}" class="mt-4 flex justify-end">
                        @csrf
                        <input type="hidden" name="submission_data" id="submission_data">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition"
                            {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                            💾 Save and Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    let hot;

    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById('spreadsheet');

        // ✅ Load saved submission data or default blank with 4 columns
        const savedData = @json($submission->submission_data ?? null);
        const initialData = savedData ? JSON.parse(savedData) : [
            ['POST-CLOSING TRIAL BALANCE', '', '', ''],
            ['December 31, 2024', '', '', ''],
            ['', '', '', ''],
            ['Account Title', '', 'Debit', 'Credit'],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['Totals', '', '', '']
        ];

        // ✅ Initialize HyperFormula with whitespace support
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any',
        });

        // ✅ Responsive detection
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // ✅ Handsontable initialization with 4 columns
        hot = new Handsontable(container, {
            data: initialData,
            columns: [
                { type: 'text' },
                { type: 'text' },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
            ],
            rowHeaders: true,
            width: '100%',
            height: isMobile ? 350 : (isTablet ? 450 : 600),
            licenseKey: 'non-commercial-and-evaluation',
            stretchH: 'all',
            className: 'htCenter htMiddle',

            // Formula support
            formulas: { engine: hyperformulaInstance },

            // Handle formula input
            beforeChange: function(changes, source) {
                if (changes) {
                    changes.forEach(function(change) {
                        if (change[3] && typeof change[3] === 'string' && change[3].startsWith('=')) {
                            change[3] = change[3].trim();
                        }
                    });
                }
            },

            // Full feature set
            contextMenu: true,
            undo: true,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: true,
            manualRowMove: true,
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 19,
            minCols: 4,
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: [
                { row: 0, col: 0, rowspan: 1, colspan: 4 },
                { row: 1, col: 0, rowspan: 1, colspan: 4 }
            ],
            comments: true,
            customBorders: true,
            minSpareRows: 0,

            // Cell styling and validation
            cells: function(row, col) {
                const cellProperties = {};
                const cellData = this.instance.getDataAtCell(row, col);

                // Formula cell styling
                if (cellData && typeof cellData === 'string' && cellData.startsWith('=')) {
                    cellProperties.className = (cellProperties.className || '') + ' formula-cell';
                }
                
                // Row 0: Title (POST-CLOSING TRIAL BALANCE)
                if (row === 0) {
                    cellProperties.className = 'header-title htCenter htMiddle';
                    cellProperties.readOnly = true;
                }
                
                // Row 1: Date
                if (row === 1) {
                    cellProperties.className = 'header-date htCenter htMiddle';
                    cellProperties.readOnly = true;
                }
                
                // Row 3: Column headers
                if (row === 3) {
                    cellProperties.readOnly = true;
                    cellProperties.className = 'header-columns htCenter htMiddle';
                    cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                        Handsontable.renderers.TextRenderer.apply(this, arguments);
                        td.innerHTML = '<strong>' + value + '</strong>';
                        td.style.textAlign = 'center';
                        td.style.backgroundColor = '#f3f4f6';
                    };
                }
                
                // Last row (Totals)
                if (row === 18) {
                    if (col === 0) {
                        cellProperties.readOnly = true;
                        cellProperties.className = 'font-bold htLeft htMiddle';
                    }
                }
                
                // Make column B (index 1) read-only for spacing
                if (col === 1) {
                    cellProperties.readOnly = true;
                }
                
                // Data rows (4-17) - add validation coloring
                if (row > 3 && row < 18) {
                    cellProperties.readOnly = false;

                    const submissionStatus = @json($submission->status ?? null);
                    const correctData = @json($answerSheet->correct_data ?? null);
                    const savedData = @json($submission->submission_data ?? null);

                    if (submissionStatus && correctData && savedData) {
                        const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                        const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                        
                        const studentValue = parsedStudent[row]?.[col];
                        const correctValue = parsedCorrect[row]?.[col];
                        
                        // Skip column B (spacing column)
                        if (col !== 1 && studentValue !== null && studentValue !== undefined && studentValue !== '') {
                            const normalizedStudent = String(studentValue).trim().toLowerCase();
                            const normalizedCorrect = String(correctValue || '').trim().toLowerCase();
                            
                            if (normalizedStudent === normalizedCorrect) {
                                cellProperties.className = 'cell-correct';
                            } else {
                                cellProperties.className = 'cell-wrong';
                            }
                        }
                    }
                }
                
                return cellProperties;
            }
        });

        // ✅ Responsive behavior
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                const newIsMobile = window.innerWidth < 640;
                const newIsTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
                const newHeight = newIsMobile ? 350 : (newIsTablet ? 450 : 600);

                hot.updateSettings({
                    height: newHeight
                });
            }, 250);
        });

        // ✅ Save submission data
        const saveForm = document.getElementById("saveForm");
        if (saveForm) {
            saveForm.addEventListener("submit", function (e) {
                e.preventDefault();
                document.getElementById("submission_data").value = JSON.stringify(hot.getData());
                this.submit();
            });
        }
    });
</script>
</x-app-layout>