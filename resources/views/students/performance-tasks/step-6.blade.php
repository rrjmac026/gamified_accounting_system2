<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>

    <div class="py-4 sm:py-6 lg:py-8">
        {{-- Flash Messages --}}
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

        <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

        <!-- Step Header -->
        <div class="mb-6 sm:mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Step 6 of 10</span>
            </div>

            <div class="flex-1">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                    Worksheet
                </h1>
                <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                    Prepare the worksheet by adjusting account balances and extending them to the appropriate financial statement columns.
                </p>
                <!-- Add attempts counter and status -->
                <div class="mt-2 flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                        Attempts remaining: {{ 2 - ($submission->attempts ?? 0) }}/2
                    </span>
                    @if($submission && $submission->status)
                        <span class="text-sm font-semibold {{ $submission->status === 'correct' ? 'text-green-600' : 'text-red-600' }}">
                            Status: {{ ucfirst($submission->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6 bg-blue-50 border-b border-blue-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-1">Worksheet Instructions</h3>
                        <p class="text-xs sm:text-sm text-blue-800">
                            {!! $performanceTask->description ?? 'Enter account titles and adjust the trial balance. Extend balances to Income Statement and Balance Sheet columns as appropriate.' !!}
                        </p>
                    </div>
                </div>
            </div>

            <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 6]) }}">
                @csrf

                <!-- Spreadsheet -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="submission_data" id="submission_data" required>
                    </div>

                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <!-- Buttons -->
                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                        <button type="button" onclick="window.history.back()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back
                        </button>

                        <button type="submit" id="submitButton" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm sm:text-base"
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

    <script>
        let hot;
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('spreadsheet');

            // Student's saved answers
            const savedData = @json($submission->submission_data ?? null);
            
            // ✅ Load saved or default data with 3 editable header rows
            let initialData = savedData
                ? JSON.parse(savedData)
                : [
                    ['Durano Enterprise', '', '', '', '', '', '', '', '', '', ''],  // Row 0: Company name
                    ['Trial Balance', '', '', '', '', '', '', '', '', '', ''],      // Row 1: Document title
                    ['Date: ____________________________', '', '', '', '', '', '', '', '', '', ''], // Row 2: Date
                    ['', '', '', '', '', '', '', '', '', '', ''], // Row 3: First data row
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', '', '']
                ];

            // Instructor's correct data
            const correctData = @json($answerSheet->correct_data ?? null);
            const submissionStatus = @json($submission->status ?? null);

            // Initialize HyperFormula
            const hyperformulaInstance = HyperFormula.buildEmpty({
                licenseKey: 'internal-use-in-handsontable',
            });

            const isMobile = window.innerWidth < 640;
            const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

            hot = new Handsontable(container, {
                data: initialData,
                rowHeaders: true,
                columns: [
                    { type: 'text', width: 200 },
                    ...Array(10).fill({ type: 'numeric', numericFormat: { pattern: '₱0,0.00' } })
                ],
                width: '100%',
                height: isMobile ? 350 : (isTablet ? 450 : 500),
                colWidths: [220, 120, 120, 120, 120, 120, 120, 120, 120, 120, 120],
                minCols: 11,
                stretchH: 'all',
                licenseKey: 'non-commercial-and-evaluation',
                formulas: { engine: hyperformulaInstance },
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
                minRows: 18,
                enterMoves: { row: 1, col: 0 },
                tabMoves: { row: 0, col: 1 },
                outsideClickDeselects: false,
                selectionMode: 'multiple',
                comments: true,
                customBorders: true,
                className: 'htCenter htMiddle',
                
                // ✅ Merge cells for the first 3 rows
                mergeCells: [
                    { row: 0, col: 0, rowspan: 1, colspan: 11 }, // Company name spans all columns
                    { row: 1, col: 0, rowspan: 1, colspan: 11 }, // Document title spans all columns
                    { row: 2, col: 0, rowspan: 1, colspan: 11 }  // Date spans all columns
                ],
                
                // ✅ Hide column headers completely
                colHeaders: false,
                
                // ✅ Custom cell rendering and styling
                cells: function(row, col) {
                    const cellProperties = {};
                    
                    // Row 0: Company name (editable, centered, bold)
                    if (row === 0) {
                        cellProperties.className = 'header-company';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Durano Enterprise') + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.fontSize = '16px';
                            td.style.backgroundColor = '#fafafa';
                        };
                    }
                    
                    // Row 1: Document title (editable, centered, bold)
                    else if (row === 1) {
                        cellProperties.className = 'header-title';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Trial Balance') + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.fontSize = '14px';
                            td.style.backgroundColor = '#fafafa';
                        };
                    }
                    
                    // Row 2: Date field (editable, centered, bold)
                    else if (row === 2) {
                        cellProperties.className = 'header-date';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Date: ____________________________') + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.fontSize = '13px';
                            td.style.backgroundColor = '#fafafa';
                            td.style.borderBottom = '2px solid #e5e7eb';
                        };
                    }
                    
                    // Row 3: Section headers (read-only)
                    else if (row === 3) {
                        cellProperties.readOnly = true;
                        cellProperties.className = 'section-headers';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            const sectionLabels = [
                                'Account Title',
                                'Unadjusted Trial Balance',
                                '',
                                'Adjustments', 
                                '',
                                'Adjusted Trial Balance',
                                '',
                                'Income Statement',
                                '',
                                'Balance Sheet',
                                ''
                            ];
                            
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + sectionLabels[col] + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.backgroundColor = '#f3f4f6';
                            td.style.fontWeight = '700';
                            td.style.borderBottom = '1px solid #d1d5db';
                        };
                    }
                    
                    // Row 4: Sub-headers (Debit/Credit) - read-only
                    else if (row === 4) {
                        cellProperties.readOnly = true;
                        cellProperties.className = 'sub-headers';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            const subLabels = ['', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit', 'Debit', 'Credit'];
                            
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + subLabels[col] + '</strong>';
                            td.style.textAlign = 'center';
                            td.style.backgroundColor = '#f3f4f6';
                            td.style.fontWeight = '700';
                            td.style.borderBottom = '2px solid #374151';
                        };
                    }
                    
                    // For data rows (row >= 5): Apply correct/incorrect coloring if submission has been graded
                    else if (row >= 5 && submissionStatus && correctData && savedData) {
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
                }
            });

            // ✅ Manually merge header cells for sections
            hot.updateSettings({
                mergeCells: [
                    { row: 0, col: 0, rowspan: 1, colspan: 11 }, // Company name
                    { row: 1, col: 0, rowspan: 1, colspan: 11 }, // Document title
                    { row: 2, col: 0, rowspan: 1, colspan: 11 }, // Date
                    // Section headers merge
                    { row: 3, col: 1, rowspan: 1, colspan: 2 }, // Unadjusted Trial Balance
                    { row: 3, col: 3, rowspan: 1, colspan: 2 }, // Adjustments
                    { row: 3, col: 5, rowspan: 1, colspan: 2 }, // Adjusted Trial Balance
                    { row: 3, col: 7, rowspan: 1, colspan: 2 }, // Income Statement
                    { row: 3, col: 9, rowspan: 1, colspan: 2 }, // Balance Sheet
                ]
            });

            // Handle responsive resize
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
        .handsontable .font-bold { font-weight: bold; }
        .handsontable .bg-gray-100 { background-color: #f3f4f6 !important; }
        .handsontable .bg-blue-50 { background-color: #eff6ff !important; }
        .handsontable td { border-color: #d1d5db; }
        .handsontable .area { background-color: rgba(59,130,246,0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }

        /* Correct/Incorrect answer styling - consistent with Step 2 */
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

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
    </style>
</x-app-layout>