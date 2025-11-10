<x-app-layout>
    <!-- Handsontable + HyperFormula -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
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

        /* Instructions Box Enhancement */
        .instructions-box {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 1px solid #e9d5ff;
            border-left: 4px solid #8b5cf6;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(139, 92, 246, 0.08);
        }

        .instructions-box h3 {
            color: #581c87;
            font-size: 0.9375rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions-box p {
            color: #6b21a8;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .instructions-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #8b5cf6;
            flex-shrink: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .answer-key-header {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.75rem;
            }

            .header-description {
                font-size: 0.875rem;
            }

            .step-badge {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .header-title {
                font-size: 2rem;
            }
        }

        @media (min-width: 1024px) {
            .header-title {
                font-size: 2.5rem;
            }
        }

        body { overflow-x: hidden; }
        .handsontable td { border-color: #d1d5db; }
        .handsontable .area { background-color: rgba(147, 51, 234, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
        
        /* Header rows styling */
        .handsontable td.header-company,
        .handsontable td.header-title,
        .handsontable td.header-date {
            background-color: white !important;
            font-size: 14px;
            padding: 8px;
        }

        .handsontable td.bold-cell {
            font-weight: bold !important;
        }
        
        .handsontable td.header-columns {
            background-color: #f3f4f6 !important;
            font-weight: 700;
            border-bottom: 2px solid #374151 !important;
        }
        
        /* Total row styling */
        .handsontable td.total-row {
            background-color: #f3f4f6 !important;
            font-weight: 700;
        }

        .handsontable td.total-cell-bold {
            border-top: 3px solid #374151 !important;
            border-bottom: 3px double #374151 !important;
        }
        
        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }
        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
    </style>

    <div class="py-4 sm:py-6 lg:py-8">
        {{-- Flash/Error/Success Messages --}}
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
                <span>Answer Key - Step 4 of 10</span>
            </div>
            
            <h1 class="header-title">
                Answer Key: Trial Balance
            </h1>
            
            <p class="header-description">
                Create the correct answer key for Trial Balance. This will be used to automatically grade student submissions.
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

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 4]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
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
        const savedDataRaw = @json($sheet->correct_data ?? null);
        
        // Parse saved data if it exists
        let initialData, savedMetadata = null;
        
        if (savedDataRaw) {
            const parsedSaved = typeof savedDataRaw === 'string' ? JSON.parse(savedDataRaw) : savedDataRaw;
            if (parsedSaved && parsedSaved.data && parsedSaved.metadata) {
                initialData = parsedSaved.data;
                savedMetadata = parsedSaved.metadata;
            } else if (parsedSaved) {
                // Old format - just the data array
                initialData = parsedSaved;
            }
        }
        
        if (!initialData) {
            // Load default data with header rows
            initialData = [
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
        }

        // Initialize HyperFormula for Excel-like formulas with whitespace support
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any', // Allows spaces in formulas
        });

        // Determine responsive dimensions
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;

        // Initialize Handsontable
        hot = new Handsontable(container, {
            data: initialData,
            columns: [
                { type: 'text' },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' } },
            ],
            rowHeaders: true,
            width: '100%',
            height: isMobile ? 350 : (isTablet ? 450 : 500),
            licenseKey: 'non-commercial-and-evaluation',
            stretchH: 'all',
            className: 'htCenter htMiddle',

            // Formula support with whitespace handling
            formulas: { engine: hyperformulaInstance },

            // Handle formula input with whitespace
            beforeChange: function(changes, source) {
                if (changes) {
                    changes.forEach(function(change) {
                        // change[3] is the new value
                        if (change[3] && typeof change[3] === 'string' && change[3].startsWith('=')) {
                            // Trim leading/trailing spaces but keep internal spaces
                            change[3] = change[3].trim();
                        }
                    });
                }
            },

            // Context menu with bold toggle
            contextMenu: {
                items: {
                    'row_above': {},
                    'row_below': {},
                    'col_left': {},
                    'col_right': {},
                    'remove_row': {},
                    'remove_col': {},
                    'undo': {},
                    'redo': {},
                    'make_read_only': {},
                    'alignment': {},
                    'separator1': '---------',
                    'bold': {
                        name: '✓ Toggle Bold',
                        callback: function() {
                            const selected = this.getSelected();
                            if (selected) {
                                selected.forEach(([startRow, startCol, endRow, endCol]) => {
                                    for (let row = startRow; row <= endRow; row++) {
                                        for (let col = startCol; col <= endCol; col++) {
                                            const meta = this.getCellMeta(row, col);
                                            
                                            // Toggle bold state
                                            if (!meta.className) {
                                                this.setCellMeta(row, col, 'className', 'bold-cell');
                                            } else if (meta.className.includes('bold-cell')) {
                                                this.setCellMeta(row, col, 'className', 
                                                    meta.className.replace('bold-cell', '').trim());
                                            } else {
                                                this.setCellMeta(row, col, 'className', 
                                                    meta.className + ' bold-cell');
                                            }
                                        }
                                    }
                                });
                                this.render();
                            }
                        }
                    }
                }
            },
            undo: true,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: true,
            manualRowMove: true,
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 16,
            minCols: 3,
            stretchH: 'all',
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: true,
            comments: true,
            customBorders: true,
            minSpareRows: 0,
            
            // Custom cell styling
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
                else if (row === 1) {
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
                else if (row === 2) {
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
                else if (row === 3) {
                    cellProperties.readOnly = true;
                    cellProperties.className = 'header-columns';
                    cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                        Handsontable.renderers.TextRenderer.apply(this, arguments);
                        td.innerHTML = '<strong>' + value + '</strong>';
                        td.style.textAlign = 'center';
                        td.style.backgroundColor = '#f3f4f6';
                    };
                }
                
                // Last row: Total row
                else if (row === lastRow) {
                    cellProperties.className = 'total-row';
                    if (col === 0) {
                        cellProperties.readOnly = true;
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                            td.innerHTML = '<strong>Total</strong>';
                        };
                    } else {
                        cellProperties.className = 'total-cell-bold';
                        cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                            Handsontable.renderers.NumericRenderer.apply(this, arguments);
                            if (td.innerHTML) {
                                td.innerHTML = '<strong>' + td.innerHTML + '</strong>';
                            }
                        };
                    }
                }
                
                // All other cells - support bold formatting
                else {
                    cellProperties.renderer = function(instance, td, row, col, prop, value, cellProperties) {
                        // Use appropriate base renderer
                        if (col === 0) {
                            Handsontable.renderers.TextRenderer.apply(this, arguments);
                        } else {
                            Handsontable.renderers.NumericRenderer.apply(this, arguments);
                        }
                        
                        // Check if cell should be bold
                        const meta = instance.getCellMeta(row, col);
                        if (meta.className && meta.className.includes('bold-cell')) {
                            td.style.fontWeight = 'bold';
                        }
                        
                        // Formula cell styling
                        if (value && typeof value === 'string' && value.startsWith('=')) {
                            td.classList.add('formula-cell');
                        }
                    };
                }
                
                return cellProperties;
            }
        });

        // Restore bold formatting if metadata exists
        if (savedMetadata) {
            savedMetadata.forEach((row, rowIndex) => {
                if (row) {
                    row.forEach((cell, colIndex) => {
                        if (cell && cell.bold) {
                            hot.setCellMeta(rowIndex, colIndex, 'className', 'bold-cell');
                        }
                    });
                }
            });
            hot.render();
        }

        // Keyboard shortcut for bold (Ctrl+B / Cmd+B)
        hot.addHook('beforeKeyDown', function(event) {
            // Check for Ctrl+B (Windows/Linux) or Cmd+B (Mac)
            if ((event.ctrlKey || event.metaKey) && event.key === 'b') {
                event.preventDefault();
                event.stopImmediatePropagation();
                
                const selected = hot.getSelected();
                if (selected) {
                    selected.forEach(([startRow, startCol, endRow, endCol]) => {
                        for (let row = startRow; row <= endRow; row++) {
                            for (let col = startCol; col <= endCol; col++) {
                                const meta = hot.getCellMeta(row, col);
                                
                                // Toggle bold
                                if (!meta.className) {
                                    hot.setCellMeta(row, col, 'className', 'bold-cell');
                                } else if (meta.className.includes('bold-cell')) {
                                    hot.setCellMeta(row, col, 'className', 
                                        meta.className.replace('bold-cell', '').trim());
                                } else {
                                    hot.setCellMeta(row, col, 'className', 
                                        meta.className + ' bold-cell');
                                }
                            }
                        }
                    });
                    hot.render();
                }
            }
        });

        // Responsive resize handler
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const newIsMobile = window.innerWidth < 640;
                const newIsTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
                const newHeight = newIsMobile ? 350 : (newIsTablet ? 450 : 500);
                
                hot.updateSettings({
                    height: newHeight
                });
            }, 250);
        });

        // Handle form submission with bold metadata
        const answerKeyForm = document.getElementById("answerKeyForm");
        if (answerKeyForm) {
            answerKeyForm.addEventListener("submit", function (e) {
                e.preventDefault();
                
                const data = hot.getData();
                const metadata = [];
                
                // Capture bold formatting
                for (let row = 0; row < data.length; row++) {
                    metadata[row] = [];
                    for (let col = 0; col < data[row].length; col++) {
                        const meta = hot.getCellMeta(row, col);
                        if (meta.className && meta.className.includes('bold-cell')) {
                            metadata[row][col] = { bold: true };
                        }
                    }
                }
                
                // Save data with metadata
                document.getElementById("correctData").value = JSON.stringify({
                    data: data,
                    metadata: metadata
                });
                
                this.submit();
            });
        }
    });
</script>
</x-app-layout>