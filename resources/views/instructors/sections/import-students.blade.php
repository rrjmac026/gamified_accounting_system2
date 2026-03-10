<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 bg-gradient-to-r from-white/90 to-[#FFF0FA]/90 backdrop-blur-sm rounded-2xl shadow-xl border border-[#FFC8FB]/30 p-6 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-[#FF92C2]/5 via-transparent to-[#FFC8FB]/10 pointer-events-none"></div>
                <div class="relative flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full mr-4 shadow-md">
                            <i class="fas fa-file-import text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Import Students</h2>
                            <p class="text-sm text-gray-500 mt-0.5">
                                <span class="font-medium text-[#FF92C2]">{{ $section->name }}</span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="bg-[#FF92C2]/10 text-[#FF92C2] px-2 py-0.5 rounded-lg text-xs font-semibold border border-[#FF92C2]/20">
                                    {{ $section->section_code }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('instructors.sections.manage-students', $section->id) }}"
                       class="inline-flex items-center px-5 py-3 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 hover:border-gray-300 rounded-xl font-medium shadow-sm hover:shadow-md transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Manage Students
                    </a>
                </div>
            </div>

            {{-- Import Errors --}}
            @if(session('import_errors') && count(session('import_errors')))
                <div class="mb-6 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start">
                        <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-yellow-800 mb-2">Some rows were skipped during import:</p>
                            <ul class="space-y-1">
                                @foreach(session('import_errors') as $err)
                                    <li class="flex items-start text-sm text-yellow-700">
                                        <i class="fas fa-dot-circle mt-1 mr-2 text-yellow-400 text-xs"></i>
                                        {{ $err }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-5 shadow-sm">
                    <div class="flex items-start">
                        <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-red-800 mb-2">Please fix the following errors:</p>
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm text-red-700 flex items-start">
                                        <i class="fas fa-dot-circle mt-1 mr-2 text-red-400 text-xs"></i>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Two column layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- LEFT: Upload Form (3 cols) --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Step indicator --}}
                    <div class="bg-white rounded-2xl shadow-md border border-[#FFC8FB]/30 p-5">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">How It Works</h3>
                        <div class="space-y-3">
                            @foreach([
                                ['1', 'Download the CSV template below', 'fas fa-download', 'from-[#FF92C2] to-[#FFC8FB]'],
                                ['2', 'Fill in student numbers or emails', 'fas fa-pen', 'from-blue-400 to-blue-500'],
                                ['3', 'Upload your completed file', 'fas fa-cloud-upload-alt', 'from-green-400 to-green-500'],
                            ] as $step)
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 hover:bg-[#FFF6FD] transition-colors duration-200 border border-gray-100">
                                <div class="w-9 h-9 bg-gradient-to-r {{ $step[3] }} rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="{{ $step[2] }} text-white text-sm"></i>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="w-5 h-5 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ $step[0] }}
                                    </span>
                                    <span class="text-sm text-gray-700 font-medium">{{ $step[1] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Upload Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-[#FFC8FB]/30 overflow-hidden">
                        <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] p-5">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-upload text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Upload Your File</h3>
                                    <p class="text-white/80 text-xs mt-0.5">CSV, XLS, or XLSX — max 2MB</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('instructors.sections.import', $section->id) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              class="p-6 space-y-5"
                              id="importForm">
                            @csrf

                            {{-- Drop Zone --}}
                            <div id="dropZone"
                                 onclick="document.getElementById('fileInput').click()"
                                 class="relative border-2 border-dashed border-[#FFC8FB] rounded-xl p-10 text-center hover:border-[#FF92C2] hover:bg-[#FFF0FA]/60 transition-all duration-300 cursor-pointer group">

                                <div class="flex flex-col items-center pointer-events-none" id="dropContent">
                                    <div class="w-16 h-16 bg-gradient-to-r from-[#FF92C2]/15 to-[#FFC8FB]/15 group-hover:from-[#FF92C2]/25 group-hover:to-[#FFC8FB]/25 rounded-full flex items-center justify-center mb-4 transition-all duration-300 border-2 border-[#FFC8FB]/50 group-hover:border-[#FF92C2]/50">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-[#FF92C2]"></i>
                                    </div>
                                    <p class="text-base font-semibold text-gray-700 group-hover:text-[#FF92C2] transition-colors" id="dropText">
                                        Click or drag & drop your file here
                                    </p>
                                    <p class="text-sm text-gray-400 mt-1">Accepted formats:</p>
                                    <div class="flex gap-2 mt-2">
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">.CSV</span>
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-semibold">.XLS</span>
                                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-xs font-semibold">.XLSX</span>
                                    </div>
                                </div>

                                {{-- File selected state (hidden by default) --}}
                                <div class="hidden flex-col items-center pointer-events-none" id="fileSelectedContent">
                                    <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center mb-4 shadow-md">
                                        <i class="fas fa-check text-white text-2xl"></i>
                                    </div>
                                    <p class="text-base font-bold text-green-700" id="selectedFileName">File selected</p>
                                    <p class="text-sm text-gray-500 mt-1" id="selectedFileSize"></p>
                                    <p class="text-xs text-[#FF92C2] mt-2 font-medium">Click to change file</p>
                                </div>

                                <input type="file"
                                       id="fileInput"
                                       name="file"
                                       accept=".csv,.xls,.xlsx"
                                       class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                       onchange="handleFileSelect(this)">
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                    id="submitBtn"
                                    class="w-full py-4 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white rounded-xl font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <i class="fas fa-file-import"></i>
                                <span id="submitText">Import Students</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: Template Preview (2 cols) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Download Template Card --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-[#FFC8FB]/30 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-5">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-table text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">CSV Template</h3>
                                    <p class="text-white/80 text-xs mt-0.5">Preview & download</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Spreadsheet Preview --}}
                            <div class="rounded-xl overflow-hidden border-2 border-gray-100 shadow-sm">
                                {{-- Column header row --}}
                                <div class="grid grid-cols-12 bg-gray-100 border-b border-gray-200">
                                    <div class="col-span-1 px-2 py-2 text-center text-xs font-bold text-gray-400 border-r border-gray-200"></div>
                                    <div class="col-span-11 px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wide">A</div>
                                </div>
                                {{-- Header Row --}}
                                <div class="grid grid-cols-12 bg-gradient-to-r from-[#FFF0FA] to-[#FFC8FB]/20 border-b border-[#FFC8FB]/40">
                                    <div class="col-span-1 px-2 py-2.5 text-center text-xs font-bold text-gray-400 border-r border-gray-200">1</div>
                                    <div class="col-span-11 px-3 py-2.5">
                                        <span class="text-xs font-bold text-[#FF92C2] bg-[#FF92C2]/10 px-2 py-1 rounded-md border border-[#FF92C2]/20">
                                            student_number
                                        </span>
                                    </div>
                                </div>
                                {{-- Example Row 1 --}}
                                <div class="grid grid-cols-12 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="col-span-1 px-2 py-2.5 text-center text-xs text-gray-400 border-r border-gray-200">2</div>
                                    <div class="col-span-11 px-3 py-2.5 text-xs text-gray-700 font-mono">2024-00001</div>
                                </div>
                                {{-- Example Row 2 --}}
                                <div class="grid grid-cols-12 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="col-span-1 px-2 py-2.5 text-center text-xs text-gray-400 border-r border-gray-200">3</div>
                                    <div class="col-span-11 px-3 py-2.5 text-xs text-gray-700 font-mono">2024-00002</div>
                                </div>
                                {{-- Example Row 3 (email) --}}
                                <div class="grid grid-cols-12 hover:bg-gray-50 transition-colors">
                                    <div class="col-span-1 px-2 py-2.5 text-center text-xs text-gray-400 border-r border-gray-200">4</div>
                                    <div class="col-span-11 px-3 py-2.5 text-xs text-gray-400 font-mono italic">or email@domain.com</div>
                                </div>
                            </div>

                            {{-- Legend --}}
                            <div class="space-y-2">
                                <div class="flex items-start gap-2 text-xs text-gray-600">
                                    <i class="fas fa-info-circle text-[#FF92C2] mt-0.5 flex-shrink-0"></i>
                                    <span>Column A accepts <strong>student number</strong> (e.g. 2024-00001) or <strong>email address</strong></span>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-gray-600">
                                    <i class="fas fa-info-circle text-blue-400 mt-0.5 flex-shrink-0"></i>
                                    <span>Row 1 is the header — do not remove it</span>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-gray-600">
                                    <i class="fas fa-info-circle text-green-400 mt-0.5 flex-shrink-0"></i>
                                    <span>One student per row, starting from row 2</span>
                                </div>
                            </div>

                            {{-- Download Button --}}
                            <a href="{{ route('instructors.sections.import-template') }}"
                               class="flex items-center justify-center gap-2 w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                                <i class="fas fa-download"></i>
                                Download CSV Template
                            </a>
                        </div>
                    </div>

                    {{-- Capacity Info Card --}}
                    @if($section->capacity)
                    <div class="bg-white rounded-2xl shadow-md border border-[#FFC8FB]/30 p-5">
                        <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-[#FF92C2] mr-2"></i>
                            Section Capacity
                        </h4>
                        @php
                            $enrolled  = $section->students->count();
                            $capacity  = $section->capacity;
                            $available = $capacity - $enrolled;
                            $pct       = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                            $barColor  = $pct >= 90 ? 'from-red-400 to-red-500' : ($pct >= 70 ? 'from-yellow-400 to-yellow-500' : 'from-[#FF92C2] to-[#FFC8FB]');
                        @endphp
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Enrolled: <strong class="text-gray-800">{{ $enrolled }}</strong></span>
                                <span class="text-gray-600">Capacity: <strong class="text-gray-800">{{ $capacity }}</strong></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="h-3 rounded-full bg-gradient-to-r {{ $barColor }} transition-all duration-500"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ $pct }}% full</span>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full
                                    {{ $available > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $available > 0 ? "{$available} slot(s) available" : 'Section is full' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function handleFileSelect(input) {
            const file = input.files[0];
            if (!file) return;

            const dropContent      = document.getElementById('dropContent');
            const fileSelectedContent = document.getElementById('fileSelectedContent');
            const selectedFileName = document.getElementById('selectedFileName');
            const selectedFileSize = document.getElementById('selectedFileSize');

            // Format file size
            const size = file.size < 1024 * 1024
                ? (file.size / 1024).toFixed(1) + ' KB'
                : (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            selectedFileName.textContent = file.name;
            selectedFileSize.textContent = size;

            dropContent.classList.add('hidden');
            dropContent.classList.remove('flex');
            fileSelectedContent.classList.remove('hidden');
            fileSelectedContent.classList.add('flex');

            document.getElementById('dropZone').classList.add('border-green-400', 'bg-green-50/40');
            document.getElementById('dropZone').classList.remove('border-[#FFC8FB]');
        }

        // Drag and drop
        const dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('border-[#FF92C2]', 'bg-[#FFF0FA]/60', 'scale-[1.01]');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-[#FF92C2]', 'bg-[#FFF0FA]/60', 'scale-[1.01]');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-[#FF92C2]', 'bg-[#FFF0FA]/60', 'scale-[1.01]');
            const fileInput = document.getElementById('fileInput');
            fileInput.files = e.dataTransfer.files;
            handleFileSelect(fileInput);
        });

        // Show loading state on submit
        document.getElementById('importForm').addEventListener('submit', function () {
            const btn  = document.getElementById('submitBtn');
            const text = document.getElementById('submitText');
            btn.disabled = true;
            text.textContent = 'Importing...';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Importing...';
        });
    </script>
</x-app-layout>