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
                    <div class="h-2 bg-gradient-to-r from-purple-500 via-pink-600 to-red-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 rounded-full text-sm font-semibold border border-purple-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 3 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 h-full rounded-full transition-all duration-500" style="width: 30%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">30%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Posting to the Ledger
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Transfer journalized entries into their respective ledger accounts to update balances.
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
                <div class="p-4 sm:p-6 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-purple-900 mb-1">T-Account Ledger Instructions</h3>
                            <div class="text-sm text-purple-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Post transactions to T-accounts with debit entries on the left and credit entries on the right. Calculate the balance for each account.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 3]) }}">
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
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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

<script>
    let hot;
    
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('spreadsheet');

        // Student's saved answers
        const savedData = @json($submission->submission_data ?? null);
        
        // Account names list
        const accounts = [
            'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixture',
            'Land', 'Equipment','Accumulated Depreciation - F&F',
            'Accumulated Depreciation - Equipment',
            'Accounts Payable', 'Notes Payable', 'Utilities Payable', 'Capital', 
            'Withdrawals', 'Service Revenue', 'Rent Expense', 'Utilities Expense',
            'Salaries Expense', 'Supplies Expense', 'Depreciation Expense',
            'Income Summary'
        ];
        
        // Generate columns: Date, Blank, Debit, Credit, Blank, Date for each account (6 cols per account)
        const numCols = accounts.length * 6;
        const initialData = savedData ? JSON.parse(savedData) : Array.from({ length: 15 }, () => Array(numCols).fill(''));
        
        // Create nested headers with blank column after Date
        const nestedHeaders = [
            accounts.map(name => ({ label: name, colspan: 6 })),
            Array(accounts.length).fill(['Date', '', 'Debit (₱)', 'Credit (₱)', '', 'Date']).flat()
        ];
        
        // Custom renderer to add peso sign and handle large numbers
        function pesoRenderer(instance, td, row, col, prop, value, cellProperties) {
            Handsontable.renderers.NumericRenderer.apply(this, arguments);
            
            if (value !== null && value !== undefined && value !== '') {
                const numValue = typeof value === 'number' ? value : parseFloat(String(value).replace(/[,₱\s]/g, ''));
                if (!isNaN(numValue)) {
                    td.innerHTML = '₱' + numValue.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
            
            return td;
        }
        
        // Create columns config with custom renderer for T-account style
        const columns = [];
        for (let i = 0; i < accounts.length; i++) {
            columns.push(
                { type: 'text', width: 100 },      // Date
                { type: 'text', width: 50 },       // Blank column
                { 
                    type: 'numeric',
                    renderer: pesoRenderer,
                    width: 120
                }, // Debit
                { 
                    type: 'numeric',
                    renderer: pesoRenderer,
                    width: 120
                }, // Credit
                { type: 'text', width: 50 },       // Blank column
                { type: 'text', width: 100 }       // Second Date
            );
        }

        // Instructor's correct data
        const correctData = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);
        const maxAttempts = @json($performanceTask->max_attempts);
        const currentAttempts = @json($submission->attempts ?? 0);
        const isReadOnly = currentAttempts >= maxAttempts;
        
        // Initialize HyperFormula with whitespace support
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any', // Allows spaces in formulas
        });
        
        hot = new Handsontable(container, {
            data: initialData,
            rowHeaders: true,
            nestedHeaders: nestedHeaders,
            columns: columns,
            height: 'auto',
            licenseKey: 'non-commercial-and-evaluation',
            readOnly: isReadOnly,
            
            // Formula support with whitespace handling
            formulas: { engine: hyperformulaInstance },
            
            // Handle formula input with whitespace and numeric parsing
            beforeChange: function(changes, source) {
                if (!isReadOnly && changes) {
                    changes.forEach(function(change) {
                        const [row, col, oldValue, newValue] = change;
                        
                        // Handle formulas
                        if (newValue && typeof newValue === 'string' && newValue.startsWith('=')) {
                            change[3] = newValue.trim();
                        }
                        // Handle numeric values - remove commas and peso signs
                        else if (newValue && typeof newValue === 'string') {
                            const colIndex = col % 6;
                            // Only process numeric columns (debit and credit)
                            if (colIndex === 2 || colIndex === 3) {
                                const cleanValue = newValue.replace(/[,₱\s]/g, '');
                                if (!isNaN(cleanValue) && cleanValue !== '') {
                                    change[3] = parseFloat(cleanValue);
                                }
                            }
                        }
                    });
                }
            },
            
            // Full feature set
            contextMenu: !isReadOnly,
            undo: !isReadOnly,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: !isReadOnly,
            manualRowMove: !isReadOnly,
            fillHandle: !isReadOnly,
            copyPaste: !isReadOnly,
            minSpareRows: 1,
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: true,
            comments: true,
            customBorders: true,
            
            cells: function(row, col) {
                const cellProperties = {};
                const colIndex = col % 6;
                const cellData = this.instance.getDataAtCell(row, col);
                
                // Add visual indicator for formula cells
                if (cellData && typeof cellData === 'string' && cellData.startsWith('=')) {
                    cellProperties.className = 'formula-cell';
                }

                // Apply T-account styling (append to existing className)
                if (colIndex === 0 || colIndex === 5) {
                    // Date column
                    cellProperties.className = (cellProperties.className || '') + ' t-account-date';
                } else if (colIndex === 1 || colIndex === 4) {
                    // Blank column
                    cellProperties.className = (cellProperties.className || '') + ' t-account-blank';
                    cellProperties.readOnly = false; // Make blank column read-only
                } else if (colIndex === 2) {
                    // Debit column (left side)
                    cellProperties.className = (cellProperties.className || '') + ' t-account-debit';
                } else if (colIndex === 3) {
                    // Credit column (right side)
                    cellProperties.className = (cellProperties.className || '') + ' t-account-credit';
                }
                
                // Only apply correct/incorrect coloring if submission has been graded
                if (submissionStatus && correctData && savedData && colIndex !== 1 && colIndex !== 4) { // Skip blank columns
                    const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                    const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                    
                    const studentValue = parsedStudent[row]?.[col];
                    const correctValue = parsedCorrect[row]?.[col];
                    
                    // ONLY color cells where the STUDENT entered something
                    if (studentValue !== null && studentValue !== undefined && studentValue !== '') {
                        // Normalize values for comparison
                        const normalizeValue = (val) => {
                            if (val === null || val === undefined || val === '') return '';
                            if (typeof val === 'string') {
                                // Remove commas, peso signs, and whitespace for comparison
                                const cleaned = val.trim().replace(/[,₱\s]/g, '').toLowerCase();
                                // Try to parse as number
                                const num = parseFloat(cleaned);
                                if (!isNaN(num)) {
                                    return num.toFixed(2);
                                }
                                return cleaned;
                            }
                            if (typeof val === 'number') return val.toFixed(2);
                            return String(val);
                        };
                        
                        const normalizedStudent = normalizeValue(studentValue);
                        const normalizedCorrect = normalizeValue(correctValue);
                        
                        // Compare student's answer with correct answer
                        if (normalizedStudent === normalizedCorrect) {
                            cellProperties.className = (cellProperties.className || '') + ' cell-correct';
                        } else {
                            cellProperties.className = (cellProperties.className || '') + ' cell-wrong';
                        }
                    }
                }
                
                return cellProperties;
            },
        });

        // Save submission data
        const form = document.getElementById('saveForm');
        if (form && !isReadOnly) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                document.getElementById('submission_data').value = JSON.stringify(hot.getData());
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
        
        /* T-Account Styling */
        .handsontable td.t-account-date {
            background-color: #f9fafb;
            font-size: 0.85em;
        }

        .handsontable td.t-account-row-bold {
            font-weight: 700;
            border-bottom: 2px solid #6b7280;
        }
        
        .handsontable td.t-account-debit {
            border-right: 2px solid #6b7280 !important;
            background-color: #fef3c7;
        }
        
        .handsontable td.t-account-credit {
            background-color: #dbeafe;
        }
        
        /* Highlight the vertical line between debit and credit */
        .handsontable th {
            font-weight: 600;
        }
        
        /* Style the nested headers to look like T-accounts */
        .handsontable thead th {
            background-color: #f3f4f6;
        }
        
        .handsontable thead tr:first-child th {
            background-color: #e5e7eb;
            font-weight: 700;
            border-bottom: 2px solid #6b7280;
        }

        /* Correct/Incorrect answer styling - matches Step 1 & 2 */
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
        
        /* Maintain T-account border even when colored - override with thicker border */
        .handsontable td.t-account-debit.cell-correct {
            border-right: 2px solid #16a34a !important;
        }
        
        .handsontable td.t-account-debit.cell-wrong {
            border-right: 2px solid #dc2626 !important;
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
            .handsontable { font-size: 11px; }
            .handsontable th, .handsontable td { padding: 3px; }
        }
        
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 12px; }
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