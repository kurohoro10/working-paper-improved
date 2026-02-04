{{-- SMSF Section Partial - working-papers/partials/smsf-section.blade.php --}}

<div class="space-y-6">
    <p class="text-sm text-gray-600 italic">Self-Managed Super Fund income and expense tracking</p>

    {{-- Income Categories --}}
    <div>
        <h4 class="text-md font-semibold text-gray-800 mb-3">SMSF Income</h4>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contributions</label>
                    <input type="number" step="0.01" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Interest Income</label>
                    <input type="number" step="0.01" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dividends</label>
                    <input type="number" step="0.01" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rental Income</label>
                    <input type="number" step="0.01" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capital Gains</label>
                    <input type="number" step="0.01" class="w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                    <select class="w-full rounded-md border-gray-300">
                        <option>All</option>
                        <option>Q1</option>
                        <option>Q2</option>
                        <option>Q3</option>
                        <option>Q4</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Expenses with GST --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">SMSF Expenses</h4>
            <button type="button" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                + Add Expense
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Inc GST</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">GST</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Net</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Quarter</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Upload</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500">
                            No expenses added yet
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-teal-50 font-semibold">
                    <tr>
                        <td colspan="2" class="px-3 py-2 text-sm">Total</td>
                        <td class="px-3 py-2 text-sm">$0.00</td>
                        <td class="px-3 py-2 text-sm">$0.00</td>
                        <td class="px-3 py-2 text-sm">$0.00</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Summary --}}
    <div class="bg-teal-100 rounded-lg p-6 border-2 border-teal-300">
        <h4 class="text-lg font-bold text-gray-900 mb-4">SMSF Summary</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg">
                <div class="text-sm text-gray-600">Total Income</div>
                <div class="text-2xl font-bold text-green-600">$0.00</div>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="text-sm text-gray-600">Total Expenses</div>
                <div class="text-2xl font-bold text-red-600">$0.00</div>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <div class="text-sm text-gray-600">Net Position</div>
                <div class="text-2xl font-bold text-teal-600">$0.00</div>
            </div>
        </div>
    </div>

    {{-- Comments --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client Comments</label>
            <textarea rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
        </div>
    </div>
</div>
