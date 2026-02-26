<x-app-layout>
    {{-- ═══════════════════════════ jSpreadsheet CDN ═══════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link  rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <style>
        /* ── Page Header ──────────────────────────────────────────────── */
        .answer-key-header {
            background: linear-gradient(135deg, #f9fafb 0%, #f3e8ff 50%, #faf5ff 100%);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e9d5ff;
            box-shadow: 0 4px 6px -1px rgba(139,92,246,.1), 0 2px 4px -1px rgba(139,92,246,.06);
            position: relative;
            overflow: hidden;
        }
        .answer-key-header::before {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(167,139,250,.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .answer-key-header::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -5%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(196,181,253,.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem 1rem;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: #fff;
            border-radius: 9999px;
            font-size: .875rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(139,92,246,.3), 0 2px 4px -1px rgba(139,92,246,.2);
            margin-bottom: 1rem;
            position: relative; z-index: 1;
            transition: transform .2s, box-shadow .2s;
        }
        .step-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px -1px rgba(139,92,246,.4), 0 3px 5px -1px rgba(139,92,246,.3);
        }
        .step-badge svg { width:1rem; height:1rem; animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite; }

        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.7} }

        .header-title {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, #581c87 0%, #7c3aed 50%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 1rem;
            position: relative; z-index: 1;
        }
        .header-description {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.625;
            max-width: 48rem;
            margin-bottom: .75rem;
            position: relative; z-index: 1;
        }
        .task-info-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .375rem .875rem;
            background: #fff;
            color: #7c3aed;
            border-radius: .5rem;
            font-size: .875rem;
            font-weight: 600;
            border: 1px solid #e9d5ff;
            box-shadow: 0 1px 3px 0 rgba(139,92,246,.1);
            position: relative; z-index: 1;
            transition: all .2s;
        }
        .task-info-badge:hover {
            background: #faf5ff;
            border-color: #d8b4fe;
            transform: translateX(4px);
        }
        .task-info-badge svg { width:1rem; height:1rem; }

        /* ── Instructions box ─────────────────────────────────────────── */
        .instructions-box {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 1px solid #e9d5ff;
            border-left: 4px solid #8b5cf6;
            border-radius: .75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(139,92,246,.08);
        }
        .instructions-box h3 {
            color: #581c87;
            font-size: .9375rem;
            font-weight: 700;
            margin-bottom: .5rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .instructions-box p { color: #6b21a8; font-size: .875rem; line-height: 1.5; }
        .instructions-icon { width: 1.25rem; height: 1.25rem; color: #8b5cf6; flex-shrink: 0; }

        /* ── Responsive typography ────────────────────────────────────── */
        @media (max-width: 640px) {
            .answer-key-header  { padding: 1.5rem; }
            .header-title       { font-size: 1.75rem; }
            .header-description { font-size: .875rem; }
            .step-badge         { font-size: .75rem; padding: .375rem .75rem; }
        }
        @media (min-width: 640px) and (max-width: 1024px) { .header-title { font-size: 2rem; } }
        @media (min-width: 1024px)                         { .header-title { font-size: 2.5rem; } }

        /* ── jSpreadsheet overrides ───────────────────────────────────── */
        body { overflow-x: hidden; }
        #spreadsheet { width: 100%; }
        #spreadsheet .jexcel_content { overflow: auto; }

        /* Cell borders */
        .jexcel td { border-color: #d1d5db !important; }

        /* Header row 1 — "Date" merged cell spanning cols A-B, plus column labels */
        .jexcel tbody tr:nth-child(1) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #f0fdf4 !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        /* Selection tint */
        .jexcel td.highlight { background-color: rgba(147,51,234,.08) !important; }

        /* Scrollbar */
        #spreadsheet ::-webkit-scrollbar        { width: 6px; height: 6px; }
        #spreadsheet ::-webkit-scrollbar-track  { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb  { background: #d1d5db; border-radius: 9999px; }

        @media (max-width: 640px) {
            .jexcel td, .jexcel th { font-size: 12px; padding: 4px; }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .jexcel td, .jexcel th { font-size: 13px; }
        }
    </style>

    <div class="py-4 sm:py-6 lg:py-8">

        {{-- ── Flash messages ──────────────────────────────────────────── --}}
        @if (session('error'))
            <div class="mb-6">
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
                    <button type="button" onclick="this.parentElement.parentElement.remove()"
                            class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6">
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
                    <button type="button" onclick="this.parentElement.parentElement.remove()"
                            class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- ── Page header ──────────────────────────────────────────────── --}}
        <div class="answer-key-header">
            <div class="step-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                <span>Answer Key – Step 2 of 10</span>
            </div>

            <h1 class="header-title">Answer Key: Journalizing Entries</h1>

            <p class="header-description">
                Create the correct answer key for Journalizing Entries. This will be used to automatically grade student submissions.
            </p>

            <div class="task-info-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Task: {{ $task->title }}</span>
            </div>
        </div>

        {{-- ── Main card ────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Instructions --}}
            <div class="p-4 sm:p-6">
                <div class="instructions-box">
                    <h3>
                        <svg class="instructions-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Instructions for Creating Answer Key
                    </h3>
                    <p>
                        Fill in the correct answers below. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison.
                    </p>
                </div>
            </div>

            {{-- Form --}}
            <form id="answerKeyForm"
                  action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 2]) }}"
                  method="POST">
                @csrf
                @method('PUT')

                {{-- Spreadsheet --}}
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto"
                             style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>

                    {{-- Mobile hint --}}
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">

                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task) }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Answer Sheets
                        </a>

                        <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2">
                            <button type="button" onclick="openImportModal()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import File
                            </button>

                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Answer Key &amp; Continue
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>{{-- /card --}}
    </div>

    {{-- ═══════════════════ jSpreadsheet initialisation ═══════════════════ --}}
    <script>
    (function () {

        const COL_COUNT = 6;
        const MIN_ROWS  = 15;   // data rows below the 2 header rows
        const HEADER_ROWS = 2;

        // ── Saved data from Laravel ──────────────────────────────────────────
        const savedData = @json($sheet->correct_data ?? null);

        // ── Header rows ──────────────────────────────────────────────────────
        // Row 1 (index 0): "Date" spans cols A-B; rest are individual labels
        const headerRow1 = ['Date', '', 'Account Titles and Explanation', 'Account Number', 'Debit (₱)', 'Credit (₱)'];
        // Row 2 (index 1): sub-labels under "Date"

        const blankRow = () => Array(COL_COUNT).fill('');

        // ── Build initial data ────────────────────────────────────────────────
        let dataRows;
        if (savedData) {
            const parsed = JSON.parse(savedData);
            // Old saves had no headers (≤15 rows); new saves have 2 header rows + data
            dataRows = parsed.length <= MIN_ROWS
                ? parsed
                : parsed.slice(HEADER_ROWS);
        } else {
            dataRows = Array(MIN_ROWS).fill(null).map(blankRow);
        }

        // Pad to MIN_ROWS
        while (dataRows.length < MIN_ROWS) dataRows.push(blankRow());

        // Full dataset (header rows first, then data rows)
        const fullData = [headerRow1, ...dataRows];

        // ── Column definitions ────────────────────────────────────────────────
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        const colWidths = isMobile
            ? [80,  80,  300, 80,  120, 120]
            : isTablet
                ? [90,  90,  350, 90,  130, 130]
                : [100, 100, 400, 100, 150, 150];


        // ── Merge cells ───────────────────────────────────────────────────────
        // "Date" in row 1 spans columns A & B  →  "A1": [2, 1]
        const mergeCells = {
            'A1': [2, 1],
        };

        // ── Cell styles for header rows ───────────────────────────────────────
        const letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        const headerStyle = {};
        letters.forEach(l => {
            headerStyle[`${l}1`] = 'font-weight:700;text-align:center;background:#f0fdf4;white-space:normal;word-break:break-word;';
        });

        // ── Init jSpreadsheet ─────────────────────────────────────────────────
        const container = document.getElementById('spreadsheet');

        const table = jspreadsheet(container, {
            data            : fullData,
            minDimensions    : [COL_COUNT, fullData.length],
            defaultColWidth  : isMobile ? 120 : 150,
            mergeCells      : mergeCells,
            style           : headerStyle,
            tableWidth      : '100%',
            tableOverflow   : true,
            tableHeight     : isMobile ? '350px' : (isTablet ? '450px' : '500px'),
            allowFormulas   : true,
            columnSorting   : false,
            columnDrag      : false,
            rowDrag         : false,
            allowInsertRow  : true,
            allowInsertColumn: false,
            allowDeleteRow  : true,
            allowDeleteColumn: false,
            columnResize    : true,
            rowResize       : true,
            copyCompatibility: true,
            minSpareRows    : 1,

            // ── Context menu ─────────────────────────────────────────────────
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
        });

        // ── Expose globally so the import-modal bridge can call table.setData() ─
        window.table = table;

        // ── Responsive height on window resize ───────────────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const newMobile = window.innerWidth < 640;
                const newTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
                const h = newMobile ? '350px' : (newTablet ? '450px' : '500px');
                const el = container.querySelector('.jexcel_content');
                if (el) el.style.maxHeight = h;
            }, 250);
        });

        // ── Form submit: serialise full grid ──────────────────────────────────
        document.getElementById('answerKeyForm').addEventListener('submit', function (e) {
            e.preventDefault();
            // getData() returns the full 2-D array including our header rows
            document.getElementById('correctData').value = JSON.stringify(table.getData());
            this.submit();
        });

    })();
    </script>

    {{-- ═══════════════════════ Import modal ════════════════════════════════ --}}
    @include('instructors.performance-tasks.answer-sheets._import-modal', ['step' => 2])

    {{-- ── Patch applyImport + XLSX bridge for jSpreadsheet ─────────────── --}}
    <script>
    (function () {

        // ── 1. Intercept XLSX.utils.sheet_to_json to capture parsed rows ──────
        if (typeof XLSX !== 'undefined') {
            const _orig = XLSX.utils.sheet_to_json;
            XLSX.utils.sheet_to_json = function (ws, opts) {
                const result = _orig.call(this, ws, opts);
                if (opts && opts.header === 1) {
                    window.__importParsedData = result.filter((row, i) =>
                        i < 3 || row.some(c => c !== '' && c != null)
                    );
                }
                return result;
            };
        }

        // ── 2. Replace applyImport to use jSpreadsheet table.setData() ───────
        const HEADER_ROWS = 1;
        const COL_COUNT   = 6;
        const MIN_ROWS    = 15;

        const HEADER_KEYWORDS = [
            'date','debit','credit','account','description','title',
            'amount','month','day','revenue','expense','balance',
            'assets','liabilities','equity','transaction','explanation',
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

            // Strategy 1 – sentinel marker (##DATA_START##)
            const markerIdx = dataRows.findIndex(row =>
                String(row[0] ?? '').trim() === '##DATA_START##'
            );
            if (markerIdx !== -1) {
                dataRows = dataRows.slice(markerIdx + 1);
            } else {
                // Strategy 2 – keyword-based header stripping
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
                // Drop one leading blank separator row
                if (dataRows.length > 0 && dataRows[0].every(c => String(c ?? '').trim() === '')) {
                    dataRows.shift();
                }
            }

            // Strip trailing blank rows
            while (dataRows.length > 0 &&
                   dataRows[dataRows.length - 1].every(c => String(c ?? '').trim() === '')) {
                dataRows.pop();
            }

            if (dataRows.length === 0) {
                document.getElementById('importErrorText').textContent =
                    'No data rows found. Make sure you filled in the yellow cells in the template.';
                document.getElementById('importError').style.display = 'flex';
                return;
            }

            // Normalise each imported row to COL_COUNT columns
            const norm = row => {
                const r = row.map(c => (c === null || c === undefined) ? '' : String(c));
                while (r.length < COL_COUNT) r.push('');
                return r.slice(0, COL_COUNT);
            };

            // Rebuild: preserve the 2 header rows, replace data rows below
            const currentFull  = table.getData();
            const headersCopy  = currentFull.slice(0, HEADER_ROWS);

            let newFull = [...headersCopy, ...dataRows.map(norm)];

            // Ensure at least MIN_ROWS data rows exist
            while (newFull.length < HEADER_ROWS + MIN_ROWS) {
                newFull.push(Array(COL_COUNT).fill(''));
            }

            table.setData(newFull);

            closeImportModal();

            // Show success toast (showToast is defined inside the modal IIFE)
            if (typeof window.__showToast === 'function') {
                window.__showToast(`Imported ${dataRows.length} data rows successfully. Review then save.`);
            } else {
                // Fallback: briefly flash the toast element directly
                const t = document.getElementById('importToast');
                const m = document.getElementById('importToastMsg');
                if (t && m) {
                    m.textContent = `Imported ${dataRows.length} data rows successfully. Review then save.`;
                    t.style.display   = 'flex';
                    t.style.opacity   = '1';
                    t.style.transform = 'translateY(0)';
                    setTimeout(() => {
                        t.style.opacity   = '0';
                        t.style.transform = 'translateY(8px)';
                        setTimeout(() => { t.style.display = 'none'; }, 300);
                    }, 3500);
                }
            }
        };

    })();
    </script>

</x-app-layout>