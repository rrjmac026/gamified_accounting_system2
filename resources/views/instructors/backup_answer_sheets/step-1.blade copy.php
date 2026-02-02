<x-app-layout>
    <!-- Luckysheet CSS -->
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/css/pluginsCss.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/plugins.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/css/luckysheet.css' />
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/assets/iconfont/iconfont.css' />
    
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

        /* Luckysheet Container */
        #luckysheet {
            margin: 0px;
            padding: 0px;
            position: relative;
            width: 100%;
            height: 600px;
        }

        /* Prevent auto-scroll on any interaction */
        #luckysheet *,
        #luckysheet *:focus,
        #luckysheet a,
        #luckysheet a:focus {
            scroll-margin: 0 !important;
            scroll-behavior: auto !important;
        }

        /* Keep page scroll position stable */
        html {
            scroll-behavior: auto !important;
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

            #luckysheet {
                height: 450px;
            }
        }

        @media (min-width: 640px) and (max-width: 1024px) {
            .header-title {
                font-size: 2rem;
            }

            #luckysheet {
                height: 550px;
            }
        }

        @media (min-width: 1024px) {
            .header-title {
                font-size: 2.5rem;
            }
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
                <span>Answer Key - Step 1 of 10</span>
            </div>
            
            <h1 class="header-title">
                Answer Key: Journal Entries
            </h1>
            
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
                        Fill in the correct answers below. Students' submissions will be compared against this answer key for grading. Empty cells will be ignored during comparison. You can use formulas just like in Excel!
                    </p>
                </div>
            </div>

            <form id="answerKeyForm" action="{{ route('instructors.performance-tasks.answer-sheets.update', ['task' => $task, 'step' => 1]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Spreadsheet Section -->
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                        <div id="luckysheet"></div>
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

    <!-- Luckysheet JS -->
    <script src="https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/plugins/js/plugin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luckysheet@latest/dist/luckysheet.umd.js"></script>

<script>
        // Prevent page scroll jumps - must be at the very top
        (function() {
            let scrollPosition = 0;
            
            // Store scroll position before any interaction
            window.addEventListener('scroll', function() {
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            }, { passive: true });

            // Prevent hash changes from scrolling
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            // Override problematic scroll methods
            const originalScrollTo = window.scrollTo;
            const originalScrollBy = window.scrollBy;
            
            window.scrollTo = function(x, y) {
                // Prevent scrolling to top (0,0)
                if (typeof x === 'object') {
                    if (x.top === 0 && x.left === 0) return;
                } else if (x === 0 && y === 0) {
                    return;
                }
                originalScrollTo.apply(window, arguments);
            };

            window.scrollBy = function(x, y) {
                if (x === 0 && y === 0) return;
                originalScrollBy.apply(window, arguments);
            };
        })();

        document.addEventListener("DOMContentLoaded", function () {
            // Get saved answer key data if it exists
            const savedData = @json($sheet->correct_data ?? null);
            
            // Initialize cell data
            let celldata = [];
            
            if (savedData) {
                const parsedData = JSON.parse(savedData);
                parsedData.forEach((row, rowIndex) => {
                    row.forEach((cellValue, colIndex) => {
                        if (cellValue !== null && cellValue !== '') {
                            celldata.push({
                                r: rowIndex,
                                c: colIndex,
                                v: {
                                    v: cellValue,
                                    m: cellValue,
                                    ct: { fa: "General", t: "g" }
                                }
                            });
                        }
                    });
                });
            }

            // Configure Luckysheet
            const options = {
                container: 'luckysheet',
                lang: 'en',
                showtoolbar: true,
                showinfobar: false,
                showsheetbar: false,
                showstatisticBar: false,
                sheetFormulaBar: true,
                enableAddRow: true,
                enableAddCol: true,
                userInfo: false,
                showConfigWindowResize: true,
                forceCalculation: false,
                plugins: ['chart'],
                data: [{
                    name: "Answer Key",
                    color: "",
                    status: "1",
                    order: "0",
                    hide: 0,
                    row: 30,
                    column: 15,
                    defaultRowHeight: 19,
                    defaultColWidth: 73,
                    celldata: celldata,
                    config: {
                        merge: {},
                        rowlen: {},
                        columnlen: {},
                        rowhidden: {},
                        colhidden: {},
                        borderInfo: [],
                        authority: {}
                    },
                    index: 0,
                    jfgird_select_save: [],
                    luckysheet_select_save: [{
                        row: [0, 0],
                        column: [0, 0],
                        row_focus: 0,
                        column_focus: 0,
                        left: 0,
                        width: 73,
                        top: 0,
                        height: 19,
                        left_move: 0,
                        width_move: 73,
                        top_move: 0,
                        height_move: 19
                    }],
                    calcChain: [],
                    isPivotTable: false,
                    pivotTable: {},
                    filter_select: {},
                    filter: null,
                    luckysheet_alternateformat_save: [],
                    luckysheet_alternateformat_save_modelCustom: [],
                    luckysheet_conditionformat_save: {},
                    frozen: {},
                    chart: [],
                    zoomRatio: 1,
                    image: [],
                    showGridLines: 1,
                    dataVerification: {}
                }],
                title: 'Answer Key - Journal Entries',
                myFolderUrl: '',
                devicePixelRatio: 1,
                allowEdit: true,
                loadUrl: '',
                loadSheetUrl: '',
                gridKey: '',
                updateUrl: '',
                updateImageUrl: '',
                allowUpdate: false,
                functionButton: '',
                showConfigWindowResize: true,
                hook: {
                    cellUpdated: function(r, c, oldValue, newValue, isRefresh) {
                        // Optional: Add any custom logic when a cell is updated
                        console.log('Cell updated:', r, c, newValue);
                    }
                }
            };

            // Initialize Luckysheet
            luckysheet.create(options);

            // Additional scroll prevention after Luckysheet loads
            setTimeout(function() {
                const luckyContainer = document.getElementById('luckysheet');
                if (luckyContainer) {
                    // Capture and stop all click events that might cause scrolling
                    luckyContainer.addEventListener('mousedown', function(e) {
                        // Prevent default only for links and anchors
                        const target = e.target;
                        if (target.tagName === 'A' || target.closest('a')) {
                            e.preventDefault();
                        }
                    }, true);

                    // Prevent focus events from scrolling
                    luckyContainer.addEventListener('focusin', function(e) {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                        
                        // Restore scroll position if it changed
                        requestAnimationFrame(function() {
                            window.scrollTo(scrollLeft, scrollTop);
                        });
                    }, true);

                    // Block hash navigation
                    window.addEventListener('hashchange', function(e) {
                        e.preventDefault();
                        return false;
                    }, false);
                }
            }, 100);

            // Handle form submission
            const answerKeyForm = document.getElementById("answerKeyForm");
            if (answerKeyForm) {
                answerKeyForm.addEventListener("submit", function (e) {
                    e.preventDefault();
                    
                    // Get all sheet data with complete information
                    const sheetData = luckysheet.getAllSheets()[0];
                    const cellData = sheetData.celldata || [];
                    const config = sheetData.config || {};
                    const mergeInfo = config.merge || {};
                    
                    // Convert celldata to 2D array format (15x15 grid)
                    const gridData = Array(15).fill().map(() => Array(15).fill(''));
                    
                    // Create a set to track cells that are part of a merge (but not the main cell)
                    const mergedCells = new Set();
                    
                    // First pass: identify all merged cell ranges
                    Object.keys(mergeInfo).forEach(key => {
                        const merge = mergeInfo[key];
                        if (merge && merge.r !== undefined && merge.c !== undefined) {
                            const mainRow = merge.r;
                            const mainCol = merge.c;
                            const rowSpan = merge.rs || 1;
                            const colSpan = merge.cs || 1;
                            
                            // Mark all cells except the main cell as merged
                            for (let r = mainRow; r < mainRow + rowSpan && r < 15; r++) {
                                for (let c = mainCol; c < mainCol + colSpan && c < 15; c++) {
                                    // Skip the main cell itself
                                    if (r !== mainRow || c !== mainCol) {
                                        mergedCells.add(`${r},${c}`);
                                    }
                                }
                            }
                        }
                    });
                    
                    // Second pass: populate grid with cell data
                    cellData.forEach(cell => {
                        if (cell.r < 15 && cell.c < 15 && cell.v) {
                            // Only set value if this is not a merged sub-cell
                            if (!mergedCells.has(`${cell.r},${cell.c}`)) {
                                // Get the actual value (formula result if formula, or raw value)
                                const value = cell.v.v || cell.v.m || '';
                                gridData[cell.r][cell.c] = value;
                            }
                        }
                    });
                    
                    // Store the data
                    document.getElementById("correctData").value = JSON.stringify(gridData);
                    
                    console.log('Submitting grid data:', gridData); // Debug log
                    console.log('Merge info:', mergeInfo); // Debug log
                    console.log('Merged cells:', Array.from(mergedCells)); // Debug log
                    
                    // Submit the form
                    this.submit();
                });
            }
        });
    </script>
</x-app-layout>