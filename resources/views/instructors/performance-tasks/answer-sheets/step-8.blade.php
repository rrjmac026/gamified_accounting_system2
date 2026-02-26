<x-app-layout>
    {{-- ═══════════════════════════ jSpreadsheet CDN ═══════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <style>
        /* ── Page header / badge styles ──────────────────────────────────────── */
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
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(167, 139, 250, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .answer-key-header::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -5%;
            width: 200px; height: 200px;
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
        .step-badge svg { width: 1rem; height: 1rem; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
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
        .task-info-badge:hover { background: #faf5ff; border-color: #d8b4fe; transform: translateX(4px); }
        .task-info-badge svg { width: 1rem; height: 1rem; }

        /* ── jSpreadsheet overrides ───────────────────────────────────────────── */
        body { overflow-x: hidden; }
        #spreadsheet { width: 100%; }
        #spreadsheet .jexcel_content { overflow: auto; }
        .jexcel td { border-color: #d1d5db !important; }

        /* ── Row 1 — main header: Date | … | Account Titles | Acct No. | Debit | Credit ── */
        .jexcel tbody tr:nth-child(1) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #e5e7eb !important;
            white-space: normal !important;
            word-break: break-word !important;
            border-bottom: 2px solid #6b7280 !important;
        }

        /* ── Row 2 — sub-labels: Month | Day | … ────────────────────────────── */
        .jexcel tbody tr:nth-child(2) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #f3f4f6 !important;
            border-bottom: 2px solid #6b7280 !important;
        }

        /* ── Scrollbar polish ────────────────────────────────────────────────── */
        #spreadsheet ::-webkit-scrollbar        { width: 6px; height: 6px; }
        #spreadsheet ::-webkit-scrollbar-track  { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 9999px; }

        @media (max-width: 640px) { .jexcel td, .jexcel th { font-size: 12px; padding: 4px; } }
        @media (min-width: 640px) and (max-width: 1024px) { .jexcel td, .jexcel th { font-size: 13px; } }
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
                <span>Answer Key - Step 8 of 10</span>
            </div>

            <h1 class="header-title">Answer Key: Balance Sheet</h1>

            <p class="header-description">
                Create the correct answer key for the Balance Sheet. This will be used to automatically grade student submissions.
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
                    <svg class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
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

            <form id="answerKeyForm"
                  action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 8]) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto"
                             style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">

                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Answer Sheets
                        </a>

                        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                            <button type="button" onclick="openImportModal()"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 transition-colors text-sm sm:text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import File
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors text-sm sm:text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Answer Key &amp; Continue
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════ jSpreadsheet initialisation ═══════════════════ --}}
    <script>
    (function () {

        const container  = document.getElementById('spreadsheet');
        const savedData  = @json($sheet->correct_data ?? null);

        // ── Constants ─────────────────────────────────────────────────────────
        const HEADER_ROWS   = 1;
        const COL_COUNT     = 6;
        const MIN_DATA_ROWS = 15;

        // ── Header rows — identical to HOT version ────────────────────────────
        const headerRow1 = ['Date', '', 'Account Titles and Explanation', 'Account Number', 'Debit (₱)', 'Credit (₱)'];
        const blankRow   = () => Array(COL_COUNT).fill('');

        // ── Restore saved data ────────────────────────────────────────────────
        let dataRows;
        if (savedData) {
            const parsed = JSON.parse(savedData);
            if (parsed.length <= MIN_DATA_ROWS) {
                // Old format — no headers stored
                dataRows = parsed;
            } else {
                // New format — strip the 2 header rows
                dataRows = parsed.slice(HEADER_ROWS);
            }
        } else {
            dataRows = Array(MIN_DATA_ROWS).fill(null).map(blankRow);
        }

        while (dataRows.length < MIN_DATA_ROWS) dataRows.push(blankRow());

        const fullData = [headerRow1, ...dataRows];

        // ── Merge cells — "Date" spans cols A-B in row 1 ─────────────────────
        const mergeCells = {
            'A1': [2, 1],
        };

        // ── Cell styles ───────────────────────────────────────────────────────
        const cellStyle = {};

        // Row 1: main headers — grey background, bold, centred
        ['A1','B1','C1','D1','E1','F1'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#e5e7eb;white-space:normal;word-break:break-word;border-bottom:2px solid #6b7280;';
        });

        // Row 2: sub-labels — light grey, bold, centred
        ['A2','B2','C2','D2','E2','F2'].forEach(ref => {
            cellStyle[ref] = 'font-weight:700;text-align:center;background:#f3f4f6;border-bottom:2px solid #6b7280;';
        });

        // ── Responsive dimensions ─────────────────────────────────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // ── Init jSpreadsheet ─────────────────────────────────────────────────
        const table = jspreadsheet(container, {
            data             : fullData,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 120 : 150,
            mergeCells       : mergeCells,
            style            : cellStyle,
            minDimensions    : [COL_COUNT, fullData.length],
            tableWidth       : '100%',
            tableOverflow    : true,
            tableHeight      : isMobile ? '350px' : (isTablet ? '450px' : '500px'),
            allowFormulas    : true,
            columnSorting    : false,
            columnDrag       : false,
            rowDrag          : false,
            allowInsertRow   : true,
            allowInsertColumn: false,
            allowDeleteRow   : true,
            allowDeleteColumn: false,
            columnResize     : true,
            rowResize        : true,
            copyCompatibility: true,
            minSpareRows     : 1,

            columns: [
            ],

            contextMenu: function (obj, x, y, e) {
                return [
                    { title: 'Insert row above', onclick: () => obj.insertRow(1, parseInt(y), true) },
                    { title: 'Insert row below', onclick: () => obj.insertRow(1, parseInt(y)) },
                    { title: 'Delete row',       onclick: () => obj.deleteRow(parseInt(y)) },
                    { type: 'line' },
                    { title: 'Copy',  onclick: () => obj.copy(true) },
                    { title: 'Paste', onclick: () => {
                        if (navigator.clipboard) {
                            navigator.clipboard.readText().then(t => obj.paste(x, y, t));
                        }
                    }},
                ];
            },

            onload  : function () { applyBorders(); },
            onchange: function () { applyBorders(); },
        });

        // ── Right-border on Credit column (col F / index 5) ──────────────────
        // Mirrors HOT afterRenderer: TD.style.borderRight = '3px solid #000000'
        function applyBorders() {
            const tbody = container.querySelector('.jexcel tbody');
            if (!tbody) return;
            tbody.querySelectorAll('tr').forEach((tr, rowIdx) => {
                if (rowIdx < HEADER_ROWS) return;
                const cells = tr.querySelectorAll('td');
                cells.forEach((td, tdIdx) => {
                    if (tdIdx === 0) return;          // skip row-number td
                    const colIdx = tdIdx - 1;         // real 0-based data column
                    td.style.borderRight = '';
                    if (colIdx === 5) {
                        td.style.borderRight = '3px solid #000000';
                    }
                });
            });
        }

        setTimeout(applyBorders, 100);

        // ── Expose for potential external use ─────────────────────────────────
        window.table = table;

        // ── Responsive resize ─────────────────────────────────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const el = container.querySelector('.jexcel_content');
                if (el) {
                    const nm = window.innerWidth < 640;
                    const nt = window.innerWidth >= 640 && window.innerWidth < 1024;
                    el.style.maxHeight = nm ? '350px' : (nt ? '450px' : '500px');
                }
            }, 250);
        });

        // ── Form submit ───────────────────────────────────────────────────────
        document.getElementById('answerKeyForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.getElementById('correctData').value = JSON.stringify(table.getData());
            this.submit();
        });

    })();
    </script>

    {{-- ═══════════════════════════ Import modal ════════════════════════════════ --}}
    @include('instructors.performance-tasks.answer-sheets._import-modal', ['step' => 8])

    {{-- ── Import modal bridge ────────────────────────────────────────────── --}}
    <script>
    (function () {

        const HEADER_ROWS   = 1;
        const COL_COUNT     = 6;
        const MIN_DATA_ROWS = 15;

        const HEADER_KEYWORDS = [
            'date', 'debit', 'credit', 'account', 'description', 'title',
            'balance', 'sheet', 'assets', 'liabilities', 'equity', 'explanation', 'ref',
        ];

        window.applyImport = function () {
            const rawImport = window.__importParsedData;

            if (!rawImport || !rawImport.length) {
                document.getElementById('importErrorText').textContent = 'No data to import.';
                document.getElementById('importError').style.display   = 'flex';
                return;
            }
            if (typeof table === 'undefined' || !table) {
                document.getElementById('importErrorText').textContent = 'Spreadsheet not ready.';
                document.getElementById('importError').style.display   = 'flex';
                return;
            }

            let dataRows = [...rawImport];

            const markerIdx = dataRows.findIndex(row =>
                String(row[0] ?? '').trim() === '##DATA_START##'
            );
            if (markerIdx !== -1) {
                dataRows = dataRows.slice(markerIdx + 1);
            } else {
                function rowIsHeader(row) {
                    const cells = row.map(c => String(c ?? '').trim());
                    if (!cells.some(c => c !== '')) return false;
                    if (cells.some(c => c !== '' && !isNaN(parseFloat(c)))) return false;
                    return cells.some(cell =>
                        HEADER_KEYWORDS.some(kw => cell.toLowerCase().includes(kw))
                    );
                }
                let stripped = 0;
                while (dataRows.length > 0 && stripped < 10 && rowIsHeader(dataRows[0])) {
                    dataRows.shift(); stripped++;
                }
                if (dataRows.length > 0 && dataRows[0].every(c => String(c ?? '').trim() === '')) {
                    dataRows.shift();
                }
            }

            while (dataRows.length > 0 &&
                   dataRows[dataRows.length - 1].every(c => String(c ?? '').trim() === '')) {
                dataRows.pop();
            }

            if (dataRows.length === 0) {
                document.getElementById('importErrorText').textContent =
                    'No data rows found. Make sure the template data cells are filled in.';
                document.getElementById('importError').style.display = 'flex';
                return;
            }

            const norm = row => {
                const r = row.map(c => (c === null || c === undefined) ? '' : String(c));
                while (r.length < COL_COUNT) r.push('');
                return r.slice(0, COL_COUNT);
            };

            const currentFull = table.getData();
            const headers     = currentFull.slice(0, HEADER_ROWS);

            let newFull = [...headers, ...dataRows.map(norm)];
            while (newFull.length < HEADER_ROWS + MIN_DATA_ROWS) {
                newFull.push(Array(COL_COUNT).fill(''));
            }

            table.setData(newFull);
            closeImportModal();

            const t = document.getElementById('importToast');
            const m = document.getElementById('importToastMsg');
            if (t && m) {
                m.textContent     = `Imported ${dataRows.length} data rows successfully. Review then save.`;
                t.style.display   = 'flex';
                t.style.opacity   = '1';
                t.style.transform = 'translateY(0)';
                setTimeout(() => {
                    t.style.opacity   = '0';
                    t.style.transform = 'translateY(8px)';
                    setTimeout(() => { t.style.display = 'none'; }, 300);
                }, 3500);
            }
        };

    })();
    </script>

</x-app-layout>