<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

    <div class="py-6">
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

        <div class="mb-6">
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                Step 10 of 10
            </span>
            <h1 class="text-3xl font-bold mt-2">Post-Closing Trial Balance</h1>
            <p class="text-gray-600 mt-2">
                Prepare the post-closing trial balance to verify equality of debits and credits in permanent accounts after closing entries.
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

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div id="spreadsheet" class="overflow-x-auto"></div>

            <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 10]) }}">
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

    <script>
        let hot;
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('spreadsheet');
            
            // Student's saved answers
            const savedData = @json($submission->submission_data ?? null);
            
            // Instructor's correct data
            const correctData = @json($answerSheet->correct_data ?? null);
            const submissionStatus = @json($submission->status ?? null);
            
            // McGraw Hill Post-Closing Trial Balance Format
            const initialData = savedData ? JSON.parse(savedData) : [
                ['POST-CLOSING TRIAL BALANCE', '', '', ''],
                ['December 31, 2024', '', '', ''],
                ['', '', '', ''],
                ['Account Title', '', 'Credit', 'Debit'],
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
                ['', '', '', ''],
                ['Totals', '', '', '']
            ];

            hot = new Handsontable(container, {
                data: initialData,
                rowHeaders: true,
                colHeaders: ['A', 'B', 'C', 'D'],
                columns: [
                    { type: 'text' },
                    { type: 'text' },
                    { type: 'numeric', numericFormat: { pattern: '0,0.00' } },
                    { type: 'numeric', numericFormat: { pattern: '0,0.00' } }
                ],
                cells: function(row, col) {
                    const cellProperties = {};
                    
                    // Apply correct/incorrect coloring if submission has been graded
                    if (submissionStatus && correctData && savedData) {
                        const parsedCorrect = typeof correctData === 'string' ? JSON.parse(correctData) : correctData;
                        const parsedStudent = typeof savedData === 'string' ? JSON.parse(savedData) : savedData;
                        
                        const studentValue = parsedStudent[row]?.[col];
                        const correctValue = parsedCorrect[row]?.[col];
                        
                        // Skip read-only cells (headers, titles, etc.)
                        const isReadOnlyCell = (row === 0 && col === 0) || 
                                               (row === 1 && col === 0) || 
                                               (row === 3) || 
                                               (row === 19 && col === 0) || 
                                               (col === 1);
                        
                        // Only compare non-empty, non-readonly cells that the STUDENT filled in
                        if (!isReadOnlyCell && studentValue !== null && studentValue !== undefined && studentValue !== '') {
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
                    
                    // Title row (row 0)
                    if (row === 0 && col === 0) {
                        cellProperties.className = (cellProperties.className || '') + ' htCenter htMiddle font-bold';
                        cellProperties.readOnly = true;
                    }
                    
                    // Date row (row 1)
                    if (row === 1 && col === 0) {
                        cellProperties.className = (cellProperties.className || '') + ' htCenter htMiddle';
                        cellProperties.readOnly = true;
                    }
                    
                    // Header row (row 3)
                    if (row === 3) {
                        cellProperties.className = (cellProperties.className || '') + ' htCenter htMiddle font-bold bg-gray-100';
                        cellProperties.readOnly = true;
                    }
                    
                    // Totals row (last row)
                    if (row === 19 && col === 0) {
                        cellProperties.className = (cellProperties.className || '') + ' htLeft htMiddle font-bold';
                        cellProperties.readOnly = true;
                    }
                    
                    // Make columns B empty and read-only for spacing
                    if (col === 1) {
                        cellProperties.readOnly = true;
                    }
                    
                    return cellProperties;
                },
                stretchH: 'all',
                height: 600,
                minSpareRows: 0,
                licenseKey: 'non-commercial-and-evaluation',
                contextMenu: ['undo', 'redo'],
                mergeCells: [
                    { row: 0, col: 0, rowspan: 1, colspan: 4 },
                    { row: 1, col: 0, rowspan: 1, colspan: 4 }
                ]
            });

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
        .handsontable .font-bold { font-weight: bold; }
        .handsontable .bg-gray-100 { background-color: #f3f4f6 !important; }
        .handsontable td { 
            border-color: #d1d5db;
            background-color: #ffffff; /* Default white background */
        }
        .handsontable .area { background-color: rgba(59,130,246,0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }

        /* Correct/Incorrect answer styling - consistent with Step 6 */
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
    </style>
</x-app-layout>