<x-app-layout>
    <!-- Handsontable -->
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    <!-- Formula Parser (HyperFormula) -->
    <script src="https://cdn.jsdelivr.net/npm/hyperformula@2.6.2/dist/hyperformula.full.min.js"></script>
    
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
            <!-- Flash Messages Container -->
            <div class="mb-6 space-y-4">
                @if (session('error'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-red-800 mb-1">Error</h3>
                                <p class="text-sm text-red-700 leading-relaxed">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="animate-slideDown">
                        <div class="flex items-start gap-3 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-green-800 mb-1">Success</h3>
                                <p class="text-sm text-green-700 leading-relaxed">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-view-answers-button :submission="$submission" :performanceTask="$performanceTask" :step="$step" />

            <!-- Enhanced Header Container with Card Design -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <!-- Colored Top Bar -->
                    <div class="h-2 bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600"></div>
                    
                    <!-- Header Content -->
                    <div class="p-6 sm:p-8">
                        <!-- Step Indicator and Progress -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                <span>Step 7 of 10</span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-xs">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 70%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">70%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Title Section with Icon -->
                        <div class="flex items-start gap-4 mb-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            
                            <div class="flex-1">
                                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight mb-2">
                                    Financial Statements
                                </h1>
                                <p class="text-base sm:text-lg text-gray-600 leading-relaxed">
                                    Prepare the financial statements following McGraw Hill format: Income Statement, Statement of Owner's Equity, and Balance Sheet.
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

            <!-- Rest of your existing code for tables etc. -->
            <div class="py-4 sm:py-6 lg:py-8">
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

                <!-- Enhanced Header Section -->
                <div class="mb-6 sm:mb-8">
                    <div class="relative">
                        <!-- Step Indicator Badge -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm font-semibold mb-3">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                            <span>Step 7 of 10</span>
                        </div>
                        
                        <!-- Title Section -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 tracking-tight">
                                    Financial Statements
                                </h1>
                                <p class="mt-3 text-sm sm:text-base text-gray-600 leading-relaxed max-w-3xl">
                                    Prepare the financial statements following McGraw Hill format: Income Statement, Statement of Owner's Equity, and Balance Sheet.
                                </p>
                                <div class="mt-2 flex items-center gap-4 text-sm">
                                    <div class="flex items-center gap-2 text-blue-600">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Attempts remaining: <strong>{{ 2 - ($submission->attempts ?? 0) }}/2</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Instructions Section -->
                    <div class="p-4 sm:p-6 bg-blue-50 border-b border-blue-100">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-blue-900 mb-1">Instructions</h3>
                                <p class="text-xs sm:text-sm text-blue-800">
                                    Complete all three financial statements in the McGraw Hill format below. Make sure to fill in all amounts accurately. Your work will be automatically graded against the answer key.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form id="saveForm" method="POST" action="{{ route('students.performance-tasks.save-step', ['id' => $performanceTask->id, 'step' => 7]) }}">
                        @csrf
                        
                        <!-- Spreadsheet Section -->
                        <div class="p-3 sm:p-4 lg:p-6">
                            <div class="border rounded-lg shadow-inner bg-gray-50 overflow-hidden">
                                <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 400px); min-height: 400px;">
                                    <div id="spreadsheet" class="bg-white min-w-full"></div>
                                </div>
                                <input type="hidden" name="submission_data" id="submission_data" required>
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
                                <a href="{{ route('students.performance-tasks.index') }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors text-sm sm:text-base">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Back to Tasks
                                </a>
                                <button type="submit" 
                                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors text-sm sm:text-base disabled:bg-gray-400 disabled:cursor-not-allowed"
                                        {{ ($submission->attempts ?? 0) >= 2 ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ ($submission->attempts ?? 0) >= 2 ? 'Maximum Attempts Reached' : 'Save and Continue' }}
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
        
        // Get saved submission data, answer key, and grading status
        const savedData = @json($submission->submission_data ?? null);
        const correctData = @json($answerSheet->correct_data ?? null);
        const submissionStatus = @json($submission->status ?? null);

        // Parse once for reuse
        const parsedSaved = savedData ? (typeof savedData === 'string' ? JSON.parse(savedData) : savedData) : null;
        const parsedCorrect = correctData ? (typeof correctData === 'string' ? JSON.parse(correctData) : correctData) : null;
        
        // IMPORTANT: Keep the original template separate from saved data
        const templateData = [
            ["Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null, "Durano Enterprise", null, null, null],
            ["Income Statement", null, null, null, "Statement of Changes in Equity", null, null, null, "Balance Sheet", null, null, null],
            ["For the month ended February 29, 2024", null, null, null, "For the month ended February 29, 2024", null, null, null, "As of February 29, 2024", null, null, null],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["Revenues:", "", "", "", "Durano, Capital, beginning", "", "", "", "Assets", "", "", ""],
            ["Service Revenue", "", "", "", "Add: Investment", "", "", "", "Current assets", "", "", ""],
            ["", "", "", "", "          Net Income", "", "", "", "Cash", "", "", ""],
            ["Less: Expenses", "", "", "", "Total", "", "", "", "Accounts receivable", "", "", ""],
            ["Rent expense", "", "", "", "Less: Durano, Withdrawals", "", "", "", "Supplies", "", "", ""],
            ["Utilities expense", "", "", "", "Durano, Capital, ending", "", "", "", "Total current assets", "", "", ""],
            ["Salaries expense", "", "", "", "", "", "", "", "", "", "", ""],
            ["Supplies expense", "", "", "", "", "", "", "", "Non-current assets", "", "", ""],
            ["Depreciation expense", "", "", "", "", "", "", "", "Furniture and fixture", "", "", ""],
            ["Net Income", "", "", "", "", "", "", "", "Accumulated depreciation-furniture and fixture", "", "", ""],
            ["", "", "", "", "", "", "", "", "Equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accumulated depreciation-equipment", "", "", ""],
            ["", "", "", "", "", "", "", "", "Land", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Non-current assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Assets", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "Accounts payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Notes payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Utilities payable", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total liabilities", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            ["", "", "", "", "", "", "", "", "Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "Durano, Capital", "", "", ""],
            ["", "", "", "", "", "", "", "", "Total Liabilities and Owner's Equity", "", "", ""],
            ["", "", "", "", "", "", "", "", "", "", "", ""],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, null, null]
        ];

        // Use saved data if available, otherwise use template
        let initialData, savedMetadata = null;
        
        if (parsedSaved) {
            // Check if saved data has new format with metadata
            if (parsedSaved.data && parsedSaved.metadata) {
                initialData = parsedSaved.data;
                savedMetadata = parsedSaved.metadata;
            } else {
                // Old format - just the data array
                initialData = parsedSaved;
            }
        } else {
            initialData = JSON.parse(JSON.stringify(templateData));
        }

        // Define template cells that should NEVER be graded (headers, labels, etc.)
        const templateCells = new Set([
            // Row 0-2: Headers (all three statements)
            '0-0', '0-1', '0-2', '0-3', '0-4', '0-5', '0-6', '0-7', '0-8', '0-9', '0-10', '0-11',
            '1-0', '1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-7', '1-8', '1-9', '1-10', '1-11',
            '2-0', '2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-7', '2-8', '2-9', '2-10', '2-11',
            // Income Statement labels (column 0)
            '4-0', '5-0', '7-0', '8-0', '9-0', '10-0', '11-0', '12-0', '13-0',
            // Statement of Changes labels (column 4)
            '4-4', '5-4', '6-4', '7-4', '8-4', '9-4',
            // Balance Sheet labels (column 8)
            '4-8', '5-8', '6-8', '7-8', '8-8', '9-8', '11-8', '12-8', '13-8', '14-8', '15-8', '16-8', '17-8', '18-8',
            '20-8', '21-8', '22-8', '23-8', '24-8', '26-8', '27-8', '28-8',
            // Separator columns (3 and 7)
            '3-3', '3-7', '4-3', '4-7', '5-3', '5-7', '6-3', '6-7', '7-3', '7-7', '8-3', '8-7',
            '9-3', '9-7', '10-3', '10-7', '11-3', '11-7', '12-3', '12-7', '13-3', '13-7'
        ]);

        // Function to check if a cell is a template cell
        function isTemplateCell(row, col) {
            return templateCells.has(`${row}-${col}`);
        }

        // Function to add comma separators to numbers
        function addCommas(value) {
            if (!value || value === '') return value;
            
            // If it's a formula, don't format it
            if (typeof value === 'string' && value.startsWith('=')) return value;
            
            // Remove existing formatting
            let cleanValue = String(value).replace(/[₱,\s]/g, '');
            
            // Check if it's a negative number (in parentheses)
            let isNegative = cleanValue.includes('(') || cleanValue.includes(')');
            cleanValue = cleanValue.replace(/[()]/g, '');
            
            // Check if it's a valid number
            if (isNaN(cleanValue) || cleanValue === '') return value;
            
            // Convert to number and back to string to handle decimals properly
            let num = parseFloat(cleanValue);
            if (isNaN(num)) return value;
            
            // Format with commas
            let parts = num.toFixed(2).split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            let formatted = parts.join('.');
            
            // Add back parentheses for negative numbers
            if (isNegative) {
                formatted = '(' + formatted + ')';
            }
            
            return formatted;
        }

        // Initialize HyperFormula
        const hyperformulaInstance = HyperFormula.buildEmpty({
            licenseKey: 'internal-use-in-handsontable',
            ignoreWhiteSpace: 'any',
        });

        // Function to calculate responsive dimensions
        function getResponsiveDimensions() {
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;
            const isMobile = viewportWidth < 640;
            const isTablet = viewportWidth >= 640 && viewportWidth < 1024;

            let tableHeight;
            if (isMobile) {
                tableHeight = Math.min(Math.max(viewportHeight * 0.5, 350), 500);
            } else if (isTablet) {
                tableHeight = Math.min(Math.max(viewportHeight * 0.6, 450), 600);
            } else {
                tableHeight = Math.min(Math.max(viewportHeight * 0.65, 550), 700);
            }

            let colWidths;
            if (isMobile) {
                colWidths = Array(12).fill(100);
            } else if (isTablet) {
                colWidths = [200, 100, 100, 20, 200, 100, 100, 20, 200, 100, 100, 120];
            } else {
                colWidths = [240, 110, 110, 30, 240, 110, 110, 30, 240, 110, 110, 130];
            }

            return { tableHeight, colWidths };
        }

        // Get initial dimensions
        const { tableHeight, colWidths } = getResponsiveDimensions();
            
        hot = new Handsontable(container, {
            data: initialData,
            rowHeaders: true,
            colHeaders: ['', '', '', '', '', '', '', '', '', '', '', ''],
            columns: Array(12).fill(null).map((_, colIndex) => ({
                type: 'text',
                renderer: function(instance, td, row, col, prop, value, cellProperties) {
                    Handsontable.renderers.TextRenderer.apply(this, arguments);
                    
                    // Apply bold formatting if cell has bold-cell class
                    const meta = instance.getCellMeta(row, col);
                    if (meta.className && meta.className.includes('bold-cell')) {
                        td.style.fontWeight = 'bold';
                    }
                    
                    // Formula cells
                    if (value && typeof value === 'string' && value.startsWith('=')) {
                        td.classList.add('formula-cell');
                    }
                    
                    // Apply grading colors ONLY if submission has been graded
                    if (submissionStatus && (submissionStatus === 'correct' || submissionStatus === 'incorrect' || submissionStatus === 'partially_correct') && parsedCorrect && parsedSaved) {
                        // Skip header rows (0-3) and separator columns (3, 7)
                        const isHeaderRow = row <= 3;
                        const isSeparatorCol = col === 3 || col === 7;
                        
                        if (!isHeaderRow && !isSeparatorCol && !isTemplateCell(row, col)) {
                            // Get the actual data (not metadata wrapper)
                            const studentData = parsedSaved.data || parsedSaved;
                            const studentValue = studentData[row]?.[col];
                            const correctValue = parsedCorrect[row]?.[col];
                            const templateValue = templateData[row]?.[col];
                            
                            // Normalize function
                            const normalizeValue = (val) => {
                                if (val === null || val === undefined) return '';
                                if (val === '') return '';
                                if (typeof val === 'string') {
                                    return val.replace(/[₱,\s()]/g, '').trim().toLowerCase();
                                }
                                if (typeof val === 'number') return val.toString();
                                return String(val).trim().toLowerCase();
                            };
                            
                            const normalizedStudent = normalizeValue(studentValue);
                            const normalizedTemplate = normalizeValue(templateValue);
                            
                            // Only grade cells where student entered something different from template
                            if (normalizedStudent !== '' && normalizedStudent !== normalizedTemplate) {
                                const normalizedCorrect = normalizeValue(correctValue);
                                
                                if (normalizedStudent === normalizedCorrect) {
                                    td.classList.add('cell-correct');
                                } else {
                                    td.classList.add('cell-wrong');
                                }
                            }
                        }
                    }
                    
                    // Company name and statement titles (bold, centered)
                    if (row <= 2 && value && value.trim() !== '') {
                        td.style.fontWeight = 'bold';
                        td.style.textAlign = 'center';
                        td.style.fontSize = row === 0 ? '14px' : '13px';
                    }
                    
                    // Section headers - Income Statement
                    if (col <= 3 && value && (value === 'Revenues:' || value === 'Less: Expenses')) {
                        td.style.fontWeight = 'bold';
                    }
                    
                    // Section headers - Balance Sheet
                    if (col >= 8 && value && (
                        value === 'Assets' ||
                        value === 'Liabilities' ||
                        value === "Owner's Equity" ||
                        value === 'Current assets' ||
                        value === 'Non-current assets'
                    )) {
                        td.style.fontWeight = 'bold';
                    }
                    
                    // Total rows styling
                    const firstColValue = instance.getDataAtCell(row, 0);
                    const middleColValue = instance.getDataAtCell(row, 4);
                    const lastColValue = instance.getDataAtCell(row, 8);
                    
                    // Income Statement totals
                    if (col <= 3 && (firstColValue === 'Net Income' || value === 'Net Income')) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Statement of Changes totals
                    if (col >= 4 && col <= 7 && (
                        middleColValue === 'Total' || 
                        middleColValue === 'Durano, Capital, ending' ||
                        value === 'Total' ||
                        value === 'Durano, Capital, ending'
                    )) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Balance Sheet totals
                    if (col >= 8 && (
                        lastColValue === 'Total current assets' ||
                        lastColValue === 'Total Non-current assets' ||
                        lastColValue === 'Total Assets' ||
                        lastColValue === 'Total liabilities' ||
                        lastColValue === "Total Liabilities and Owner's Equity" ||
                        value === 'Total current assets' ||
                        value === 'Total Non-current assets' ||
                        value === 'Total Assets' ||
                        value === 'Total liabilities' ||
                        value === "Total Liabilities and Owner's Equity"
                    )) {
                        td.style.fontWeight = 'bold';
                        td.style.borderTop = '1px solid #000';
                    }
                    
                    // Double underline for final totals
                    if (col >= 4 && col <= 7 && middleColValue === 'Durano, Capital, ending') {
                        td.style.borderBottom = '3px double #000';
                    }
                    
                    if (col >= 8 && (
                        lastColValue === 'Total Assets' ||
                        lastColValue === "Total Liabilities and Owner's Equity"
                    )) {
                        td.style.borderBottom = '3px double #000';
                    }
                    
                    // Separator columns
                    if (col === 3 || col === 7) {
                        td.style.backgroundColor = '#f8f9fa';
                        td.style.borderRight = '2px solid #dee2e6';
                        td.style.width = '30px';
                    }
                    
                    // Right align numbers
                    if ((col === 1 || col === 2 || col === 5 || col === 6 || col === 9 || col === 10 || col === 11) && 
                        value && (value.includes('₱') || value.includes(',') || !isNaN(value.replace(/[₱,()]/g, '')))) {
                        td.style.textAlign = 'right';
                    }
                }
            })),
            width: '100%',
            height: tableHeight,
            colWidths: colWidths,
            licenseKey: 'non-commercial-and-evaluation',
            formulas: { engine: hyperformulaInstance },
            beforeChange: function(changes, source) {
                if (changes) {
                    changes.forEach(function(change) {
                        const row = change[0];
                        const col = change[1];
                        let newValue = change[3];
                        
                        // Don't format formulas
                        if (newValue && typeof newValue === 'string' && newValue.startsWith('=')) {
                            change[3] = newValue.trim();
                            return;
                        }
                        
                        // Auto-format numbers in value columns (1, 2, 5, 6, 9, 10, 11)
                        if (col === 1 || col === 2 || col === 5 || col === 6 || col === 9 || col === 10 || col === 11) {
                            if (newValue && newValue !== '') {
                                change[3] = addCommas(newValue);
                            }
                        }
                    });
                }
            },
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
            fillHandle: true,
            autoColumnSize: false,
            autoRowSize: false,
            copyPaste: true,
            minRows: 35,
            minCols: 12,
            minSpareRows: 1,
            stretchH: 'none',
            enterMoves: { row: 1, col: 0 },
            tabMoves: { row: 0, col: 1 },
            outsideClickDeselects: false,
            selectionMode: 'multiple',
            comments: true,
            customBorders: true,
            className: 'htLeft htMiddle',
            mergeCells: [
                // Income Statement header merges
                { row: 0, col: 0, rowspan: 1, colspan: 4 },
                { row: 1, col: 0, rowspan: 1, colspan: 4 },
                { row: 2, col: 0, rowspan: 1, colspan: 4 },
                // Statement of Changes in Equity header merges
                { row: 0, col: 4, rowspan: 1, colspan: 4 },
                { row: 1, col: 4, rowspan: 1, colspan: 4 },
                { row: 2, col: 4, rowspan: 1, colspan: 4 },
                // Balance Sheet header merges
                { row: 0, col: 8, rowspan: 1, colspan: 4 },
                { row: 1, col: 8, rowspan: 1, colspan: 4 },
                { row: 2, col: 8, rowspan: 1, colspan: 4 }
            ]
        });

        // Restore bold formatting if metadata exists
        if (savedMetadata) {
            savedMetadata.forEach((row, rowIndex) => {
                if (row && Array.isArray(row)) {
                    row.forEach((cell, colIndex) => {
                        if (cell && cell.bold) {
                            // Get current className and add bold-cell if not already present
                            const currentMeta = hot.getCellMeta(rowIndex, colIndex);
                            const currentClassName = currentMeta.className || '';
                            
                            if (!currentClassName.includes('bold-cell')) {
                                const newClassName = currentClassName 
                                    ? currentClassName + ' bold-cell' 
                                    : 'bold-cell';
                                hot.setCellMeta(rowIndex, colIndex, 'className', newClassName);
                            }
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

        // Improved window resize and zoom handler
        let resizeTimer;
        const handleResize = function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const newDimensions = getResponsiveDimensions();
                
                hot.updateSettings({
                    height: newDimensions.tableHeight,
                    colWidths: newDimensions.colWidths,
                    width: '100%'
                });
                
                // Force complete re-render
                hot.render();
            }, 250);
        };

        // Listen to both resize and zoom events
        window.addEventListener('resize', handleResize);
        
        // Detect zoom changes (works in most browsers)
        let lastWidth = window.innerWidth;
        let lastHeight = window.innerHeight;
        
        const checkZoom = function() {
            const currentWidth = window.innerWidth;
            const currentHeight = window.innerHeight;
            
            // If dimensions changed, trigger resize handler
            if (currentWidth !== lastWidth || currentHeight !== lastHeight) {
                lastWidth = currentWidth;
                lastHeight = currentHeight;
                handleResize();
            }
        };
        
        // Check for zoom changes periodically
        setInterval(checkZoom, 500);

        // Capture spreadsheet data on submit with bold metadata
            // Capture spreadsheet data on submit with bold metadata
const saveForm = document.getElementById("saveForm");
if (saveForm) {
    saveForm.addEventListener("submit", function (e) {
        e.preventDefault();
        
        const data = hot.getData();
        const metadata = [];
        
        // Capture bold formatting
        for (let row = 0; row < data.length; row++) {
            metadata[row] = [];
            for (let col = 0; col < data[row].length; col++) {
                const meta = hot.getCellMeta(row, col);
                metadata[row][col] = { 
                    bold: !!(meta.className && meta.className.includes('bold-cell'))
                };
            }
        }
        
        // Save both data and metadata
        document.getElementById("submission_data").value = JSON.stringify({
            data: data,
            metadata: metadata
        });
        
        this.submit();
    });
}
    });
</script>

<style>
    body { overflow-x: hidden; }
    .handsontable .font-bold { font-weight: bold; }
    .handsontable .bg-gray-100 { background-color: #f3f4f6 !important; }
    .handsontable .bg-blue-50 { background-color: #eff6ff !important; }
    .handsontable td { border-color: #d1d5db; }
    .handsontable .area { background-color: rgba(59, 130, 246, 0.1); }
    .handsontable { position: relative; z-index: 1; }
    #spreadsheet { isolation: isolate; }
    .overflow-x-auto { -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
    .handsontable .htMerge { background-color: #f8fafc; }

    /* Formula cell indicator */
    .handsontable td.formula-cell {
        font-style: italic;
        background-color: #f0f9ff !important;
        border-left: 3px solid #3b82f6 !important;
    }

    /* Formula cell selected states */
    .handsontable td.formula-cell.area,
    .handsontable td.formula-cell.current {
        background-color: #dbeafe !important;
    }

    /* McGraw Hill style formatting */
    .handsontable th {
        background-color: #e5e7eb;
        font-weight: 600;
    }

    .handsontable td.bold-cell {
    font-weight: bold !important;
    }

    .handsontable .total-row {
        background-color: #f3f4f6;
        font-weight: bold;
    }

    /* Grading colors (copied from step 6) */
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

    /* Table separation styling */
    .handsontable .table-divider {
        border-bottom: 3px solid #3b82f6;
    }

    @media (max-width: 640px) {
        .handsontable { font-size: 11px; }
        .handsontable th, .handsontable td { padding: 3px; }
    }

    @media (min-width: 640px) and (max-width: 1024px) {
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
        </div>
    </div>
</x-app-layout>