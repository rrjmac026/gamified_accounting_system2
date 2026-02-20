@section('title', 'Import Students to Section')
<x-app-layout>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Import Students to Section</h2>
        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.sections.index') }}"
               class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-white border border-[#FF92C2] text-[#FF92C2] rounded-lg hover:bg-[#FFF0FA] focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Sections
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div class="px-4 sm:px-8 mt-4">
        @if (session('success'))
            <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl mb-3">
                <i class="fas fa-check-circle mt-0.5 text-green-500 flex-shrink-0"></i>
                <div class="flex-1 text-sm">
                    <span class="font-medium">{{ session('success') }}</span>
                    @if (session('import_errors') && count(session('import_errors')))
                        <p class="mt-2 font-semibold text-green-800">Rows with issues:</p>
                        <ul class="list-disc list-inside mt-1 space-y-0.5">
                            @foreach (session('import_errors') as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl mb-3">
                <i class="fas fa-exclamation-triangle mt-0.5 text-red-500 flex-shrink-0"></i>
                <ul class="list-disc list-inside text-sm space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- ── Upload Card ── --}}
                <div class="lg:col-span-3">
                    <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg">
                        <div class="p-4 sm:p-6">

                            <div class="flex items-center mb-6">
                                <div class="w-9 h-9 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3 shadow-sm">
                                    <i class="fas fa-file-import text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-800">Upload Excel File</h3>
                                    <p class="text-xs text-gray-500">Accepted: .xlsx, .xls — Max 5 MB</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.sections.import.store') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="importForm">
                                @csrf

                                {{-- Drag-drop zone --}}
                                <label for="import_file"
                                       id="dropZone"
                                       class="group flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-[#FFC8FB] rounded-xl bg-white/70 hover:bg-white hover:border-[#FF92C2] cursor-pointer transition-all duration-200 mb-4">

                                    <div class="flex flex-col items-center pointer-events-none" id="dropContent">
                                        <div class="w-12 h-12 bg-[#FFE6F0] rounded-full flex items-center justify-center mb-3 group-hover:bg-[#FF92C2]/20 transition-colors">
                                            <i class="fas fa-cloud-upload-alt text-[#FF92C2] text-xl"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">Click to browse or drag & drop</p>
                                        <p class="text-xs text-gray-400 mt-1">.xlsx or .xls files only</p>
                                    </div>

                                    <div class="hidden flex-col items-center pointer-events-none" id="fileSelectedContent">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fas fa-file-excel text-green-500 text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-800" id="fileName">—</p>
                                        <p class="text-xs text-gray-400 mt-1">Click to change file</p>
                                    </div>

                                    <input type="file" name="import_file" id="import_file" class="sr-only" accept=".xlsx,.xls">
                                </label>

                                @error('import_file')
                                    <p class="text-xs text-red-500 -mt-2 mb-3">{{ $message }}</p>
                                @enderror

                                <div class="flex flex-col sm:flex-row gap-3 mt-2">
                                    <button type="submit"
                                            id="submitBtn"
                                            class="flex-1 px-6 py-2.5 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg font-medium text-sm">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                                        Import Now
                                    </button>
                                    <a href="{{ route('admin.sections.import.template') }}"
                                       class="flex-1 px-6 py-2.5 bg-white border border-[#FF92C2] text-[#FF92C2] rounded-lg hover:bg-[#FFF0FA] focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md font-medium text-sm">
                                        <i class="fas fa-download mr-2"></i>
                                        Download Template
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ── Instructions Card ── --}}
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6 h-full">

                        <div class="flex items-center mb-5">
                            <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-info text-white text-sm"></i>
                            </div>
                            <h3 class="text-base font-semibold text-gray-800">How to Use</h3>
                        </div>

                        <ol class="space-y-3 text-sm text-gray-600">
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#FFE6F0] text-[#FF92C2] rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <span>Download the <span class="font-semibold text-gray-800">Excel template</span> using the button on the left.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#FFE6F0] text-[#FF92C2] rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <span>Fill in data starting from <span class="font-semibold text-gray-800">row 9</span>. Do not change column headers or order.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#FFE6F0] text-[#FF92C2] rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span><code class="bg-[#FFE6F0] text-[#FF92C2] px-1 rounded text-xs">student_number</code> and <code class="bg-[#FFE6F0] text-[#FF92C2] px-1 rounded text-xs">section_code</code> must exist in the system.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#FFE6F0] text-[#FF92C2] rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                <span>Section <span class="font-semibold text-gray-800">capacity limits</span> are enforced; rows over limit are skipped.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="flex-shrink-0 w-5 h-5 bg-[#FFE6F0] text-[#FF92C2] rounded-full flex items-center justify-center text-xs font-bold">5</span>
                                <span>Students already in the section are <span class="font-semibold text-gray-800">silently skipped</span> — no duplicates.</span>
                            </li>
                        </ol>

                        <hr class="my-4 border-[#FFC8FB]/50">

                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Required Columns</p>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="inline-block bg-[#FFE6F0] text-[#FF92C2] px-2 py-1 rounded text-xs font-medium">student_number</span>
                            <span class="inline-block bg-[#FFE6F0] text-[#FF92C2] px-2 py-1 rounded text-xs font-medium">section_code</span>
                        </div>

                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Optional Columns</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-block bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-medium">year_level</span>
                            <span class="inline-block bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-medium">notes</span>
                            <span class="inline-block bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-medium">status</span>
                            <span class="inline-block bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-medium">email</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const input        = document.getElementById('import_file');
        const dropZone     = document.getElementById('dropZone');
        const defaultView  = document.getElementById('dropContent');
        const selectedView = document.getElementById('fileSelectedContent');
        const fileNameEl   = document.getElementById('fileName');
        const submitBtn    = document.getElementById('submitBtn');

        function showFile(file) {
            fileNameEl.textContent = file.name;
            defaultView.classList.add('hidden');
            defaultView.classList.remove('flex');
            selectedView.classList.remove('hidden');
            selectedView.classList.add('flex');
        }

        input.addEventListener('change', function () {
            if (this.files.length > 0) showFile(this.files[0]);
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-[#FF92C2]', 'bg-white');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-[#FF92C2]', 'bg-white');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-[#FF92C2]', 'bg-white');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showFile(file);
            }
        });

        document.getElementById('importForm').addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Importing...';
        });
    </script>

</x-app-layout>