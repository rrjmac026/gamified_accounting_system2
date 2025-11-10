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
                    <div class="animate-slideDown">
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
            </div>

            <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

            <!-- Enhanced Header Container with Card Design -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <!-- Colored Top Bar -->
                    <div class="h-2 bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-700 rounded-full text-sm font-semibold border border-cyan-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 4 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-full rounded-full transition-all duration-500" style="width: 40%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">40%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Trial Balance
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    List all accounts and their debit or credit balances to ensure totals are equal.
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
                                    <p class="text-lg font-bold text-amber-900">{{ 2 - ($submission->attempts ?? 0) }}/2</p>
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
                <div class="p-4 sm:p-6 bg-gradient-to-r from-cyan-50 to-blue-50 border-b border-cyan-100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-cyan-900 mb-1">Instructions</h3>
                            <div class="text-sm text-cyan-800 leading-relaxed">
                                {!! $performanceTask->description ?? 'Review your ledger balances before continuing.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 4]) }}">
                    @csrf
                    <div class="p-3 sm:p-4 lg:p-6">
                        <div class="border-2 border-gray-300 rounded-xl shadow-inner bg-gray-50 overflow-hidden">
                            <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                                <div id="spreadsheet" class="bg-white min-w-full"></div>
                            </div>
                            <input type="hidden" name="submission_data" id="submission_data" required>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-gray-50 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                            <button type="button" onclick="window.history.back()"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white text-gray-700 border-2 border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all text-sm font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-cyan-600 to-blue-600 text-white rounded-lg hover:from-cyan-700 hover:to-blue-700 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition-all text-sm font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
    </div>


<script>
    let hot;
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('spreadsheet');
        const savedDataRaw = @json($submission->submission_data ?? null);
        const correctDataRaw = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);

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
                ['Durano Enterprise', '', ''],
                ['Trial Balance', '', ''],
                ['Date: ____________', '', ''],
                ['Account Title', 'Debit (₱)', 'Credit (₱)'],
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
                ['', '', ''],
                ['Total', '', '']
            ];
        }

        // Parse correct data if it exists
        let correctData = null, correctMetadata = null;
        
        if (correctDataRaw) {
            const parsedCorrect = typeof correctDataRaw === 'string' ? JSON.parse(correctDataRaw) : correctDataRaw;
            if (parsedCorrect && parsedCorrect.data && parsedCorrect.metadata) {
                correctData = parsedCorrect.data;
                correctMetadata = parsedCorrect.metadata;
            } else if (parsedCorrect) {
                correctData = parsedCorrect;
            }
        }

        // Initialize HyperFormula with whitespace support
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any', // Allows spaces in formulas
        });

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
            copyPaste: true,
            minSpareRows: 0,
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: true,
            comments: true,
            customBorders: true,
            className: 'htCenter htMiddle',
            
            cells: function(row, col) {
                const cellProperties = {};
                const data = this.instance.getData();
                const lastRow = data.length - 1;
                
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
                
                // All other cells - support bold and grading
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
                        
                        // Check if submission is graded and apply correct/wrong styling
                        if (submissionStatus && correctData && row > 3 && row !== lastRow) {
                            const studentValue = instance.getDataAtCell(row, col);
                            const correctValue = correctData[row]?.[col];
                            
                            if (studentValue !== null && studentValue !== undefined && studentValue !== '') {
                                const normalizeValue = (val) => {
                                    if (val === null || val === undefined || val === '') return '';
                                    if (typeof val === 'string') return val.trim().toLowerCase();
                                    if (typeof val === 'number') return val.toFixed(2);
                                    return String(val);
                                };
                                
                                const normalizedStudent = normalizeValue(studentValue);
                                const normalizedCorrect = normalizeValue(correctValue);
                                
                                if (normalizedStudent === normalizedCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }
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

        // Handle form submission with bold metadata
        const form = document.getElementById('saveForm');
        form.addEventListener('submit', function (e) {
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
            document.getElementById('submission_data').value = JSON.stringify({
                data: data,
                metadata: metadata
            });
            
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
    .handsontable .area { background-color: rgba(6, 182, 212, 0.1); }
    #spreadsheet { isolation: isolate; }
    
    .handsontable td.total-row {
        background-color: #f3f4f6 !important;
        font-weight: 700;
    }

    .handsontable td.bold-cell {
            font-weight: bold !important;
        }

    .handsontable td.total-cell-bold {
        border-top: 3px solid #374151 !important;
        border-bottom: 3px double #374151 !important;
    }
    
    .handsontable td.cell-correct {
        background-color: #dcfce7 !important;
        border: 2px solid #16a34a !important;
        color: #166534;
    }
    
    .handsontable td.cell-wrong {
        background-color: #fee2e2 !important;
        border: 2px solid #dc2626 !important;
        color: #991b1b;
    }
    
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