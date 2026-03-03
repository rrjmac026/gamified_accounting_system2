<x-app-layout>
    <div class="flex justify-between items-center px-4 sm:px-8 mt-4">
        <a href="{{ route('instructors.performance-tasks.exercises.show', $task->id) }}"
           class="inline-flex items-center text-[#D5006D] hover:text-[#FF6F91] font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Exercises
        </a>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] shadow-lg rounded-2xl p-6 sm:p-8 border border-[#FFC8FB]/50">

                {{-- Header --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 text-xs font-bold bg-[#D5006D] text-white rounded-full">Step 1</span>
                        <h2 class="text-xl font-bold text-[#D5006D]">Analyze Transactions</h2>
                    </div>
                    <p class="text-sm text-gray-500">Task: <span class="font-medium text-gray-700">{{ $task->title }}</span></p>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ isset($exercise)
                          ? route('instructors.performance-tasks.exercises.update', [$task, $exercise])
                          : route('instructors.performance-tasks.exercises.store', [$task, 1]) }}"
                      id="exerciseForm">
                    @csrf
                    @if(isset($exercise)) @method('PUT') @endif

                    {{-- Exercise Title --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-[#D5006D] mb-1">
                            Exercise Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $exercise->title ?? 'Exercise ' . $nextNumber) }}"
                               class="w-full rounded-lg border border-[#FFC8FB] bg-white px-4 py-2 text-gray-800 focus:border-pink-400 focus:ring focus:ring-pink-200"
                               required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-[#D5006D] mb-1">Description (Optional)</label>
                        <textarea name="description" rows="2"
                                  class="w-full rounded-lg border border-[#FFC8FB] bg-white px-4 py-2 text-gray-800 focus:border-pink-400 focus:ring focus:ring-pink-200"
                                  placeholder="Additional instructions for this exercise...">{{ old('description', $exercise->description ?? '') }}</textarea>
                    </div>

                    {{-- ── Step 1 Specific: Transaction Analysis Table ── --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-semibold text-[#D5006D]">
                                Transactions to Analyze <span class="text-red-500">*</span>
                            </label>
                            <button type="button" onclick="addRow()"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-[#D5006D] hover:bg-[#FF6F91] rounded-lg transition-colors">
                                <i class="fas fa-plus mr-1"></i>Add Row
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-[#FFC8FB]">
                            <table class="min-w-full text-sm" id="transactionTable">
                                <thead class="bg-[#FF9AAB]/20">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D] w-8">#</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D]">Date</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D]">Transaction Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D]">Account Affected</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D]">Classification</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-[#D5006D]">Effect (+/-)</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold text-[#D5006D]">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionBody" class="bg-white divide-y divide-[#FFC8FB]/30">
                                    {{-- Rows injected by JS --}}
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Students will analyze each transaction and identify the accounts affected.</p>
                    </div>

                    {{-- Hidden field for correct_data JSON --}}
                    <input type="hidden" name="correct_data" id="correct_data">

                    {{-- Submit --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('instructors.performance-tasks.exercises.show', $task->id) }}"
                           class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-8 py-2.5 bg-gradient-to-r from-[#D5006D] to-[#FF6F91] text-white font-semibold rounded-lg hover:opacity-90 transition-all shadow-md">
                            {{ isset($exercise) ? 'Update Exercise' : 'Save Exercise' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const classifications = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        const effects = ['+', '-'];

        // Load existing data if editing
        let existingData = @json(isset($exercise) ? $exercise->correct_data : null);

        function addRow(data = {}) {
            const tbody = document.getElementById('transactionBody');
            const rowIndex = tbody.rows.length + 1;

            const row = document.createElement('tr');
            row.className = 'hover:bg-[#FFF0FA]';
            row.innerHTML = `
                <td class="px-3 py-2 text-gray-500 text-xs">${rowIndex}</td>
                <td class="px-2 py-1.5">
                    <input type="text" placeholder="e.g. Jan 1"
                           value="${data.date || ''}"
                           class="w-24 rounded border border-[#FFC8FB] px-2 py-1 text-xs focus:outline-none focus:border-pink-400 row-date">
                </td>
                <td class="px-2 py-1.5">
                    <input type="text" placeholder="Describe the transaction"
                           value="${data.description || ''}"
                           class="w-full min-w-[180px] rounded border border-[#FFC8FB] px-2 py-1 text-xs focus:outline-none focus:border-pink-400 row-description">
                </td>
                <td class="px-2 py-1.5">
                    <input type="text" placeholder="e.g. Cash"
                           value="${data.account || ''}"
                           class="w-32 rounded border border-[#FFC8FB] px-2 py-1 text-xs focus:outline-none focus:border-pink-400 row-account">
                </td>
                <td class="px-2 py-1.5">
                    <select class="rounded border border-[#FFC8FB] px-2 py-1 text-xs focus:outline-none focus:border-pink-400 row-classification">
                        <option value="">Select</option>
                        ${classifications.map(c => `<option value="${c}" ${data.classification === c ? 'selected' : ''}>${c}</option>`).join('')}
                    </select>
                </td>
                <td class="px-2 py-1.5">
                    <select class="rounded border border-[#FFC8FB] px-2 py-1 text-xs focus:outline-none focus:border-pink-400 row-effect">
                        <option value="">Select</option>
                        ${effects.map(e => `<option value="${e}" ${data.effect === e ? 'selected' : ''}>${e}</option>`).join('')}
                    </select>
                </td>
                <td class="px-2 py-1.5 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            // Re-number rows
            document.querySelectorAll('#transactionBody tr').forEach((row, i) => {
                row.cells[0].textContent = i + 1;
            });
        }

        function collectData() {
            const rows = document.querySelectorAll('#transactionBody tr');
            const transactions = [];
            rows.forEach(row => {
                transactions.push({
                    date:           row.querySelector('.row-date').value,
                    description:    row.querySelector('.row-description').value,
                    account:        row.querySelector('.row-account').value,
                    classification: row.querySelector('.row-classification').value,
                    effect:         row.querySelector('.row-effect').value,
                });
            });
            return { step: 1, type: 'analyze_transactions', transactions };
        }

        // On submit: serialize table → correct_data JSON
        document.getElementById('exerciseForm').addEventListener('submit', function(e) {
            const data = collectData();
            if (data.transactions.length === 0) {
                e.preventDefault();
                alert('Please add at least one transaction row.');
                return;
            }
            document.getElementById('correct_data').value = JSON.stringify(data);
        });

        // Init: load existing rows or start with 3 blank rows
        document.addEventListener('DOMContentLoaded', function() {
            if (existingData && existingData.transactions) {
                existingData.transactions.forEach(t => addRow(t));
            } else {
                addRow(); addRow(); addRow();
            }
        });
    </script>
</x-app-layout>