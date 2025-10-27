<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

    <div class="py-4 sm:py-6 lg:py-8">
        {{-- ✅ Flash Messages --}}
        @if (session('error'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    <button onclick="this.closest('div.mb-6').remove()"
                            class="text-red-400 hover:text-red-600 transition-colors">✕</button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 animate-slideDown">
                <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.closest('div.mb-6').remove()"
                            class="text-green-400 hover:text-green-600 transition-colors">✕</button>
                </div>
            </div>
        @endif

        <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

        {{-- ✅ Header --}}
        <div class="mb-6 sm:mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 
                        7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 
                        0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Step 4 of 10</span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900">Trial Balance</h1>
            <p class="mt-2 text-gray-600 text-sm sm:text-base">
                List all accounts and their debit or credit balances to ensure totals are equal.
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

        {{-- ✅ Main Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <p class="text-xs sm:text-sm text-gray-600">
                    {!! $performanceTask->description ?? 'Review your ledger balances before continuing.' !!}
                </p>
            </div>

            <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 4]) }}">
                @csrf
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="submission_data" id="submission_data" required>
                    </div>
                </div>

                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                        <button type="button" onclick="window.history.back()"
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 text-sm sm:text-base">
                            ← Back
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                            💾 Save and Continue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<script>
    let hot;
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('spreadsheet');
        const savedData = @json($submission->submission_data ?? null);
        const correctData = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);

        // ✅ Load saved or default data with header rows
        let initialData = savedData
            ? JSON.parse(savedData)
            : [
                ['Durano Enterprise', '', ''],  // Row 0: Company name
                ['Trial Balance', '', ''],      // Row 1: Document title
                ['Date: ____________', '', ''], // Row 2: Date field
                ['Account Title', 'Debit (₱)', 'Credit (₱)'], // Row 3: Column headers
                ['', '', ''],                   // Row 4: First data row
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['', '', ''],
                ['Total', '', '']               // Last row: Totals
            ];

        hot = new Handsontable(container, {
            data: initialData,
            columns: [
                { type: 'text' },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
            ],
            rowHeaders: true,
            licenseKey: 'non-commercial-and-evaluation',
            height: 450,
            stretchH: 'all',
            contextMenu: true,
            manualColumnResize: true,
            manualRowResize: true,
            minSpareRows: 0,
            className: 'htCenter htMiddle',
            
            // ✅ Add cell coloring logic
            cells: function(row, col) {
                const cellProperties = {};
                const data = this.instance.getData();
                const lastRow = data.length - 1;
                
                // Row 0: Company name (only in Debit column)
                if (row === 0) {
                    cellProperties.className = 'header-company';
                    if (col === 0 || col === 2) {
                        cellProperties.readOnly = true;
                        cellProperties.renderer = function(instance, td) {
                            td.innerHTML = '';
                            td.style.background = 'white';
                            td.style.border = 'none';
                        };
                    } else if (col === 1) {
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Durano Enterprise') + '</strong>';
                            td.style.textAlign = 'center';
                        };
                    }
                }
                
                // Row 1: Document title (only in Debit column)
                if (row === 1) {
                    cellProperties.className = 'header-title';
                    if (col === 0 || col === 2) {
                        cellProperties.readOnly = true;
                        cellProperties.renderer = function(instance, td) {
                            td.innerHTML = '';
                            td.style.background = 'white';
                            td.style.border = 'none';
                        };
                    } else if (col === 1) {
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Trial Balance') + '</strong>';
                            td.style.textAlign = 'center';
                        };
                    }
                }
                
                // Row 2: Date field (only in Debit column)
                if (row === 2) {
                    cellProperties.className = 'header-date';
                    if (col === 0 || col === 2) {
                        cellProperties.readOnly = true;
                        cellProperties.renderer = function(instance, td) {
                            td.innerHTML = '';
                            td.style.background = 'white';
                            td.style.border = 'none';
                        };
                    } else if (col === 1) {
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>' + (value || 'Date: ____________') + '</strong>';
                            td.style.textAlign = 'center';
                        };
                    }
                }
                
                // Row 3: Column headers (bold, centered, read-only)
                if (row === 3) {
                    cellProperties.readOnly = true;
                    cellProperties.className = 'header-columns';
                    cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                        Handsontable.renderers.TextRenderer.apply(this, arguments);
                        td.innerHTML = '<strong>' + value + '</strong>';
                        td.style.textAlign = 'center';
                        td.style.backgroundColor = '#f3f4f6';
                    };
                }
                
                // Make last row "Total" static and bold
                if (row === lastRow) {
                    cellProperties.className = 'total-row';
                    if (col === 0) {
                        cellProperties.readOnly = true;
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>Total</strong>';
                        };
                    } else {
                        cellProperties.className = 'total-row total-cell-bold';
                    }
                }
                
                // Only apply correct/incorrect coloring if submission has been graded
                // Skip header rows (0-3) and total row
                if (submissionStatus && correctData && savedData && row > 3 && row !== lastRow) {
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
            }
        });

        // ✅ Save data before form submit
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
        background-color: #ffffff;
    }
    .handsontable .area { background-color: rgba(59, 130, 246, 0.1); }
    #spreadsheet { isolation: isolate; }
    
    /* Total row styling */
    .handsontable td.total-row {
        background-color: #f3f4f6 !important;
        font-weight: 700;
    }

    .handsontable td.total-cell-bold {
        border-top: 3px solid #374151 !important;
        border-bottom: 3px double #374151 !important;
    }
    
    /* Correct/Incorrect answer styling - consistent with Steps 1, 2, 3 */
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
        background-color: #bbf7d0 !important;
    }

    .handsontable td.cell-wrong.area,
    .handsontable td.cell-wrong.current {
        background-color: #fecaca !important;
    }
    
    @media (max-width: 640px) {
        .handsontable { font-size: 12px; }
    }
</style>
</x-app-layout>