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
            background: linear-gradient(135deg,#581c87 0%,#7c3aed 50%,#a78bfa 100%);
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
            background: linear-gradient(135deg,#faf5ff 0%,#f3e8ff 100%);
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
        .instructions-box p { color:#6b21a8; font-size:.875rem; line-height:1.5; }
        .instructions-icon { width:1.25rem; height:1.25rem; color:#8b5cf6; flex-shrink:0; }

        /* ── Responsive typography ────────────────────────────────────── */
        @media (max-width:640px) {
            .answer-key-header { padding:1.5rem; }
            .header-title { font-size:1.75rem; }
            .header-description { font-size:.875rem; }
            .step-badge { font-size:.75rem; padding:.375rem .75rem; }
        }
        @media (min-width:640px) and (max-width:1024px) { .header-title { font-size:2rem; } }
        @media (min-width:1024px) { .header-title { font-size:2.5rem; } }

        /* ── jSpreadsheet overrides ───────────────────────────────────── */
        body { overflow-x: hidden; }

        /* Wrapper sizing */
        #spreadsheet { width: 100%; }
        #spreadsheet .jexcel_content { overflow: auto; }

        /* Zebra + border styling to match previous HOT look */
        .jexcel tbody tr:nth-child(odd)  td { background-color: #fafafa; }
        .jexcel tbody tr:nth-child(even) td { background-color: #ffffff; }
        .jexcel td { border-color: #d1d5db !important; }

        /* Header row styles (rows 1–2 in jss = index 0–1) */
        .jexcel tbody tr:nth-child(1) td,
        .jexcel tbody tr:nth-child(2) td {
            font-weight: 700 !important;
            text-align: center !important;
            background-color: #f0fdf4 !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        /* Selection highlight purple tint */
        .jexcel td.highlight { background-color: rgba(147,51,234,.1) !important; }

        /* Scrollbar */
        #spreadsheet ::-webkit-scrollbar { width:6px; height:6px; }
        #spreadsheet ::-webkit-scrollbar-track { background: transparent; }
        #spreadsheet ::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:9999px; }

        @media (max-width:640px) {
            .jexcel td, .jexcel th { font-size:12px; padding:4px; }
        }
        @media (min-width:640px) and (max-width:1024px) {
            .jexcel td, .jexcel th { font-size:13px; }
        }
    </style>

    <div class="py-4 sm:py-6 lg:py-8">

        {{-- ── Flash messages ───────────────────────────────────────────── --}}
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
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
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
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-green-400 hover:text-green-600 transition-colors">
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
                <span>Answer Key – Step 1 of 10</span>
            </div>

            <h1 class="header-title">Answer Key: Journal Entries</h1>

            <p class="header-description">
                Create the correct answer key for Journal Entries. This will be used to automatically grade student submissions.
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
                  action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 1]) }}"
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Answer Key &amp; Continue
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>{{-- /card --}}
    </div>

    {{-- ═══════════════════ jSpreadsheet initialisation ═════════════════════ --}}
    <script>
    (function () {


        const COL_COUNT = 15;
        const MIN_ROWS  = 17;   // visible data rows (excl. the 2 header rows)

        // ── Saved data from Laravel ───────────────────────────────────────────
        const savedData = @json($sheet->correct_data ?? null);

        // ── Header rows ───────────────────────────────────────────────────────
        const headerRow1 = [
            '',
            'ASSETS',      '', '', '', '', '',        // B–G  (6 cols, merged below)
            'LIABILITIES', '',                         // H–I  (2 cols, merged)
            "OWNER'S EQUITY", '', '',                  // J–L  (3 cols, merged)
            'EXPENSES',    '', '', ''                   // M–P  (4 cols, merged)
        ];
        const headerRow2 = [
            '',
            'Cash', 'Accounts Receivable', 'Supplies',
            'Furniture & Fixtures', 'Land', 'Equipment',
            'Accounts Payable', 'Notes Payable',
            'Capital', 'Withdrawal', 'Service Revenue',
            'Rent Expense', 'Utilities Expense', 'Salaries Expense'
        ];
        const blankRow  = () => Array(COL_COUNT).fill('');

        // ── Build initial data ────────────────────────────────────────────────
        let dataRows;
        if (savedData) {
            const parsed = JSON.parse(savedData);
            // Old saves had no headers (≤15 rows); new saves have 2 header rows + data
            dataRows = parsed.length <= MIN_ROWS
                ? parsed
                : parsed.slice(2);          // strip saved headers — we re-inject them
        } else {
            dataRows = Array(MIN_ROWS).fill(null).map(blankRow);
        }

        // Pad short saves to MIN_ROWS
        while (dataRows.length < MIN_ROWS) dataRows.push(blankRow());

        // Full dataset passed to jSpreadsheet (headers first, then data)
        const fullData = [headerRow1, headerRow2, ...dataRows];

        const isMobile = window.innerWidth < 640;
        const colW     = isMobile ? 90 : 110;

        const columns = Array(COL_COUNT).fill(null).map(() => ({
            type  : 'text',
            width : colW,
            align : 'center',
            wordWrap: true,
        }));

        const mergeCells = {
            'B1': [6, 1],   // ASSETS       → B1..G1
            'H1': [2, 1],   // LIABILITIES  → H1..I1
            'J1': [3, 1],   // OWNER'S EQ.  → J1..L1
            'M1': [3, 1],   // EXPENSES     → M1..O1
        };

        const letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
        const headerStyle = {};
        letters.forEach(l => {
            headerStyle[`${l}1`] = 'font-weight:700;text-align:center;background:#dcfce7;';
            headerStyle[`${l}2`] = 'font-weight:700;text-align:center;background:#f0fdf4;';
        });

        // ── Init jSpreadsheet ─────────────────────────────────────────────────
        const container = document.getElementById('spreadsheet');

        const table = jspreadsheet(container, {
            data             : fullData,
            columns          : columns,
            mergeCells       : mergeCells,
            style            : headerStyle,
            defaultColWidth  : colW,
            minDimensions    : [COL_COUNT, fullData.length],
            tableWidth       : '100%',
            tableOverflow    : true,
            tableHeight      : isMobile ? '350px' : (window.innerWidth < 1024 ? '450px' : '500px'),
            allowFormulas    : true,
            columnSorting    : false,
            columnDrag       : false,
            rowDrag          : false,
            allowInsertRow   : true,
            allowInsertColumn: false,   // column structure is fixed
            allowDeleteRow   : true,
            allowDeleteColumn: false,
            columnResize     : true,
            rowResize        : true,
            copyCompatibility: true,    // Ctrl+C / Ctrl+V
            parseFormulas    : true,

            // ── Context menu ─────────────────────────────────────────────────
            contextMenu: function(obj, x, y, e) {
                const items = [];
                items.push({ title: 'Insert row above', onclick: () => obj.insertRow(1, parseInt(y), true) });
                items.push({ title: 'Insert row below', onclick: () => obj.insertRow(1, parseInt(y)) });
                items.push({ title: 'Delete row',       onclick: () => obj.deleteRow(parseInt(y)) });
                items.push({ type: 'line' });
                items.push({ title: 'Copy',  onclick: () => obj.copy(true) });
                items.push({ title: 'Paste', onclick: () => {
                    if (navigator.clipboard) {
                        navigator.clipboard.readText().then(t => obj.paste(x, y, t));
                    }
                }});
                return items;
            },
        });

        // ── Expose globally so the import modal can call table.setData() ──────
        window.hot   = null;   // nullify in case old code checks for `hot`
        window.table = table;

        // ── Responsive height on window resize ───────────────────────────────
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const h = window.innerWidth < 640 ? '350px'
                        : window.innerWidth < 1024 ? '450px'
                        : '500px';
                container.querySelector('.jexcel_content').style.maxHeight = h;
            }, 250);
        });

        // ── Form submit: serialise full grid (headers + data) ─────────────────
        document.getElementById('answerKeyForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const raw = table.getData();   // 2-D array; includes header rows

            // We persist the FULL grid (incl. header rows) so reloading works.
            document.getElementById('correctData').value = JSON.stringify(raw);
            this.submit();
        });

    })();
    </script>

    {{-- ═══════════════════════ Import modal ════════════════════════════════ --}}
    {{--
        The import modal calls window.applyImport() which is defined inside
        _import-modal.blade.php. That function references `hot` (Handsontable).
        We patch it below so it works with jSpreadsheet instead.
    --}}
    @include('instructors.performance-tasks.answer-sheets._import-modal', ['step' => 1])

    {{-- Patch applyImport to work with jSpreadsheet ──────────────────────── --}}
    <script>
    (function () {
        // Wait until _import-modal.blade.php has defined applyImport, then override it.
        const STEP_CONFIG_LOCAL = {
            1: { headerRows: 2 },
        };
        const HEADER_KEYWORDS_LOCAL = [
            'date','debit','credit','account','description','title',
            'amount','month','day','revenue','expense','balance',
            'assets','liabilities','equity','transaction','explanation',
        ];

        window.applyImport = function () {
            // importParsedData is declared inside the IIFE in _import-modal.blade.php;
            // expose it by re-reading from the modal's closure via a small bridge below.
            const rawImport = window.__importParsedData;
            if (!rawImport || !rawImport.length) {
                document.getElementById('importErrorText').textContent = 'No data to import.';
                document.getElementById('importError').style.display  = 'flex';
                return;
            }
            if (typeof table === 'undefined' || !table) {
                document.getElementById('importErrorText').textContent = 'Spreadsheet not ready.';
                document.getElementById('importError').style.display  = 'flex';
                return;
            }

            const { headerRows = 0 } = STEP_CONFIG_LOCAL[1] || {};
            let dataRows = [...rawImport];

            // Strategy 1: sentinel marker
            const markerIdx = dataRows.findIndex(row =>
                String(row[0] ?? '').trim() === '##DATA_START##'
            );
            if (markerIdx !== -1) {
                dataRows = dataRows.slice(markerIdx + 1);
            } else {
                // Strategy 2: keyword-based header stripping
                function rowIsHeader(row) {
                    const cells = row.map(c => String(c ?? '').trim());
                    if (!cells.some(c => c !== '')) return false;
                    if (cells.some(c => c !== '' && !isNaN(parseFloat(c)))) return false;
                    return cells.some(cell =>
                        HEADER_KEYWORDS_LOCAL.some(kw => cell.toLowerCase().includes(kw))
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

            const COL_COUNT = 15;
            const MIN_ROWS  = 17;

            const norm = row => {
                const r = row.map(c => (c === null || c === undefined) ? '' : String(c));
                while (r.length < COL_COUNT) r.push('');
                return r.slice(0, COL_COUNT);
            };

            // Preserve the two header rows, replace data rows below
            const currentFull = table.getData();
            const preserved   = currentFull.slice(0, headerRows);   // e.g. rows 0-1

            let newFull = [...preserved, ...dataRows.map(norm)];
            while (newFull.length < headerRows + MIN_ROWS) {
                newFull.push(Array(COL_COUNT).fill(''));
            }

            table.setData(newFull);


            closeImportModal();

            if (typeof window.showToast === 'function') {
                window.showToast(`Imported ${dataRows.length} data rows successfully. Review then save.`);
            }
        };
    })();
    </script>

    {{-- Bridge: expose importParsedData from modal's IIFE so our patch can read it --}}
    <script>

    (function () {

        const origConfirm = document.getElementById('importConfirmBtn');
        if (origConfirm) {
            // Watch for the button becoming enabled — at that point importParsedData is set
            new MutationObserver(() => {
                if (!origConfirm.disabled) {

                }
            }).observe(origConfirm, { attributes: true, attributeFilter: ['disabled'] });
        }

        if (typeof XLSX !== 'undefined') {
            const _origToJson = XLSX.utils.sheet_to_json;
            XLSX.utils.sheet_to_json = function (ws, opts) {
                const result = _origToJson.call(this, ws, opts);
                // When called with { header:1 } (which the modal uses) the result
                // is a 2-D array — exactly what we need.
                if (opts && opts.header === 1) {
                    window.__importParsedData = result.filter((row, i) =>
                        i < 3 || row.some(c => c !== '' && c != null)
                    );
                }
                return result;
            };
        }
    })();
    </script>

</x-app-layout>