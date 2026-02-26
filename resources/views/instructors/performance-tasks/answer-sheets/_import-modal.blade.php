{{--
    REUSABLE IMPORT MODAL  (v3 — fixed responsive layout & positioning)
    ─────────────────────────────────────────────────────────────────────────
    Save to:
        resources/views/instructors/performance-tasks/answer-sheets/_import-modal.blade.php

    Place the template file at:
        public/templates/answer_key_templates.xlsx

    Usage — at the bottom of EVERY step-N.blade.php before </x-app-layout>:
        @include('instructors.performance-tasks.answer-sheets._import-modal', ['step' => N])

    ── Action Buttons block (copy-paste into each step, change step number) ──
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import File
                </button>
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Answer Key & Continue
                </button>
            </div>

        </div>
    </div>
--}}

{{-- ═══════════════════════════ IMPORT MODAL ═══════════════════════════ --}}

{{-- Overlay + centered modal using fixed+flex --}}
<div
    id="importModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="importModalTitle"
    class="import-modal-root"
    style="display:none; position:fixed; inset:0; z-index:9999;
           align-items:center; justify-content:center; padding:1rem;"
>
    {{-- Backdrop --}}
    <div
        id="importBackdrop"
        onclick="closeImportModal()"
        style="position:fixed; inset:0; background:rgba(0,0,0,0.55); backdrop-filter:blur(3px);"
    ></div>

    {{-- Panel --}}
    <div
        id="importPanel"
        style="position:relative; z-index:1; width:100%; max-width:520px;
               max-height:90vh; overflow-y:auto; border-radius:1rem;
               background:#fff; box-shadow:0 25px 60px rgba(0,0,0,0.25);
               display:flex; flex-direction:column;"
    >
        {{-- ── Header ── --}}
        <div style="background:linear-gradient(135deg,#059669 0%,#0d9488 100%);
                    padding:1.25rem 1.5rem; border-radius:1rem 1rem 0 0; flex-shrink:0;">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:2.25rem; height:2.25rem; background:rgba(255,255,255,0.2);
                                border-radius:0.5rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h2 id="importModalTitle" style="color:#fff; font-weight:700; font-size:0.9375rem; margin:0; line-height:1.3;">
                            Import Answer Key
                        </h2>
                        <p style="color:rgba(255,255,255,0.75); font-size:0.75rem; margin:0; margin-top:1px;">
                            Step {{ $step ?? '?' }} of 10 &mdash; upload your spreadsheet
                        </p>
                    </div>
                </div>
                <button
                    onclick="closeImportModal()"
                    aria-label="Close"
                    style="background:rgba(255,255,255,0.15); border:none; border-radius:0.5rem;
                           width:2rem; height:2rem; display:flex; align-items:center; justify-content:center;
                           cursor:pointer; color:#fff; flex-shrink:0; transition:background 0.15s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.25)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.15)'"
                >
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Body ── --}}
        <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem; flex:1; overflow-y:auto;">

            {{-- Step tracker --}}
            <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.75rem;">
                <span style="width:1.25rem; height:1.25rem; background:#059669; color:#fff;
                             border-radius:50%; display:inline-flex; align-items:center;
                             justify-content:center; font-weight:700; flex-shrink:0; font-size:0.65rem;">1</span>
                <span style="color:#374151; font-weight:600;">Download template</span>
                <span style="color:#d1d5db; margin:0 0.125rem;">›</span>
                <span style="width:1.25rem; height:1.25rem; background:#e5e7eb; color:#6b7280;
                             border-radius:50%; display:inline-flex; align-items:center;
                             justify-content:center; font-weight:700; flex-shrink:0; font-size:0.65rem;">2</span>
                <span style="color:#6b7280;">Fill &amp; upload</span>
            </div>

            {{-- Download card --}}
            <div style="border:1.5px solid #a7f3d0; border-radius:0.75rem; background:#f0fdf4;
                        padding:1rem; display:flex; gap:0.875rem; align-items:flex-start;">
                <div style="width:2.5rem; height:2.5rem; background:#059669; border-radius:0.625rem;
                            display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div style="flex:1; min-width:0;">
                    <p style="font-weight:700; color:#064e3b; font-size:0.875rem; margin:0 0 0.25rem;">
                        Answer Key Template (.xlsx)
                    </p>
                    <p style="font-size:0.75rem; color:#065f46; line-height:1.5; margin:0 0 0.75rem;">
                        One sheet per step. Fill the <strong>yellow cells</strong> with correct answers, then upload below.
                    </p>
                    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:0.625rem;">
                        <a
                            href="{{ asset('templates/answer_key_templates.xlsx') }}"
                            download="answer_key_templates.xlsx"
                            style="display:inline-flex; align-items:center; gap:0.375rem;
                                   padding:0.4rem 0.875rem; background:#059669; color:#fff;
                                   border-radius:0.5rem; font-size:0.75rem; font-weight:600;
                                   text-decoration:none; transition:background 0.15s;"
                            onmouseover="this.style.background='#047857'"
                            onmouseout="this.style.background='#059669'"
                        >
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Download Template
                        </a>
                        <span style="font-size:0.72rem; color:#059669;">
                            → open sheet <strong>&ldquo;Step {{ $step ?? '?' }}&rdquo;</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="flex:1; height:1px; background:#e5e7eb;"></div>
                <span style="font-size:0.65rem; color:#9ca3af; font-weight:600; letter-spacing:0.05em; white-space:nowrap; text-transform:uppercase;">
                    then upload your filled file
                </span>
                <div style="flex:1; height:1px; background:#e5e7eb;"></div>
            </div>

            {{-- Info banner --}}
            <div style="display:flex; gap:0.625rem; padding:0.75rem; background:#eff6ff;
                        border:1px solid #bfdbfe; border-radius:0.625rem; align-items:flex-start;">
                <svg width="15" height="15" fill="#3b82f6" viewBox="0 0 20 20" style="flex-shrink:0; margin-top:1px;">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p style="font-size:0.75rem; color:#1d4ed8; margin:0; line-height:1.5;">
                    Accepts <strong>.xlsx · .xls · .csv</strong>.
                    Multi-sheet files: pick which sheet to import.
                    Spreadsheet header rows are always preserved.
                </p>
            </div>

            {{-- Drop zone --}}
            <div
                id="importDropZone"
                onclick="document.getElementById('importFileInput').click()"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                ondrop="handleDrop(event)"
                style="border:2px dashed #d1d5db; border-radius:0.75rem; padding:2rem 1.5rem;
                       text-align:center; cursor:pointer; transition:all 0.2s;
                       background:#fafafa; position:relative;"
                onmouseover="this.style.borderColor='#059669'; this.style.background='#f0fdf4';"
                onmouseout="if(!importDropZone.classList.contains('dz-active')){this.style.borderColor='#d1d5db'; this.style.background='#fafafa';}"
            >
                {{-- Default state --}}
                <div id="dropZoneContent">
                    <div style="width:3rem; height:3rem; background:#f3f4f6; border-radius:50%;
                                display:flex; align-items:center; justify-content:center; margin:0 auto 0.75rem;">
                        <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p style="font-size:0.875rem; font-weight:600; color:#374151; margin:0 0 0.25rem;">
                        Drop file here or <span style="color:#059669; text-decoration:underline;">click to browse</span>
                    </p>
                    <p style="font-size:0.75rem; color:#9ca3af; margin:0;">.xlsx &middot; .xls &middot; .csv &mdash; max 10 MB</p>
                </div>

                {{-- Selected state (hidden) --}}
                <div id="selectedFileInfo" style="display:none;">
                    <div style="width:3rem; height:3rem; background:#dcfce7; border-radius:50%;
                                display:flex; align-items:center; justify-content:center; margin:0 auto 0.75rem;">
                        <svg width="22" height="22" fill="#059669" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p id="selectedFileName" style="font-size:0.875rem; font-weight:600; color:#065f46; margin:0 0 0.125rem; word-break:break-all;"></p>
                    <p id="selectedFileSize" style="font-size:0.75rem; color:#9ca3af; margin:0 0 0.5rem;"></p>
                    <button
                        type="button"
                        onclick="clearImportFile(event)"
                        style="background:none; border:none; font-size:0.75rem; color:#ef4444;
                               cursor:pointer; text-decoration:underline; padding:0;"
                    >Remove &amp; choose another</button>
                </div>
            </div>

            <input type="file" id="importFileInput" accept=".xlsx,.xls,.csv" style="display:none;" onchange="handleFileSelect(this)">

            {{-- Sheet selector --}}
            <div id="sheetSelectorContainer" style="display:none;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:0.375rem;">
                    Select Sheet
                    <span style="font-weight:400; color:#9ca3af; margin-left:0.25rem;">— multiple sheets detected</span>
                </label>
                <select
                    id="sheetSelector"
                    style="width:100%; border:1.5px solid #d1d5db; border-radius:0.5rem;
                           padding:0.5rem 0.75rem; font-size:0.875rem; background:#fff;
                           outline:none; color:#111827; transition:border-color 0.15s;"
                    onfocus="this.style.borderColor='#059669'"
                    onblur="this.style.borderColor='#d1d5db'"
                ></select>
                <p style="font-size:0.72rem; color:#9ca3af; margin:0.375rem 0 0;">
                    💡 Using the template? Choose <strong>&ldquo;Step {{ $step ?? '?' }}&rdquo;</strong>.
                </p>
            </div>

            {{-- Preview --}}
            <div id="importPreviewContainer" style="display:none;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                    <p style="font-size:0.75rem; font-weight:600; color:#374151; margin:0;">
                        Preview <span style="font-weight:400; color:#9ca3af;">(first 5 rows)</span>
                    </p>
                    <span id="previewRowCount"
                          style="font-size:0.7rem; background:#f3f4f6; color:#6b7280;
                                 padding:0.2rem 0.5rem; border-radius:9999px;"></span>
                </div>
                <div style="overflow-x:auto; border-radius:0.5rem; border:1px solid #e5e7eb; max-height:9rem;">
                    <table id="importPreviewTable" style="min-width:100%; font-size:0.72rem; border-collapse:collapse;">
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            {{-- Error --}}
            <div id="importError" style="display:none; gap:0.5rem; padding:0.75rem;
                                         background:#fef2f2; border:1px solid #fecaca;
                                         border-radius:0.625rem; align-items:flex-start;">
                <svg width="15" height="15" fill="#ef4444" viewBox="0 0 20 20" style="flex-shrink:0; margin-top:1px;">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p id="importErrorText" style="font-size:0.75rem; color:#b91c1c; margin:0;"></p>
            </div>

        </div>{{-- /body --}}

        {{-- ── Footer ── --}}
        <div style="padding:1rem 1.5rem; background:#f9fafb; border-top:1px solid #e5e7eb;
                    display:flex; justify-content:flex-end; gap:0.625rem;
                    border-radius:0 0 1rem 1rem; flex-shrink:0;">
            <button
                type="button"
                onclick="closeImportModal()"
                style="padding:0.5rem 1.125rem; font-size:0.875rem; font-weight:500; color:#374151;
                       background:#fff; border:1.5px solid #d1d5db; border-radius:0.5rem;
                       cursor:pointer; transition:all 0.15s;"
                onmouseover="this.style.background='#f3f4f6'"
                onmouseout="this.style.background='#fff'"
            >Cancel</button>
            <button
                type="button"
                id="importConfirmBtn"
                onclick="applyImport()"
                disabled
                style="padding:0.5rem 1.25rem; font-size:0.875rem; font-weight:600; color:#fff;
                       background:#059669; border:none; border-radius:0.5rem; cursor:pointer;
                       display:inline-flex; align-items:center; gap:0.375rem;
                       transition:all 0.15s; opacity:0.4; pointer-events:none;"
            >
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Apply to Spreadsheet
            </button>
        </div>

    </div>{{-- /panel --}}
</div>{{-- /modal --}}

{{-- Toast container --}}
<div id="importToast"
     style="position:fixed; bottom:1.5rem; right:1.5rem; z-index:10000;
            display:none; align-items:center; gap:0.5rem;
            background:#065f46; color:#fff; padding:0.75rem 1.25rem;
            border-radius:0.75rem; box-shadow:0 8px 24px rgba(0,0,0,0.2);
            font-size:0.875rem; font-weight:500; max-width:calc(100vw - 3rem);
            transition:opacity 0.3s, transform 0.3s; opacity:0; transform:translateY(8px);">
    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <span id="importToastMsg"></span>
</div>

{{-- SheetJS --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
    /* Smooth panel entrance */
    @keyframes importSlideIn {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }
    #importPanel { animation: importSlideIn 0.22s ease-out; }

    /* Scrollbar in panel body */
    #importPanel::-webkit-scrollbar { width: 5px; }
    #importPanel::-webkit-scrollbar-track { background: transparent; }
    #importPanel::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }

    /* Preview table rows */
    #importPreviewTable tr:nth-child(odd)  td { background: #f9fafb; }
    #importPreviewTable tr:nth-child(even) td { background: #fff; }
    #importPreviewTable td {
        padding: 0.3rem 0.5rem;
        border-right: 1px solid #f3f4f6;
        max-width: 130px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #374151;
        font-size: 0.72rem;
    }
    #importPreviewTable tr:first-child td {
        font-weight: 600;
        color: #111827;
        background: #f0fdf4;
    }

    /* Drop zone active state */
    #importDropZone.dz-active {
        border-color: #059669 !important;
        background: #f0fdf4 !important;
    }

    /* Confirm btn enabled */
    #importConfirmBtn:not([disabled]) {
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    #importConfirmBtn:not([disabled]):hover {
        background: #047857 !important;
    }

    /* Mobile: full-width panel */
    @media (max-width: 540px) {
        #importPanel {
            max-width: 100% !important;
            max-height: 95vh !important;
            border-radius: 0.75rem !important;
        }
    }
</style>

<script>
(function () {
    // ── Config ───────────────────────────────────────────────────────────────
    const STEP_CONFIG = {
        1:  { headerRows: 2 },
        2:  { headerRows: 2 },
        3:  { headerRows: 2 },
        4:  { headerRows: 4 },
        5:  { headerRows: 2 },
        6:  { headerRows: 5 },
        7:  { headerRows: 3 },
        8:  { headerRows: 2 },
        9:  { headerRows: 4 },
        10: { headerRows: 0 },
    };

    const CURRENT_STEP = {{ $step ?? 1 }};

    const HEADER_KEYWORDS = [
        'date','debit','credit','account','description','title',
        'amount','month','day','revenue','expense','balance',
        'assets','liabilities','equity','transaction','explanation',
    ];

    // ── State ────────────────────────────────────────────────────────────────
    let importWorkbook   = null;
    let importParsedData = [];

    // ── DOM refs ─────────────────────────────────────────────────────────────
    const $ = id => document.getElementById(id);

    // ── Modal open / close ───────────────────────────────────────────────────
    window.openImportModal = function () {
        const m = $('importModal');
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeImportModal = function () {
        $('importModal').style.display = 'none';
        document.body.style.overflow = '';
        resetState();
    };

    function resetState() {
        importWorkbook = null;
        importParsedData = [];

        $('importFileInput').value = '';
        hide('selectedFileInfo');   show('dropZoneContent');
        hide('sheetSelectorContainer');
        hide('importPreviewContainer');
        hide('importError');
        setConfirm(false);

        // Reset drop zone styling
        const dz = $('importDropZone');
        dz.style.borderColor = '#d1d5db';
        dz.style.background  = '#fafafa';
        dz.classList.remove('dz-active');
    }

    // ── Drag & drop ──────────────────────────────────────────────────────────
    window.handleDragOver = function (e) {
        e.preventDefault();
        const dz = $('importDropZone');
        dz.style.borderColor = '#059669';
        dz.style.background  = '#f0fdf4';
        dz.classList.add('dz-active');
    };
    window.handleDragLeave = function (e) {
        const dz = $('importDropZone');
        dz.style.borderColor = '#d1d5db';
        dz.style.background  = '#fafafa';
        dz.classList.remove('dz-active');
    };
    window.handleDrop = function (e) {
        e.preventDefault();
        handleDragLeave(e);
        if (e.dataTransfer.files[0]) processFile(e.dataTransfer.files[0]);
    };
    window.handleFileSelect = function (input) {
        if (input.files[0]) processFile(input.files[0]);
    };
    window.clearImportFile = function (e) {
        e.stopPropagation();
        resetState();
    };

    // ── File processing ──────────────────────────────────────────────────────
    function processFile(file) {
        hideError();

        const ext = file.name.split('.').pop().toLowerCase();
        if (!['xlsx','xls','csv'].includes(ext)) {
            return showError('Unsupported file type. Please upload .xlsx, .xls, or .csv.');
        }
        if (file.size > 10 * 1024 * 1024) {
            return showError('File too large. Maximum size is 10 MB.');
        }

        hide('dropZoneContent');
        show('selectedFileInfo');
        $('selectedFileName').textContent = file.name;
        $('selectedFileSize').textContent = fmtSize(file.size);

        const reader = new FileReader();
        reader.onload = function (ev) {
            try {
                importWorkbook = XLSX.read(new Uint8Array(ev.target.result),
                    { type: 'array', cellDates: true, raw: false });

                const names = importWorkbook.SheetNames;

                if (names.length > 1) {
                    const sel = $('sheetSelector');
                    sel.innerHTML = '';

                    const stepLabel = `Step ${CURRENT_STEP}`;
                    let best = names[0];

                    names.forEach(name => {
                        const opt = document.createElement('option');
                        opt.value = opt.textContent = name;
                        sel.appendChild(opt);
                        if (name.startsWith(stepLabel)) best = name;
                    });

                    sel.value    = best;
                    sel.onchange = () => loadSheet(sel.value);
                    show('sheetSelectorContainer');
                    loadSheet(best);
                } else {
                    hide('sheetSelectorContainer');
                    loadSheet(names[0]);
                }
            } catch (err) {
                showError('Could not read file: ' + err.message);
            }
        };
        reader.readAsArrayBuffer(file);
    }

    function loadSheet(name) {
        hideError();
        try {
            const ws  = importWorkbook.Sheets[name];
            const raw = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '', raw: false });
            importParsedData = raw.filter((row, i) =>
                i < 3 || row.some(c => c !== '' && c != null)
            );
            renderPreview();
            setConfirm(importParsedData.length > 0);
        } catch (err) {
            showError('Failed to parse sheet: ' + err.message);
        }
    }

    // ── Preview ──────────────────────────────────────────────────────────────
    function renderPreview() {
        const tbody  = $('importPreviewTable').querySelector('tbody');
        tbody.innerHTML = '';
        const rows   = importParsedData.slice(0, 5);
        const nCols  = Math.max(...rows.map(r => r.length), 1);
        const visible = Math.min(nCols, 7);

        rows.forEach((row, ri) => {
            const tr = document.createElement('tr');
            for (let ci = 0; ci < visible; ci++) {
                const td = document.createElement('td');
                td.textContent = row[ci] != null ? String(row[ci]) : '';
                tr.appendChild(td);
            }
            if (nCols > 7) {
                const td = document.createElement('td');
                td.textContent = `+${nCols - 7} more…`;
                td.style.color = '#9ca3af';
                td.style.fontStyle = 'italic';
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
        });

        $('previewRowCount').textContent = `${importParsedData.length} rows · ${nCols} cols`;
        show('importPreviewContainer');
    }

    // ── Apply to HOT ─────────────────────────────────────────────────────────
    window.applyImport = function () {
        if (!importParsedData.length) return showError('No data to import.');
        if (typeof hot === 'undefined' || !hot) return showError('Spreadsheet not ready.');

        const { headerRows = 0 } = STEP_CONFIG[CURRENT_STEP] || {};
        const currentData = hot.getData();
        const numCols     = hot.countCols();

        let dataRows = [...importParsedData];

        // ── Strategy 1: sentinel marker (templates downloaded from this system) ──
        // The template has an invisible row whose first cell = '##DATA_START##'.
        // Everything up to and INCLUDING that row is header — skip it all.
        const markerIndex = dataRows.findIndex(row =>
            String(row[0] ?? '').trim() === '##DATA_START##'
        );

        if (markerIndex !== -1) {
            // Found the marker — data starts immediately after it
            dataRows = dataRows.slice(markerIndex + 1);
        } else {
            // ── Strategy 2: keyword-based stripping (user's own file) ──────────
            // Strip rows from the top that look like labels/headers:
            // a row is a header if it has NO numbers AND matches at least one keyword.
            function rowIsHeader(row) {
                const cells = row.map(c => String(c ?? '').trim());
                if (!cells.some(c => c !== '')) return false; // blank = stop
                if (cells.some(c => c !== '' && !isNaN(parseFloat(c)))) return false; // has number = data
                return cells.some(cell =>
                    HEADER_KEYWORDS.some(kw => cell.toLowerCase().includes(kw))
                );
            }

            let stripped = 0;
            while (dataRows.length > 0 && stripped < 10 && rowIsHeader(dataRows[0])) {
                dataRows.shift();
                stripped++;
            }

            // Skip one leading blank separator row if present
            if (dataRows.length > 0 && dataRows[0].every(c => String(c ?? '').trim() === '')) {
                dataRows.shift();
            }
        }

        // Strip trailing all-blank rows
        while (dataRows.length > 0 &&
               dataRows[dataRows.length - 1].every(c => String(c ?? '').trim() === '')) {
            dataRows.pop();
        }

        if (dataRows.length === 0) {
            return showError(
                'No data rows found. Make sure you filled in the yellow cells in the template before importing.'
            );
        }

        const norm = row => {
            const r = row.map(c => (c === null || c === undefined) ? '' : c);
            while (r.length < numCols) r.push('');
            return r.slice(0, numCols);
        };

        // Build final HOT data: keep HOT's own frozen headers + imported data
        let newData = headerRows > 0
            ? [...currentData.slice(0, headerRows), ...dataRows.map(norm)]
            : dataRows.map(norm);

        const minRows = hot.getSettings().minRows || 17;
        while (newData.length < minRows) newData.push(Array(numCols).fill(''));

        hot.loadData(newData);
        hot.render();
        closeImportModal();
        showToast(`Imported ${dataRows.length} data rows successfully. Review then save.`);
    };

    // ── Helpers ──────────────────────────────────────────────────────────────
    function show(id) {
        const el = $(id);
        if (!el) return;
        // Use appropriate display value
        el.style.display = ['importError','sheetSelectorContainer',
                            'importPreviewContainer'].includes(id) ? 'block' : 'block';
        if (id === 'importError') el.style.display = 'flex';
    }
    function hide(id) {
        const el = $(id); if (!el) return;
        el.style.display = 'none';
    }
    function showError(msg) {
        $('importErrorText').textContent = msg;
        show('importError');
    }
    function hideError() { hide('importError'); }
    function setConfirm(enabled) {
        const btn = $('importConfirmBtn');
        btn.disabled = !enabled;
    }
    function fmtSize(b) {
        if (b < 1024)      return b + ' B';
        if (b < 1048576)   return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(1) + ' MB';
    }

    function showToast(msg) {
        const t = $('importToast');
        $('importToastMsg').textContent = msg;
        t.style.display   = 'flex';
        t.style.opacity   = '0';
        t.style.transform = 'translateY(8px)';
        requestAnimationFrame(() => {
            t.style.opacity   = '1';
            t.style.transform = 'translateY(0)';
        });
        setTimeout(() => {
            t.style.opacity   = '0';
            t.style.transform = 'translateY(8px)';
            setTimeout(() => { t.style.display = 'none'; }, 300);
        }, 3500);
    }

    // Escape key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && $('importModal').style.display !== 'none') {
            closeImportModal();
        }
    });
})();
</script>