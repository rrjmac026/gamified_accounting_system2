<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

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
            <div class="relative">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                    <span>Step 3 of 10</span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                        Posting to the Ledger
                    </h1>
                    <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                        Transfer journalized entries into their respective ledger accounts to update balances.
                    </p>
                    <div class="mt-2 text-sm text-gray-600">
                        Attempts remaining: {{ 2 - ($submission->attempts ?? 0) }}/2
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Instructions Section -->
            <div class="p-4 sm:p-6 bg-purple-50 border-b border-purple-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-purple-900 mb-1">T-Account Ledger Instructions</h3>
                        <p class="text-xs sm:text-sm text-purple-800">
                            {!! $performanceTask->description ?? 'Post transactions to T-accounts with debit entries on the left and credit entries on the right. Calculate the balance for each account.' !!}
                        </p>
                    </div>
                </div>
            </div>

            <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 3]) }}">
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
                            class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors text-sm sm:text-base"
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
            
            // Account names list
            const accounts = [
                'Cash', 'Accounts Receivable', 'Supplies', 'Furniture & Fixture', 
                'Accumulated Depreciation - F&F', 'Land', 'Equipment', 
                'Accumulated Depreciation - Equipment', 'Accounts Payable', 
                'Notes Payable', 'Utilities Payable', 'Capital', 'Withdrawals',
                'Service Revenue', 'Rent Expense', 'Utilities Expense', 
                'Salaries Expense', 'Supplies Expense', 'Depreciation Expense', 
                'Income Summary'
            ];
            
            // Generate columns: Date, Blank, Debit, Credit for each account (4 cols per account)
            const numCols = accounts.length * 4;
            const initialData = savedData ? JSON.parse(savedData) : Array.from({ length: 15 }, () => Array(numCols).fill(''));
            
            // Create nested headers with blank column after Date
            const nestedHeaders = [
                accounts.map(name => ({ label: name, colspan: 4 })),
                Array(accounts.length).fill(['Date', '', 'Debit (₱)', 'Credit (₱)']).flat()
            ];
            
            // Create columns config with custom renderer for T-account style
            const columns = [];
            for (let i = 0; i < accounts.length; i++) {
                columns.push(
                    { type: 'text', width: 100 },      // Date
                    { type: 'text', width: 50 },       // Blank column
                    { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 120 }, // Debit
                    { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: 120 }  // Credit
                );
            }

            // Instructor's correct data
            const correctData = @json($answerSheet->correct_data ?? null);
            const submissionStatus = @json($submission->status ?? null);
            
            hot = new Handsontable(container, {
                data: initialData,
                rowHeaders: true,
                nestedHeaders: nestedHeaders,
                columns: columns,
                height: 'auto',
                licenseKey: 'non-commercial-and-evaluation',
                contextMenu: true,
                manualColumnResize: true,
                manualRowResize: true,
                minSpareRows: 1,
                cells: function(row, col) {
                    const cellProperties = {};
                    const colIndex = col % 4;

                    
                    // Apply T-account styling (append to existing className)
                    if (colIndex === 0) {
                        // Date column
                        cellProperties.className = (cellProperties.className || '') + ' t-account-date';
                    } else if (colIndex === 1) {
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
                    if (submissionStatus && correctData && savedData && colIndex !== 1) { // Skip blank column
                        const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                        const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                        
                        const studentValue = parsedStudent[row]?.[col];
                        const correctValue = parsedCorrect[row]?.[col];
                        
                        // ONLY color cells where the STUDENT entered something
                        if (studentValue !== null && studentValue !== undefined && studentValue !== '') {
                            // Normalize values for comparison
                            const normalizeValue = (val) => {
                                if (val === null || val === undefined || val === '') return '';
                                if (typeof val === 'string') return val.trim().toLowerCase();
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
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                document.getElementById('submission_data').value = JSON.stringify(hot.getData());
                this.submit();
            });
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
    </style>
</x-app-layout>