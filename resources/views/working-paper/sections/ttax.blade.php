{{-- Trust Tax Section Partial - working-papers/partials/ttax-section.blade.php --}}

<div class="space-y-6" x-data="ttaxSection()">
    {{-- Quarter Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex flex-wrap gap-2 sm:gap-0 sm:space-x-8">
            <template x-for="tab in tabs" :key="tab.value">
                <button type="button"
                        @click="activeTab = tab.value"
                        :class="activeTab === tab.value
                            ? 'border-pink-600 text-pink-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="border-b-2 py-4 px-1 text-sm font-medium transition"
                        x-text="tab.label">
                </button>
            </template>
        </nav>
    </div>

    {{-- Income Section --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">Income</h4>
            <button type="button"
                    @click="addIncome"
                    class="text-sm text-pink-600 hover:text-pink-700 font-medium transition">
                + Add Income
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quarter</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="filteredIncome.length === 0">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    No income entries <span x-show="activeTab !== 'all'" x-text="'for ' + getCurrentTabLabel()"></span>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in filteredIncome" :key="index">
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="text"
                                           x-model="item.description"
                                           class="w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="Income description">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           step="0.01"
                                           x-model="item.amount"
                                           class="w-full rounded-md border-gray-300 text-sm text-right focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="0.00">
                                </td>
                                <td class="px-4 py-3">
                                    <select x-model="item.quarter"
                                            class="w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="file" class="text-xs w-full">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="removeIncome(index)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-pink-50 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-sm">Total Income</td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalIncome.toFixed(2)"></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Expenses Section --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">Expenses</h4>
            <button type="button"
                    @click="addExpense"
                    class="text-sm text-pink-600 hover:text-pink-700 font-medium transition">
                + Add Expense
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inc GST</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">GST</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ex GST</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quarter</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="filteredExpenses.length === 0">
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    No expenses <span x-show="activeTab !== 'all'" x-text="'for ' + getCurrentTabLabel()"></span>
                                </td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in filteredExpenses" :key="index">
                            <tr>
                                <td class="px-4 py-3">
                                    <select x-model="item.type"
                                            class="w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                        <option value="operating">Operating</option>
                                        <option value="admin">Admin</option>
                                        <option value="other">Other</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text"
                                           x-model="item.description"
                                           class="w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="Expense description">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           step="0.01"
                                           x-model="item.amountIncGst"
                                           @input="calculateGST(item)"
                                           class="w-full rounded-md border-gray-300 text-sm text-right focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="0.00">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           step="0.01"
                                           x-model="item.gst"
                                           class="w-full rounded-md border-gray-300 text-sm text-right bg-gray-50 focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="0.00"
                                           readonly>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           step="0.01"
                                           x-model="item.netExGst"
                                           class="w-full rounded-md border-gray-300 text-sm text-right bg-gray-50 focus:border-pink-500 focus:ring-pink-500"
                                           placeholder="0.00"
                                           readonly>
                                </td>
                                <td class="px-4 py-3">
                                    <select x-model="item.quarter"
                                            class="w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                        <option value="q1">Q1 (Jul-Sep)</option>
                                        <option value="q2">Q2 (Oct-Dec)</option>
                                        <option value="q3">Q3 (Jan-Mar)</option>
                                        <option value="q4">Q4 (Apr-Jun)</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="file" class="text-xs w-full">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="removeExpense(index)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-pink-50 font-semibold">
                        <tr>
                            <td colspan="2" class="px-4 py-3 text-sm">Total Expenses</td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalExpensesIncGst.toFixed(2)"></td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalExpensesGst.toFixed(2)"></td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalExpensesNet.toFixed(2)"></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-6 border-2 border-pink-300">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Trust Tax Summary</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Income</div>
                <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(totalIncome)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Expenses (Net)</div>
                <div class="text-2xl font-bold text-red-600" x-text="formatCurrency(totalExpensesNet)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Net Profit</div>
                <div class="text-2xl font-bold"
                     :class="netProfit >= 0 ? 'text-pink-600' : 'text-red-600'"
                     x-text="formatCurrency(netProfit)"></div>
            </div>
        </div>
    </div>

    {{-- Comments Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client Comments</label>
            <textarea rows="3"
                      x-model="clientComments"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition"
                      placeholder="Add any notes or comments..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3"
                      x-model="internalComments"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 transition"
                      placeholder="Add internal notes..."></textarea>
        </div>
    </div>
</div>

<script>
function ttaxSection() {
    return {
        activeTab: 'all',
        tabs: [
            { value: 'all', label: 'All Periods' },
            { value: 'q1', label: 'Q1 (Jul-Sep)' },
            { value: 'q2', label: 'Q2 (Oct-Dec)' },
            { value: 'q3', label: 'Q3 (Jan-Mar)' },
            { value: 'q4', label: 'Q4 (Apr-Jun)' }
        ],
        income: [],
        expenses: [],
        clientComments: '',
        internalComments: '',

        get filteredIncome() {
            if (this.activeTab === 'all') return this.income;
            return this.income.filter(item => item.quarter === this.activeTab);
        },

        get filteredExpenses() {
            if (this.activeTab === 'all') return this.expenses;
            return this.expenses.filter(item => item.quarter === this.activeTab);
        },

        get totalIncome() {
            return this.filteredIncome.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
        },

        get totalExpensesIncGst() {
            return this.filteredExpenses.reduce((sum, item) => sum + parseFloat(item.amountIncGst || 0), 0);
        },

        get totalExpensesGst() {
            return this.filteredExpenses.reduce((sum, item) => sum + parseFloat(item.gst || 0), 0);
        },

        get totalExpensesNet() {
            return this.filteredExpenses.reduce((sum, item) => sum + parseFloat(item.netExGst || 0), 0);
        },

        get netProfit() {
            return this.totalIncome - this.totalExpensesNet;
        },

        // Helper function for formatting
        formatCurrency(number) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD', // Change to 'AUD' or your preferred code
            }).format(number || 0);
        },

        addIncome() {
            this.income.push({
                description: '',
                amount: 0,
                quarter: this.activeTab === 'all' ? 'q1' : this.activeTab
            });
        },

        removeIncome(index) {
            const actualIndex = this.activeTab === 'all'
                ? index
                : this.income.findIndex((item, i) =>
                    item.quarter === this.activeTab &&
                    this.filteredIncome[index] === item
                  );
            this.income.splice(actualIndex, 1);
        },

        addExpense() {
            this.expenses.push({
                type: 'operating',
                description: '',
                amountIncGst: 0,
                gst: 0,
                netExGst: 0,
                quarter: this.activeTab === 'all' ? 'q1' : this.activeTab
            });
        },

        removeExpense(index) {
            const actualIndex = this.activeTab === 'all'
                ? index
                : this.expenses.findIndex((item, i) =>
                    item.quarter === this.activeTab &&
                    this.filteredExpenses[index] === item
                  );
            this.expenses.splice(actualIndex, 1);
        },

        calculateGST(item) {
            const amount = parseFloat(item.amountIncGst || 0);
            item.gst = (amount / 11).toFixed(2);
            item.netExGst = (amount - item.gst).toFixed(2);
        },

        getCurrentTabLabel() {
            const tab = this.tabs.find(t => t.value === this.activeTab);
            return tab ? tab.label : '';
        }
    }
}
</script>
