{{-- Wage Section Partial - working-papers/partials/wage-section.blade.php --}}

<div class="space-y-6">
    {{-- Income Section --}}
    <div>
        <h4 class="text-md font-semibold text-gray-800 mb-3">Income</h4>
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary/Wages</label>
                    <input type="number" step="0.01"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Withheld</label>
                    <input type="number" step="0.01"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="0.00">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload PAYG Summary (Optional)</label>
                <input type="file"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>
    </div>

    {{-- Work-Related Expenses (Optional) --}}
    <div>
        <h4 class="text-md font-semibold text-gray-800 mb-3">Work-Related Expenses (Optional)</h4>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Expense rows will be dynamically added here --}}
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                            No expenses added yet
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button"
                class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Expense
        </button>
    </div>
</div>
