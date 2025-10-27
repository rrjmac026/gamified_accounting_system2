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
                    <span>Answer Key - Step 6 of 10</span>
                </div>
                
                <!-- Title Section -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                            Answer Key: Worksheet
                        </h1>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                            Create the correct answer key for the Worksheet. This will be used to automatically grade student submissions.
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
                            Fill in the correct answers below. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                        </p>
                    </div>
                </div>
            </div>

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 6]) }}" method="POST">
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
        const savedData = @json($sheet->correct_data ?? null);
        
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
            
            // ✅ Hide column headers completely - we'll add them manually
            colHeaders: false,
            
            // ✅ Add custom headers using afterGetRowHeader hook
            afterGetRowHeader: function(row, TH) {
                // Add a data attribute to identify header rows
                if (row < 3) {
                    TH.style.backgroundColor = '#fafafa';
                }
            },
            
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
                        const labels = ['Account Title', 'Unadjusted TB', '', 'Adjustments', '', 'Adjusted TB', '', 'Income Stmt', '', 'Balance Sheet', ''];
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

        // Save data on submit
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
    /* Center and style the top report headers */
    .handsontable thead th.report-header {
        text-align: center !important;
        font-weight: 700 !important;
        font-size: 1rem !important;
        background-color: #fafafa !important;
        border-bottom: none !important;
    }

    /* Optional: give subtle separator line before table content */
    .handsontable thead tr:nth-child(3) th.report-header {
        border-bottom: 2px solid #e5e7eb !important;
    }

    @media (max-width: 640px) {
        .handsontable { font-size: 12px; }
    }
</style>


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

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
    </style>
</x-app-layout>