<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <div class="flex justify-between items-center px-4 sm:px-8 mt-4">
        <a href="{{ route('instructors.performance-tasks.exercises.show', $task->id) }}"
           class="inline-flex items-center text-[#D5006D] hover:text-[#FF6F91] font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back to Exercises
        </a>
    </div>

    <div class="py-6 sm:py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] shadow-lg rounded-2xl p-6 sm:p-8 border border-[#FFC8FB]/50">

                {{-- Header --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 text-xs font-bold bg-[#D5006D] text-white rounded-full">
                            Step {{ $step }}
                        </span>
                        <h2 class="text-xl font-bold text-[#D5006D]">{{ $stepTitle }}</h2>
                    </div>
                    <p class="text-sm text-gray-500">
                        Task: <span class="font-medium text-gray-700">{{ $task->title }}</span>
                    </p>
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ isset($exercise)
                          ? route('instructors.performance-tasks.exercises.update', [$task, $exercise])
                          : route('instructors.performance-tasks.exercises.store', [$task, $step]) }}"
                      id="exerciseForm">
                    @csrf
                    @if(isset($exercise)) @method('PUT') @endif

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-[#D5006D] mb-1">
                            Exercise Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $exercise->title ?? 'Exercise ' . $nextNumber) }}"
                               placeholder="e.g. Exercise 1 — Basic Transactions"
                               class="w-full rounded-lg border border-[#FFC8FB] bg-white px-4 py-2 text-gray-800 text-sm focus:border-pink-400 focus:ring focus:ring-pink-200"
                               required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-[#D5006D] mb-1">
                            Description <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea name="description" rows="2"
                                  placeholder="Additional instructions or context for this exercise..."
                                  class="w-full rounded-lg border border-[#FFC8FB] bg-white px-4 py-2 text-gray-800 text-sm focus:border-pink-400 focus:ring focus:ring-pink-200">{{ old('description', $exercise->description ?? '') }}</textarea>
                    </div>

                    {{-- Spreadsheet --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-[#D5006D]">
                                Answer Sheet (Correct Data) <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <button type="button" onclick="addRow()"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-[#D5006D] hover:bg-[#FF6F91] rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Add Row
                                </button>
                                <button type="button" onclick="removeLastRow()"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-gray-400 hover:bg-gray-500 rounded-lg transition-colors">
                                    <i class="fas fa-minus mr-1"></i> Remove Row
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mb-3">
                            Fill in the correct answers. This is what student submissions will be graded against.
                        </p>

                        <div class="border-2 border-[#FFC8FB] rounded-xl overflow-hidden bg-white">
                            <div class="overflow-x-auto" style="max-height: 500px;">
                                <div id="spreadsheet"></div>
                            </div>
                        </div>

                        <input type="hidden" name="correct_data" id="correct_data">
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-[#FFC8FB]/50">
                        <a href="{{ route('instructors.performance-tasks.exercises.show', $task->id) }}"
                           class="px-6 py-2.5 bg-white text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-8 py-2.5 bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white font-semibold text-sm rounded-lg hover:opacity-90 transition-all shadow-md">
                            {{ isset($exercise) ? 'Update Exercise' : 'Save Exercise' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('spreadsheet');

    // Load existing data when editing, otherwise start blank
    const existingData = @json(isset($exercise) ? $exercise->correct_data : null);

    const blankRow  = () => Array(10).fill('');
    const initRows  = 15;

    let data;
    if (existingData) {
        // Support both raw array and {data, metadata} wrapper
        data = Array.isArray(existingData)
            ? existingData
            : (existingData.data ?? []);
        // Pad to at least initRows
        while (data.length < initRows) data.push(blankRow());
    } else {
        data = Array(initRows).fill(null).map(blankRow);
    }

    const isMobile = window.innerWidth < 640;
    const colW     = isMobile ? 90 : 120;

    const columns = Array(10).fill(null).map((_, i) => ({
        type    : 'text',
        width   : colW,
        align   : 'center',
        title   : String.fromCharCode(65 + i), // A, B, C …
        wordWrap: true,
    }));

    window.table = jspreadsheet(container, {
        data             : data,
        columns          : columns,
        defaultColWidth  : colW,
        minDimensions    : [10, initRows],
        tableWidth       : '100%',
        tableOverflow    : true,
        tableHeight      : '450px',
        allowFormulas    : true,
        columnSorting    : false,
        columnDrag       : false,
        rowDrag          : false,
        allowInsertRow   : true,
        allowInsertColumn: true,
        allowDeleteRow   : true,
        allowDeleteColumn: true,
        columnResize     : true,
        rowResize        : true,
        copyCompatibility: true,

        contextMenu: function(obj, x, y, e) {
            return [
                { title: 'Insert row above', onclick: () => obj.insertRow(1, parseInt(y), true) },
                { title: 'Insert row below', onclick: () => obj.insertRow(1, parseInt(y)) },
                { title: 'Delete row',       onclick: () => obj.deleteRow(parseInt(y)) },
                { type: 'line' },
                { title: 'Insert col left',  onclick: () => obj.insertColumn(1, parseInt(x), true) },
                { title: 'Insert col right', onclick: () => obj.insertColumn(1, parseInt(x)) },
                { title: 'Delete column',    onclick: () => obj.deleteColumn(parseInt(x)) },
                { type: 'line' },
                { title: 'Copy',  onclick: () => obj.copy(true) },
                { title: 'Paste', onclick: () => {
                    if (navigator.clipboard) navigator.clipboard.readText().then(t => obj.paste(x, y, t));
                }},
            ];
        },
    });

    // Add / remove row helpers wired to buttons
    window.addRow        = () => window.table.insertRow();
    window.removeLastRow = () => {
        const rowCount = window.table.getData().length;
        if (rowCount > 1) window.table.deleteRow(rowCount - 1);
    };

    // On submit — serialize spreadsheet → correct_data hidden field
    document.getElementById('exerciseForm').addEventListener('submit', function (e) {
        const data = window.table.getData();

        // Strip completely empty trailing rows
        let lastNonEmpty = -1;
        data.forEach((row, i) => {
            if (row.some(cell => cell !== null && cell !== '')) lastNonEmpty = i;
        });

        const trimmed = lastNonEmpty >= 0 ? data.slice(0, lastNonEmpty + 1) : data;

        if (trimmed.length === 0 || trimmed.every(r => r.every(c => c === '' || c === null))) {
            e.preventDefault();
            alert('Please fill in at least one cell in the answer sheet.');
            return;
        }

        document.getElementById('correct_data').value = JSON.stringify(trimmed);
    });
});
</script>

<style>
    #spreadsheet { width: 100%; }
    #spreadsheet .jexcel_content { overflow: auto; }
    .jexcel tbody tr:nth-child(odd)  td { background-color: #fafafa; }
    .jexcel tbody tr:nth-child(even) td { background-color: #ffffff; }
    .jexcel td { border-color: #ffc8fb !important; }
    #spreadsheet ::-webkit-scrollbar { width: 6px; height: 6px; }
    #spreadsheet ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
</style>
</x-app-layout>