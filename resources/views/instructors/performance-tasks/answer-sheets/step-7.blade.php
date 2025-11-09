<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>
    
    <style>
        /* Enhanced Header Section Styles */
        .answer-key-header {
            background: linear-gradient(135deg, #f9fafb 0%, #f3e8ff 50%, #faf5ff 100%);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e9d5ff;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1), 0 2px 4px -1px rgba(139, 92, 246, 0.06);
            position: relative;
            overflow: hidden;
        }

        .answer-key-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .answer-key-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(196, 181, 253, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3), 0 2px 4px -1px rgba(139, 92, 246, 0.2);
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .step-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(139, 92, 246, 0.4), 0 3px 5px -1px rgba(139, 92, 246, 0.3);
        }

        .step-badge svg {
            width: 1rem;
            height: 1rem;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .header-title {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-description {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.625;
            max-width: 48rem;
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .task-info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            background: white;
            color: #7c3aed;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #e9d5ff;
            box-shadow: 0 1px 3px 0 rgba(139, 92, 246, 0.1);
            position: relative;
            z-index: 1;
            transition: all 0.2s ease;
        }

        .task-info-badge:hover {
            background: #faf5ff;
            border-color: #d8b4fe;
            transform: translateX(4px);
        }

        .task-info-badge svg {
            width: 1rem;
            height: 1rem;
        }

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
        <div class="answer-key-header">
            <div class="step-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Answer Key - Step 7 of 10</span>
            </div>
            
            <h1 class="header-title">
                Answer Key: Financial Statements
            </h1>
            
            <p class="header-description">
                Create the correct answer key for the Financial Statements following McGraw Hill format. This will be used to automatically grade student submissions.
            </p>
            
            <div class="task-info-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Task: {{ $task->title }}</span>
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
        
        // Parse saved data if it exists
        const parsedSaved = savedData ? (typeof savedData === 'string' ? JSON.parse(savedData) : savedData) : null;
        
        // Initialize with horizontal layout matching the image exactly
        const initialData = parsedSaved || [
            ["Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null],
            ["Income Statement", null, null, null, "Statement of Changes in Equity", null, null, null, "Balance Sheet", null, null, null],
            ["For the month ended February 29, 2024", null, null, null, "For the month ended February 29, 2024", null, null, null, "As of February 29, 2024", null, null, null],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["Revenues:", "", "", "", "Durano, Capital, beginning", "", "", "", "Assets", "", "", ""],
            ["Service Revenue", "", "", "", "Add: Investment", "", "", "", "Current assets", "", "", ""],
            ["", "", "", "", "          Net Income", "", "", "", "Cash", "", "", ""],
            ["Less: Expenses", "", "", "", "Total", "", "", "", "Accounts receivable", "", "", ""],
            ["Rent expense", "", "", "", "Less: Durano, Withdrawals", "", "", "", "Supplies", "", "", ""],
            ["Utilities expense", "", "", "", "Durano, Capital, ending", "", "", "", "Total current assets", "", "", ""],
            ["Salaries expense", "", "", "", "", "", "", "", "", "", "", ""],
            ["Supplies expense", "", "", "", "", "", "", "", "Non-current assets", "", "", ""],
            ["Depreciation expense", "", "", "", "", "", "", "", "Furniture and fixture", "", "", ""],
            ["Net Income", "", "", "", "", "", "", "", "Accumulated depreciation-furniture and fixture", "", "", ""],
            ["", "", "", "", "", "", "", "", "Equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accumulated depreciation-equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Land", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Non-current assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accounts payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Notes payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Utilities payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "Durano, Capital", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Liabilities and Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null]
        ];

        // Initialize HyperFormula
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any',
        });

        // Responsive dimensions
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const isMobile = viewportWidth < 640;
        const isTablet = viewportWidth >= 640 && viewportWidth < 1024;

        let tableHeight;
        if (isMobile) {
            tableHeight = Math.min(Math.max(viewportHeight * 0.5, 350), 500);
        } else if (isTablet) {
            tableHeight = Math.min(Math.max(viewportHeight * 0.6, 450), 600);
        } else {
            tableHeight = Math.min(Math.max(viewportHeight * 0.65, 550), 700);
        }

        // Column widths for 12 columns
        let colWidths;
        if (isMobile) {
            colWidths = Array(12).fill(100);
        } else if (isTablet) {
            colWidths = [200, 100, 100, 20, 200, 100, 100, 20, 200, 100, 100, 120];
        } else {
            colWidths = [240, 110, 110, 30, 240, 110, 110, 30, 240, 110, 110, 130];
        }
            
        hot = new Handsontable(container, {
            data: initialData,
            rowHeaders: true,
            colHeaders: ['', '', '', '', '', '', '', '', '', '', '', ''],
            columns: Array(12).fill(null).map((_, colIndex) => ({
                type: 'text',
                renderer: function(instance, td, row, col, prop, value, cellProperties) {
                    Handsontable.renderers.TextRenderer.apply(this, arguments);
                    
                    // Formula cells
                    if (value && typeof value === 'string' && value.startsWith('=')) {
                        td.classList.add('formula-cell');
                    }
                    
                    // Company name and statement titles (bold, centered)
                    if (row <= 2 && value && value.trim() !== '') {
                        td.style.fontWeight = 'bold';
                        td.style.textAlign = 'center';
                        td.style.fontSize = row === 0 ? '14px' : '13px';
                    }
                    
                    // Section headers - Income Statement
                    if (col <= 3 && value && (value === 'Revenues:' || value === 'Less: Expenses')) {
                        td.style.fontWeight = 'bold';
                    }
                    
                    // Section headers - Balance Sheet
                    if (col >= 8 && value && (
                        value === 'Assets' ||
                        value === 'Liabilities' ||
                        value === "Owner's Equity" ||
                        value === 'Current assets' ||
                        value === 'Non-current assets'
                    )) {
                        td.style.fontWeight = 'bold';
                    }
                    
                    // Total rows styling - apply to all columns in the row
                    const firstColValue = instance.getDataAtCell(row, 0);
                    const middleColValue = instance.getDataAtCell(row, 4);
                    const lastColValue = instance.getDataAtCell(row, 8);
                    
                    // Income Statement totals (columns 0-3)
                    if (col <= 3 && (firstColValue === 'Net Income' || value === 'Net Income')) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Statement of Changes totals (columns 4-7)
                    if (col >= 4 && col <= 7 && (
                        middleColValue === 'Total' || 
                        middleColValue === 'Durano, Capital, ending' ||
                        value === 'Total' ||
                        value === 'Durano, Capital, ending'
                    )) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Balance Sheet totals (columns 8-11)
                    if (col >= 8 && (
                        lastColValue === 'Total current assets' ||
                        lastColValue === 'Total Non-current assets' ||
                        lastColValue === 'Total Assets' ||
                        lastColValue === 'Total liabilities' ||
                        lastColValue === "Total Liabilities and Owner's Equity" ||
                        value === 'Total current assets' ||
                        value === 'Total Non-current assets' ||
                        value === 'Total Assets' ||
                        value === 'Total liabilities' ||
                        value === "Total Liabilities and Owner's Equity"
                    )) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Double underline for final totals
                    if (col >= 4 && col <= 7 && (middleColValue === 'Durano, Capital, ending' || value === '₱1,147,500')) {
                        td.style.borderBottom = '3px double #000';
                    }
                    
                    if (col >= 8 && (
                        lastColValue === 'Total Assets' ||
                        lastColValue === "Total Liabilities and Owner's Equity"
                    ) && (value === '₱1,253,000' || value === 'Total Assets' || value === "Total Liabilities and Owner's Equity")) {
                        td.style.borderBottom = '3px double #000';
                    }
                    
                    // Separator columns
                    if (col === 3 || col === 7) {
                        td.style.backgroundColor = '#f8f9fa';
                        td.style.borderRight = '2px solid #dee2e6';
                        td.style.width = '30px';
                    }
                    
                    // Right align numbers in appropriate columns
                    if ((col === 1 || col === 2 || col === 5 || col === 6 || col === 9 || col === 10 || col === 11) && 
                        value && (value.includes('₱') || value.includes(',') || !isNaN(value.replace(/[₱,()]/g, '')))) {
                        td.style.textAlign = 'right';
                    }
                }
            })),
            width: '100%',
            height: tableHeight,
            colWidths: colWidths,
            licenseKey: 'non-commercial-and-evaluation',
            formulas: { engine: hyperformulaInstance },
            beforeChange: function(changes, source) {
                if (changes) {
                    changes.forEach(function(change) {
                        if (change[3] && typeof change[3] === 'string' && change[3].startsWith('=')) {
                            change[3] = change[3].trim();
                        }
                    });
                }
            },
            contextMenu: true,
            undo: true,
            manualColumnResize: true,
            manualRowResize: true,
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 35,
            minCols: 12,
            minSpareRows: 1,
            stretchH: 'none',
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            comments: true,
            customBorders: true,
            className: 'htLeft htMiddle',
            mergeCells: [
                // Income Statement header merges
                { row: 0, col: 0, rowspan: 1, colspan: 4 },
                { row: 1, col: 0, rowspan: 1, colspan: 4 },
                { row: 2, col: 0, rowspan: 1, colspan: 4 },
                // Statement of Changes in Equity header merges
                { row: 0, col: 4, rowspan: 1, colspan: 4 },
                { row: 1, col: 4, rowspan: 1, colspan: 4 },
                { row: 2, col: 4, rowspan: 1, colspan: 4 },
                // Balance Sheet header merges
                { row: 0, col: 8, rowspan: 1, colspan: 4 },
                { row: 1, col: 8, rowspan: 1, colspan: 4 },
                { row: 2, col: 8, rowspan: 1, colspan: 4 }
            ]
        });

        // Window resize handler
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const newViewportWidth = window.innerWidth;
                const newViewportHeight = window.innerHeight;
                const newIsMobile = newViewportWidth < 640;
                const newIsTablet = newViewportWidth >= 640 && newViewportWidth < 1024;
                
                let newHeight;
                if (newIsMobile) {
                    newHeight = Math.min(Math.max(newViewportHeight * 0.5, 350), 500);
                } else if (newIsTablet) {
                    newHeight = Math.min(Math.max(newViewportHeight * 0.6, 450), 600);
                } else {
                    newHeight = Math.min(Math.max(newViewportHeight * 0.65, 550), 700);
                }
                
                let newColWidths;
                if (newIsMobile) {
                    newColWidths = Array(12).fill(100);
                } else if (newIsTablet) {
                    newColWidths = [200, 100, 100, 20, 200, 100, 100, 20, 200, 100, 100, 120];
                } else {
                    newColWidths = [240, 110, 110, 30, 240, 110, 110, 30, 240, 110, 110, 130];
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
</x-app-layout>