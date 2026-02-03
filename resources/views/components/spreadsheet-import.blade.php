@props(['step'])

<div class="mb-4">
    <div class="flex flex-wrap gap-2">
        <!-- File Upload Button -->
        <label for="file-upload-{{ $step }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors text-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            Import from File
        </label>
        <input id="file-upload-{{ $step }}" type="file" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleFileImport(event, {{ $step }})">

        <!-- Google Sheets Button -->
        <button type="button" onclick="importFromGoogleSheets({{ $step }})" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.5 2h-15A2.5 2.5 0 002 4.5v15A2.5 2.5 0 004.5 22h15a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0019.5 2zM9 18H5v-3h4v3zm0-5H5v-3h4v3zm0-5H5V5h4v3zm5 10h-4v-3h4v3zm0-5h-4v-3h4v3zm0-5h-4V5h4v3zm5 10h-4v-3h4v3zm0-5h-4v-3h4v3zm0-5h-4V5h4v3z"/>
            </svg>
            Import from Google Sheets
        </button>

        <!-- Sample Template Download -->
        <button type="button" onclick="downloadTemplate({{ $step }})" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download Template
        </button>
    </div>

    <!-- Google Sheets URL Input (hidden by default) -->
    <div id="google-sheets-input-{{ $step }}" class="hidden mt-3">
        <div class="flex gap-2">
            <input type="text" id="sheets-url-{{ $step }}" placeholder="Paste Google Sheets URL here..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <button type="button" onclick="loadGoogleSheet({{ $step }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Load
            </button>
            <button type="button" onclick="cancelGoogleSheets({{ $step }})" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Cancel
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">Make sure the Google Sheet is publicly accessible or shared with view permissions</p>
    </div>
</div>

<!-- Loading Indicator -->
<div id="import-loading-{{ $step }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 flex items-center gap-3">
        <svg class="animate-spin h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-lg font-medium">Importing data...</span>
    </div>
</div>