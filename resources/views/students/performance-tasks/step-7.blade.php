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

        <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

        <!-- Enhanced Header Section -->
        <div class="mb-6 sm:mb-8">
            <div class="relative">
                <!-- Step Indicator Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Step 7 of 10</span>
                </div>
                
                <!-- Title Section -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                            Financial Statements
                        </h1>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                            Prepare the financial statements following McGraw Hill format: Income Statement, Statement of Owner's Equity, and Balance Sheet.
                        </p>
                        <div class="mt-2 flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-2 text-blue-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                <span>Attempts remaining: <strong>{{ 2 - ($submission->attempts ?? 0) }}/2</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6 bg-blue-50 border-b border-blue-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-1">Instructions</h3>
                        <p class="text-xs sm:text-sm text-blue-800">
                            Complete all three financial statements in the McGraw Hill format below. Make sure to fill in all amounts accurately. Your work will be automatically graded against the answer key.
                        </p>
                    </div>
                </div>
            </div>

            <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 7]) }}">
                @csrf
                
                <!-- Spreadsheet Section -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="submission_data" id="submission_data" required>
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
                        <a href="{{ route('students.performance-tasks.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Tasks
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm sm:text-base disabled:bg-gray-400 disabled:cursor-not-allowed"
                                {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ ($submission->attempts ?? 0) >= 2 ? 'Maximum Attempts Reached' : 'Save and Continue' }}
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
            
            // Get saved submission data, answer key, and grading status
            const savedData = @json($submission->submission_data ?? null);
            const correctData = @json($answerSheet->correct_data ?? null);
            const submissionStatus = @json($submission->status ?? null);

            // Parse once for reuse
            const parsedSaved = savedData ? (typeof savedData === 'string' ? JSON.parse(savedData) : savedData) : null;
            const parsedCorrect = correctData ? (typeof correctData === 'string' ? JSON.parse(correctData) : correctData) : null;
            
            // Define the initial empty template (without any pre-filled values)
            const initialData = [
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

            // Define template cells that should NEVER be graded (headers, labels, etc.)
            const templateCells = new Set([
                // Income Statement headers and labels
                '0-0', '0-1', '0-2', '0-3',
                '1-0', '1-1', '1-2', '1-3', 
                '2-0', '2-1', '2-2', '2-3',
                '4-0', '8-0', '5-0', '6-0', '9-0', '10-0', '11-0', '12-0', '13-0', '14-0', '16-0',
                
                // Statement of Owner's Equity headers and labels
                '19-0', '19-1', '19-2', '19-3',
                '20-0', '20-1', '20-2', '20-3',
                '21-0', '21-1', '21-2', '21-3',
                '24-0', '25-0', '26-0', '28-0', '29-0', '30-0',
                
                // Balance Sheet headers and labels
                '32-0', '32-1', '32-2', '32-3',
                '33-0', '33-1', '33-2', '33-3',
                '34-0', '34-1', '34-2', '34-3',
                '35-0', '36-0', '37-0', '38-0', '40-0', '41-0', '43-0', '44-0', '46-0', '47-0', '48-0', '49-0',
                '51-0', '52-0', '53-0', '54-0', '56-0', '57-0', '58-0'
            ]);

            // Define table boundaries for visual separation
            const tableBoundaries = {
                incomeStatement: {
                    startRow: 0,
                    endRow: 18,
                    color: '#e6f3ff', // Light blue
                    borderColor: '#3b82f6' // Blue border
                },
                ownersEquity: {
                    startRow: 19,
                    endRow: 31,
                    color: '#f0f9ff', // Very light blue
                    borderColor: '#0ea5e9' // Sky blue border
                },
                balanceSheet: {
                    startRow: 32,
                    endRow: 58,
                    color: '#eff6ff', // Very light indigo
                    borderColor: '#6366f1' // Indigo border
                }
            };

            // Function to check if a cell is a template cell (should not be graded)
            function isTemplateCell(row, col) {
                return templateCells.has(`${row}-${col}`);
            }

            // Function to check if student actually modified this cell
            function studentModifiedCell(row, col) {
                if (!parsedSaved || !parsedSaved[row] || parsedSaved[row][col] === undefined) {
                    return false;
                }
                
                const studentValue = parsedSaved[row][col];
                const initialValue = initialData[row] ? initialData[row][col] : '';
                
                // Student modified if value exists and is different from initial empty template
                return studentValue !== null && 
                       studentValue !== undefined && 
                       studentValue !== '' && 
                       studentValue !== initialValue;
            }

            // Function to determine which table a row belongs to
            function getTableForRow(row) {
                if (row >= tableBoundaries.incomeStatement.startRow && row <= tableBoundaries.incomeStatement.endRow) {
                    return 'incomeStatement';
                } else if (row >= tableBoundaries.ownersEquity.startRow && row <= tableBoundaries.ownersEquity.endRow) {
                    return 'ownersEquity';
                } else if (row >= tableBoundaries.balanceSheet.startRow && row <= tableBoundaries.balanceSheet.endRow) {
                    return 'balanceSheet';
                }
                return null;
            }

            // Initialize HyperFormula for Excel-like formulas
            const hyperformulaInstance = HyperFormula.buildEmpty({
                licenseKey: 'internal-use-in-handsontable',
            });

            // Determine responsive dimensions
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const isMobile = viewportWidth < 640;
            const isTablet = viewportWidth >= 640 && viewportWidth < 1024;
            const isDesktop = viewportWidth >= 1024;

            // Calculate optimal table height
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
                    Math.floor(availableWidth * 0.30),
                    Math.floor(availableWidth * 0.35),
                    Math.floor(availableWidth * 0.175),
                    Math.floor(availableWidth * 0.175)
                ];
            } else if (isTablet) {
                colWidths = [180, 200, 120, 120];
            } else {
                colWidths = [220, 260, 140, 140];
            }
                
            hot = new Handsontable(container, {
                data: parsedSaved || initialData, // Use saved data if available, otherwise empty template
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
                            
                            // Apply table-specific background colors
                            const table = getTableForRow(row);
                            if (table) {
                                td.style.backgroundColor = tableBoundaries[table].color;
                            }
                            
                            // Apply grading colors
                            if (submissionStatus && parsedCorrect && studentModifiedCell(row, col) && !isTemplateCell(row, col)) {
                                const studentValue = parsedSaved[row][col];
                                const correctValue = parsedCorrect[row] ? parsedCorrect[row][col] : '';
                                const normalizeValue = (val) => {
                                    if (val === null || val === undefined || val === '') return '';
                                    if (typeof val === 'string') return val.trim().toLowerCase();
                                    return val.toString();
                                };
                                const isCorrect = normalizeValue(studentValue) === normalizeValue(correctValue);
                                if (isCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }

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
                                if (!submissionStatus || !td.style.backgroundColor || td.style.backgroundColor === '' || td.style.backgroundColor === 'transparent') {
                                    // Keep the table-specific background color
                                    const table = getTableForRow(row);
                                    if (table) {
                                        td.style.backgroundColor = tableBoundaries[table].color;
                                    }
                                }
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
                            
                            // Apply table-specific background colors
                            const table = getTableForRow(row);
                            if (table) {
                                td.style.backgroundColor = tableBoundaries[table].color;
                            }
                            
                            // Apply grading colors
                            if (submissionStatus && parsedCorrect && studentModifiedCell(row, col) && !isTemplateCell(row, col)) {
                                const studentValue = parsedSaved[row][col];
                                const correctValue = parsedCorrect[row] ? parsedCorrect[row][col] : '';
                                const normalizeValue = (val) => {
                                    if (val === null || val === undefined || val === '') return '';
                                    if (typeof val === 'string') return val.trim().toLowerCase();
                                    return val.toString();
                                };
                                const isCorrect = normalizeValue(studentValue) === normalizeValue(correctValue);
                                if (isCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }
                        }
                    },
                    { 
                        type: 'numeric', 
                        numericFormat: { pattern: '₱0,0.00' },
                        renderer: function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.NumericRenderer.apply(this, arguments);
                            
                            // Apply table-specific background colors
                            const table = getTableForRow(row);
                            if (table) {
                                td.style.backgroundColor = tableBoundaries[table].color;
                            }
                            
                            // Apply grading colors
                            if (submissionStatus && parsedCorrect && studentModifiedCell(row, col) && !isTemplateCell(row, col)) {
                                const studentValue = parsedSaved[row][col];
                                const correctValue = parsedCorrect[row] ? parsedCorrect[row][col] : '';
                                const normalizeValue = (val) => {
                                    if (val === null || val === undefined || val === '') return '';
                                    if (typeof val === 'number') return val.toString();
                                    if (typeof val === 'string') return val.trim();
                                    return val.toString();
                                };
                                const studentVal = normalizeValue(studentValue);
                                const correctVal = normalizeValue(correctValue);
                                const isCorrect = parseFloat(studentVal) === parseFloat(correctVal);
                                if (isCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }

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
                            
                            // Apply table-specific background colors
                            const table = getTableForRow(row);
                            if (table) {
                                td.style.backgroundColor = tableBoundaries[table].color;
                            }
                            
                            // Apply grading colors
                            if (submissionStatus && parsedCorrect && studentModifiedCell(row, col) && !isTemplateCell(row, col)) {
                                const studentValue = parsedSaved[row][col];
                                const correctValue = parsedCorrect[row] ? parsedCorrect[row][col] : '';
                                const normalizeValue = (val) => {
                                    if (val === null || val === undefined || val === '') return '';
                                    if (typeof val === 'number') return val.toString();
                                    if (typeof val === 'string') return val.trim();
                                    return val.toString();
                                };
                                const studentVal = normalizeValue(studentValue);
                                const correctVal = normalizeValue(correctValue);
                                const isCorrect = parseFloat(studentVal) === parseFloat(correctVal);
                                if (isCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }

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
                minRows: 65,
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
                // Add custom borders for table separation
                customBorders: [
                    // Income Statement bottom border
                    {
                        range: {
                            from: { row: tableBoundaries.incomeStatement.endRow, col: 0 },
                            to: { row: tableBoundaries.incomeStatement.endRow, col: 3 }
                        },
                        bottom: {
                            width: 3,
                            color: tableBoundaries.incomeStatement.borderColor
                        }
                    },
                    // Statement of Owner's Equity bottom border
                    {
                        range: {
                            from: { row: tableBoundaries.ownersEquity.endRow, col: 0 },
                            to: { row: tableBoundaries.ownersEquity.endRow, col: 3 }
                        },
                        bottom: {
                            width: 3,
                            color: tableBoundaries.ownersEquity.borderColor
                        }
                    },
                    // Balance Sheet bottom border
                    {
                        range: {
                            from: { row: tableBoundaries.balanceSheet.endRow, col: 0 },
                            to: { row: tableBoundaries.balanceSheet.endRow, col: 3 }
                        },
                        bottom: {
                            width: 3,
                            color: tableBoundaries.balanceSheet.borderColor
                        }
                    }
                ]
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
            const saveForm = document.getElementById("saveForm");
            if (saveForm) {
                saveForm.addEventListener("submit", function (e) {
                    e.preventDefault();
                    const data = hot.getData();
                    document.getElementById("submission_data").value = JSON.stringify(data);
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
        .handsontable .area { background-color: rgba(59, 130, 246, 0.1); }
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

        /* Grading colors (copied from step 6) */
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

        /* Table separation styling */
        .handsontable .table-divider {
            border-bottom: 3px solid #3b82f6;
        }

        @media (max-width: 640px) {
            .handsontable { font-size: 11px; }
            .handsontable th, .handsontable td { padding: 3px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 12px; }
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