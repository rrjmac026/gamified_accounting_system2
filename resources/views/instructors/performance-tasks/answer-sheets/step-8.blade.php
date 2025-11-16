<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
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

        body { overflow-x: hidden; }
        .handsontable td { 
            border-color: #d1d5db;
        }
        .handsontable .area { background-color: rgba(147, 51, 234, 0.1); }
        .handsontable { position: relative; z-index: 1; }
        #spreadsheet { isolation: isolate; }
        .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }

        @media (max-width: 640px) {
            .handsontable { font-size: 12px; }
            .handsontable th, .handsontable td { padding: 4px; }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .handsontable { font-size: 13px; }
        }
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
            
            <h1 class="header-title">
                Answer Key: Balance Sheet
            </h1>
            
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
                    <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
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

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 8]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Spreadsheet Section -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                            <div id="spreadsheet" class="bg-white min-w-full"></div>
                        </div>
                        <input type="hidden" name="correct_data" id="correctData" required>
                    </div>

                    <!-- Mobile Scroll Hint -->
                    <div class="mt-2 text-xs text-gray-500 sm:hidden text-center">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Swipe to scroll spreadsheet
                    </div>
                </div>

                <!-- Action Buttons -->
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
        
        // Get saved answer key data if it exists
        const savedData = @json($sheet->correct_data ?? null);
        // Changed to 20 columns to match Step 5 structure
        const initialData = savedData ? JSON.parse(savedData) : Array.from({ length: 15 }, () => Array(20).fill(''));

        // Initialize HyperFormula with whitespace support
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any', // Allows spaces in formulas
        });

        // Determine responsive dimensions
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        
        hot = new Handsontable(container, {
            data: initialData,
            rowHeaders: true,
            width: '100%',
            height: isMobile ? 350 : (isTablet ? 450 : 500),
            licenseKey: 'non-commercial-and-evaluation',

            // Using nested headers like Step 5
            nestedHeaders: [
                [
                    {label: 'Date', colspan: 2}, // Date spans 2 columns
                    'Account Titles and Explanation', 
                    'Account Number', 
                    'Debit (₱)', 
                    'Credit (₱)',
                ],
                [
                    '', // Sub-column 1 under Date
                    '', // Sub-column 2 under Date
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
                ]
            ],

            columns: [
                { type: 'text', width: isMobile ? 80 : 100 }, // Month
                { type: 'text', width: isMobile ? 80 : 100 }, // Day
                { type: 'text', width: isMobile ? 300 : 400 },
                { type: 'text', width: isMobile ? 80 : 100 },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: isMobile ? 120 : 150 },
                { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: isMobile ? 120 : 150 },
            ],

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

            // Optional: Add visual indicator for formula cells
            cells: function(row, col) {
                const cellProperties = {};
                const cellData = this.instance.getDataAtCell(row, col);
                
                if (cellData && typeof cellData === 'string' && cellData.startsWith('=')) {
                    cellProperties.className = 'formula-cell';
                }
                
                return cellProperties;
            },

            // Full feature set
            stretchH: 'none',
            contextMenu: true,
            undo: true,
            manualColumnResize: true,
            manualRowResize: true,
            manualColumnMove: true,
            manualRowMove: true,
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 15,
            minCols: 21,
            minSpareRows: 1,
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            mergeCells: true,
            comments: true,
            customBorders: true,

            afterRenderer: function (TD, row, col, prop, value, cellProperties) {
                // Make the border after Credit column (now index 5) bold
                if (col === 5) {
                    TD.style.borderRight = '3px solid #000000';
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
                    height: newHeight,
                    columns: [
                        { type: 'text', width: newIsMobile ? 80 : 100 },
                        { type: 'text', width: newIsMobile ? 80 : 100 },
                        { type: 'text', width: newIsMobile ? 300 : 400 },
                        { type: 'text', width: newIsMobile ? 80 : 100 },
                        { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: newIsMobile ? 120 : 150 },
                        { type: 'numeric', numericFormat: { pattern: '₱0,0.00' }, width: newIsMobile ? 120 : 150 },
                    ]
                });
            }, 250);
        });

        // Capture spreadsheet data on submit
        const answerKeyForm = document.getElementById("answerKeyForm");
        if (answerKeyForm) {
            answerKeyForm.addEventListener("submit", function (e) {
                e.preventDefault();
                const data = hot.getData();
                document.getElementById("correctData").value = JSON.stringify(data);
                this.submit();
            });
        }
    });
</script>
</x-app-layout>