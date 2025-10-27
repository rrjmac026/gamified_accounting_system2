<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>
    
    <div class="py-4 sm:py-6 lg:py-8">
        @if (session('error'))
            <div class="mb-6 animate-slideDown">
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
            <div class="mb-6 animate-slideDown">
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

        <!-- Enhanced Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="relative">
                <!-- Step Indicator Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Answer Key - Step 7 of 10</span>
                </div>
                
                <!-- Title Section -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                            Answer Key: Financial Statements
                        </h1>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                            Create the correct answer key for the Financial Statements following McGraw Hill format. This will be used to automatically grade student submissions.
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
                            Fill in the correct answers below following McGraw Hill financial statement format. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                        </p>
                    </div>
                </div>
            </div>

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 7]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Spreadsheet Section -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>

                    <!-- Mobile Scroll Hint -->
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <!-- Action Buttons -->
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
            
            // Get saved answer key data if it exists
            const savedData = @json($sheet->correct_data ?? null);
            
            // Initialize with McGraw Hill financial statement structure
            const initialData = savedData ? JSON.parse(savedData) : [
                // INCOME STATEMENT - McGraw Hill Format
                ['', 'Durano Enterprise', '', ''],
                ['', 'Income Statement', '', ''],
                ['', 'For the Month Ended February 29, 2024', '', ''],
                ['', '', '', ''],
                ['Revenues', '', '', ''],
                ['Service revenue', '', '', ''],
                ['Total revenues', '', '', ''],
                ['', '', '', ''],
                ['Expenses', '', '', ''],
                ['Rent expense', '', '', ''],
                ['Utilities expense', '', '', ''],
                ['Salaries expense', '', '', ''],
                ['Supplies expense', '', '', ''],
                ['Depreciation expense', '', '', ''],
                ['Total expenses', '', '', ''],
                ['', '', '', ''],
                ['Net income', '', '', ''],
                ['', '', '', ''],
                ['', '', '', ''],
                
                // STATEMENT OF OWNER'S EQUITY - McGraw Hill Format
                ['', 'Durano Enterprise', '', ''],
                ['', 'Statement of Owner\'s Equity', '', ''],
                ['', 'For the Month Ended February 29, 2024', '', ''],
                ['', '', '', ''],
                ['Durano, Capital, February 1, 2024', '', '', ''],
                ['Add: Investments by owner', '', '', ''],
                ['Net income for the month', '', '', ''],
                ['', '', '', ''],
                ['Less: Withdrawals by owner', '', '', ''],
                ['Increase in capital', '', '', ''],
                ['Durano, Capital, February 29, 2024', '', '', ''],
                ['', '', '', ''],
                ['', '', '', ''],
                
                // BALANCE SHEET - McGraw Hill Format
                ['', 'Durano Enterprise', '', ''],
                ['', 'Balance Sheet', '', ''],
                ['', 'February 29, 2024', '', ''],
                ['', '', '', ''],
                ['Assets', '', '', ''],
                ['Cash', '', '', ''],
                ['Accounts receivable', '', '', ''],
                ['Supplies', '', '', ''],
                ['Total current assets', '', '', ''],
                ['', '', '', ''],
                ['Equipment', '', '', ''],
                ['Less: Accumulated depreciation—Equipment', '', '', ''],
                ['', '', '', ''],
                ['Furniture and fixtures', '', '', ''],
                ['Less: Accumulated depreciation—Furniture and fixtures', '', '', ''],
                ['', '', '', ''],
                ['Land', '', '', ''],
                ['Total property, plant and equipment', '', '', ''],
                ['Total assets', '', '', ''],
                ['', '', '', ''],
                ['Liabilities', '', '', ''],
                ['Accounts payable', '', '', ''],
                ['Utilities payable', '', '', ''],
                ['Notes payable', '', '', ''],
                ['Total liabilities', '', '', ''],
                ['', '', '', ''],
                ['Owner\'s Equity', '', '', ''],
                ['Durano, Capital', '', '', ''],
                ['Total liabilities and owner\'s equity', '', '', '']
            ];

            // Initialize HyperFormula for Excel-like formulas
            const hyperformulaInstance = HyperFormula.buildEmpty({
                licenseKey: 'internal-use-in-handsontable',
            });

            // Determine responsive dimensions with better calculations
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const isMobile = viewportWidth < 640;
            const isTablet = viewportWidth >= 640 && viewportWidth < 1024;
            const isDesktop = viewportWidth >= 1024;

            // Calculate optimal table height based on viewport
            let tableHeight;
            if (isMobile) {
                tableHeight = Math.min(Math.max(viewportHeight * 0.5, 350), 500);
            } else if (isTablet) {
                tableHeight = Math.min(Math.max(viewportHeight * 0.6, 450), 600);
            } else {
                tableHeight = Math.min(Math.max(viewportHeight * 0.65, 550), 700);
            }

            // Calculate optimal column widths
            let colWidths;
            if (isMobile) {
                const availableWidth = Math.min(viewportWidth - 60, 440);
                colWidths = [
                    Math.floor(availableWidth * 0.30),  // Account Title: 30%
                    Math.floor(availableWidth * 0.35),  // Description: 35%
                    Math.floor(availableWidth * 0.175), // Debit: 17.5%
                    Math.floor(availableWidth * 0.175)  // Credit: 17.5%
                ];
            } else if (isTablet) {
                colWidths = [180, 200, 120, 120];
            } else {
                colWidths = [220, 260, 140, 140];
            }
                
            hot = new Handsontable(container, {
                data: initialData,
                rowHeaders: true,
                colHeaders: [
                    'Account Title',
                    'Description',
                    'Debit (₱)',
                    'Credit (₱)'
                ],
                columns: [
                    { 
                        type: 'text',
                        renderer: function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            
                            // Style statement titles
                            if (value && value.includes('Durano Enterprise')) {
                                td.style.fontWeight = 'bold';
                                td.style.fontSize = '14px';
                                td.style.textAlign = 'center';
                            }
                            
                            // Style main statement headers
                            if (value && (
                                value.includes('Income Statement') ||
                                value.includes('Statement of Owner\'s Equity') ||
                                value.includes('Balance Sheet') ||
                                value.includes('For the Month Ended') ||
                                value.includes('February 29, 2024')
                            )) {
                                td.style.fontWeight = 'bold';
                                td.style.textAlign = 'center';
                            }
                            
                            // Style major categories
                            if (value && (
                                value === 'Revenues' ||
                                value === 'Expenses' ||
                                value === 'Assets' ||
                                value === 'Liabilities' ||
                                value === 'Owner\'s Equity' ||
                                value === 'Total current assets' ||
                                value === 'Total property, plant and equipment'
                            )) {
                                td.style.fontWeight = 'bold';
                                td.style.backgroundColor = '#f8fafc';
                            }
                            
                            // Style total rows
                            if (value && (
                                value.includes('Total revenues') ||
                                value.includes('Total expenses') ||
                                value.includes('Net income') ||
                                value.includes('Total assets') ||
                                value.includes('Total liabilities') ||
                                value.includes('Total liabilities and owner\'s equity') ||
                                value.includes('Durano, Capital, February 29, 2024')
                            )) {
                                td.style.fontWeight = 'bold';
                                td.style.borderTop = '2px solid #4b5563';
                            }
                            
                            // Indent sub-accounts
                            if (value && !value.includes('Durano') && !value.includes('Total') && 
                                !value.includes('Revenues') && !value.includes('Expenses') && 
                                !value.includes('Assets') && !value.includes('Liabilities') && 
                                !value.includes('Owner\'s Equity') && value !== 'Net income' &&
                                value !== 'Add: Investments by owner' && value !== 'Less: Withdrawals by owner' &&
                                value !== 'Increase in capital') {
                                td.style.paddingLeft = '20px';
                            }
                        }
                    },
                    { 
                        type: 'text',
                        renderer: function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.style.textAlign = 'center';
                        }
                    },
                    { 
                        type: 'numeric', 
                        numericFormat: { pattern: '₱0,0.00' },
                        renderer: function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.NumericRenderer.apply(this, arguments);
                            if (instance.getDataAtCell(row, 0) && (
                                instance.getDataAtCell(row, 0).includes('Total revenues') ||
                                instance.getDataAtCell(row, 0).includes('Total expenses') ||
                                instance.getDataAtCell(row, 0).includes('Net income') ||
                                instance.getDataAtCell(row, 0).includes('Total assets') ||
                                instance.getDataAtCell(row, 0).includes('Total liabilities') ||
                                instance.getDataAtCell(row, 0).includes('Total liabilities and owner\'s equity') ||
                                instance.getDataAtCell(row, 0).includes('Durano, Capital, February 29, 2024')
                            )) {
                                td.style.fontWeight = 'bold';
                                td.style.borderTop = '2px solid #4b5563';
                            }
                        }
                    },
                    { 
                        type: 'numeric', 
                        numericFormat: { pattern: '₱0,0.00' },
                        renderer: function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.NumericRenderer.apply(this, arguments);
                            if (instance.getDataAtCell(row, 0) && (
                                instance.getDataAtCell(row, 0).includes('Total revenues') ||
                                instance.getDataAtCell(row, 0).includes('Total expenses') ||
                                instance.getDataAtCell(row, 0).includes('Net income') ||
                                instance.getDataAtCell(row, 0).includes('Total assets') ||
                                instance.getDataAtCell(row, 0).includes('Total liabilities') ||
                                instance.getDataAtCell(row, 0).includes('Total liabilities and owner\'s equity') ||
                                instance.getDataAtCell(row, 0).includes('Durano, Capital, February 29, 2024')
                            )) {
                                td.style.fontWeight = 'bold';
                                td.style.borderTop = '2px solid #4b5563';
                            }
                        }
                    }
                ],
                width: '100%',
                height: tableHeight,
                colWidths: colWidths,
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
                minRows: 45,
                minSpareRows: 1,
                stretchH: 'all',
                enterMoves: { row: 1, col: 0 },
                tabMoves: { row: 0, col: 1 },
                outsideClickDeselects: false,
                selectionMode: 'multiple',
                className: 'htLeft htMiddle',
                mergeCells: [
                    { row: 0, col: 1, rowspan: 1, colspan: 3 },
                    { row: 1, col: 1, rowspan: 1, colspan: 3 },
                    { row: 2, col: 1, rowspan: 1, colspan: 3 },
                    { row: 19, col: 1, rowspan: 1, colspan: 3 },
                    { row: 20, col: 1, rowspan: 1, colspan: 3 },
                    { row: 21, col: 1, rowspan: 1, colspan: 3 },
                    { row: 32, col: 1, rowspan: 1, colspan: 3 },
                    { row: 33, col: 1, rowspan: 1, colspan: 3 },
                    { row: 34, col: 1, rowspan: 1, colspan: 3 }
                ],
            });

            // Improved window resize handler with debouncing
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const newViewportWidth = window.innerWidth;
                    const newViewportHeight = window.innerHeight;
                    const newIsMobile = newViewportWidth < 640;
                    const newIsTablet = newViewportWidth >= 640 && newViewportWidth < 1024;
                    const newIsDesktop = newViewportWidth >= 1024;
                    
                    // Recalculate height
                    let newHeight;
                    if (newIsMobile) {
                        newHeight = Math.min(Math.max(newViewportHeight * 0.5, 350), 500);
                    } else if (newIsTablet) {
                        newHeight = Math.min(Math.max(newViewportHeight * 0.6, 450), 600);
                    } else {
                        newHeight = Math.min(Math.max(newViewportHeight * 0.65, 550), 700);
                    }
                    
                    // Recalculate column widths
                    let newColWidths;
                    if (newIsMobile) {
                        const availableWidth = Math.min(newViewportWidth - 60, 440);
                        newColWidths = [
                            Math.floor(availableWidth * 0.30),
                            Math.floor(availableWidth * 0.35),
                            Math.floor(availableWidth * 0.175),
                            Math.floor(availableWidth * 0.175)
                        ];
                    } else if (newIsTablet) {
                        newColWidths = [180, 200, 120, 120];
                    } else {
                        newColWidths = [220, 260, 140, 140];
                    }
                    
                    hot.updateSettings({
                        height: newHeight,
                        colWidths: newColWidths
                    });
                }, 250);
            });

            // Capture spreadsheet data on submit
            const answerKeyForm = document.getElementById("answerKeyForm");
            if (answerKeyForm) {
                answerKeyForm.addEventListener("submit", function (e) {
                    e.preventDefault();
                    const data = hot.getData();
                    document.getElementById("correctData").value = JSON.stringify(data);
                    this.submit();
                });
            }
        });
    </script>

    <style>
        body { overflow-x: hidden; }
        .handsontable .font-bold { font-weight: bold; }
        .handsontable .bg-gray-100 { background-color: #f3f4f6 !important; }
        .handsontable .bg-blue-50 { background-color: #eff6ff !important; }
        .handsontable td { border-color: #d1d5db; }
        .handsontable .area { background-color: rgba(147, 51, 234, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
        .handsontable .htMerge { background-color: #f8fafc; }

        /* McGraw Hill style formatting */
        .handsontable th {
            background-color: #e5e7eb;
            font-weight: 600;
        }

        .handsontable .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        @media (max-width: 640px) {
            .handsontable { font-size: 11px; }
            .handsontable th, .handsontable td { padding: 3px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 12px; }
        }
    </style>
</x-app-layout>