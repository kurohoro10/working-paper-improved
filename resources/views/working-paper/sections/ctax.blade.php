{{-- Company Tax Section Partial - working-papers/partials/ctax-section.blade.php --}}

<div class="space-y-6" x-data="ctaxSection({{ $section->id }})">
    {{-- Quarter Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex flex-wrap gap-2 sm:gap-0 sm:space-x-8">
            <template x-for="tab in tabs" :key="tab.value">
                <button type="button"
                        @click="changeTab(tab.value)"
                        :class="activeTab === tab.value
                            ? 'border-indigo-600 text-indigo-600'
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
                    @click="startAddingIncome"
                    x-show="!isAddingIncome"
                    class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">
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
                        {{-- Add Income Form Row --}}
                        <tr x-show="isAddingIncome" class="bg-blue-50">
                            <td class="px-4 py-3">
                                <input type="text"
                                       x-model="newIncome.description"
                                       placeholder="Income description"
                                       class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       step="0.01"
                                       x-model.number="newIncome.amount"
                                       placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 text-sm text-right focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3">
                                <select x-model="newIncome.quarter"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="q1">Q1 (Jul-Sep)</option>
                                    <option value="q2">Q2 (Oct-Dec)</option>
                                    <option value="q3">Q3 (Jan-Mar)</option>
                                    <option value="q4">Q4 (Apr-Jun)</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="file"
                                       id="newIncomeFile"
                                       @change="newIncome.file = $event.target.files[0]"
                                       class="text-xs w-full">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button type="button"
                                            @click="saveIncome"
                                            :disabled="saving"
                                            class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 disabled:opacity-50">
                                        Save
                                    </button>
                                    <button type="button"
                                            @click="cancelAddingIncome"
                                            class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Existing Income Items --}}
                        <template x-for="item in income" :key="item.id">
                            <tr>
                                <td class="px-4 py-3" x-text="item.description"></td>
                                <td class="px-4 py-3 text-right" x-text="'$' + parseFloat(item.amount).toFixed(2)"></td>
                                <td class="px-4 py-3" x-text="getQuarterLabel(item.quarter)"></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <input type="file"
                                               :id="'income-file-' + item.id"
                                               @change="uploadIncomeFile(item.id, $event)"
                                               class="hidden">
                                        <label :for="'income-file-' + item.id"
                                               class="cursor-pointer text-xs text-blue-600 hover:text-blue-800">
                                            📎 Upload
                                        </label>
                                        <template x-if="item.attachments && item.attachments.length > 0">
                                            <div class="space-y-1">
                                                <template x-for="attachment in item.attachments" :key="attachment.id">
                                                    <div class="flex items-center gap-1">
                                                        <a :href="'/api/work-sections/attachments/' + attachment.id + '/download'"
                                                           target="_blank"
                                                           class="text-xs text-green-600 hover:text-green-800 truncate max-w-[150px]"
                                                           :title="attachment.original_filename"
                                                           x-text="attachment.original_filename"></a>
                                                        <button type="button"
                                                                @click="deleteAttachment(attachment.id, item, 'income')"
                                                                class="text-red-500 hover:text-red-700">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="deleteIncome(item.id)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <tr x-show="!isAddingIncome && income.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                No income entries <span x-show="activeTab !== 'all'" x-text="'for ' + getCurrentTabLabel()"></span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-indigo-50 font-semibold">
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
                    @click="startAddingExpense"
                    x-show="!isAddingExpense"
                    class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">
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
                        {{-- Add Expense Form Row --}}
                        <tr x-show="isAddingExpense" class="bg-blue-50">
                            <td class="px-4 py-3">
                                <select x-model="newExpense.type"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="operating">Operating</option>
                                    <option value="admin">Admin</option>
                                    <option value="other">Other</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="text"
                                       x-model="newExpense.description"
                                       placeholder="Expense description"
                                       class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       step="0.01"
                                       x-model.number="newExpense.amountIncGst"
                                       placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 text-sm text-right focus:border-indigo-500 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600" x-text="'$' + calculateGST(newExpense.amountIncGst, newExpense.isGstFree).toFixed(2)"></td>
                            <td class="px-4 py-3 text-right text-gray-600" x-text="'$' + calculateExGST(newExpense.amountIncGst, newExpense.isGstFree).toFixed(2)"></td>
                            <td class="px-4 py-3">
                                <select x-model="newExpense.quarter"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="q1">Q1 (Jul-Sep)</option>
                                    <option value="q2">Q2 (Oct-Dec)</option>
                                    <option value="q3">Q3 (Jan-Mar)</option>
                                    <option value="q4">Q4 (Apr-Jun)</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <input type="file"
                                           id="newExpenseFile"
                                           @change="newExpense.file = $event.target.files[0]"
                                           class="text-xs w-full">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" x-model="newExpense.isGstFree" class="rounded border-gray-300 text-indigo-600">
                                        <span class="ml-1">GST Free</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button type="button"
                                            @click="saveExpense"
                                            :disabled="saving"
                                            class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 disabled:opacity-50">
                                        Save
                                    </button>
                                    <button type="button"
                                            @click="cancelAddingExpense"
                                            class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Existing Expense Items --}}
                        <template x-for="item in expenses" :key="item.id">
                            <tr>
                                <td class="px-4 py-3" x-text="item.type"></td>
                                <td class="px-4 py-3" x-text="item.description"></td>
                                <td class="px-4 py-3 text-right" x-text="'$' + parseFloat(item.amount_inc_gst).toFixed(2)"></td>
                                <td class="px-4 py-3 text-right" x-text="'$' + parseFloat(item.gst_amount).toFixed(2)"></td>
                                <td class="px-4 py-3 text-right" x-text="'$' + parseFloat(item.net_ex_gst).toFixed(2)"></td>
                                <td class="px-4 py-3" x-text="getQuarterLabel(item.quarter)"></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <input type="file"
                                               :id="'expense-file-' + item.id"
                                               @change="uploadExpenseFile(item.id, $event)"
                                               class="hidden">
                                        <label :for="'expense-file-' + item.id"
                                               class="cursor-pointer text-xs text-blue-600 hover:text-blue-800">
                                            📎 Upload
                                        </label>
                                        <span x-show="item.is_gst_free" class="text-xs text-gray-500">GST Free</span>
                                        <template x-if="item.attachments && item.attachments.length > 0">
                                            <div class="space-y-1">
                                                <template x-for="attachment in item.attachments" :key="attachment.id">
                                                    <div class="flex items-center gap-1">
                                                        <a :href="'/api/work-sections/attachments/' + attachment.id + '/download'"
                                                           target="_blank"
                                                           class="text-xs text-green-600 hover:text-green-800 truncate max-w-[150px]"
                                                           :title="attachment.original_filename"
                                                           x-text="attachment.original_filename"></a>
                                                        <button type="button"
                                                                @click="deleteAttachment(attachment.id, item, 'expense')"
                                                                class="text-red-500 hover:text-red-700">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            @click="deleteExpense(item.id)"
                                            class="text-red-600 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <tr x-show="!isAddingExpense && expenses.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                No expenses <span x-show="activeTab !== 'all'" x-text="'for ' + getCurrentTabLabel()"></span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-indigo-50 font-semibold">
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
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-6 border-2 border-indigo-300">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Company Tax Summary</h4>
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
                    :class="netProfit >= 0 ? 'text-indigo-600' : 'text-red-600'"
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
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                      placeholder="Add any notes or comments..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3"
                      x-model="internalComments"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                      placeholder="Add internal notes..."></textarea>
        </div>
    </div>
</div>

<script>
function ctaxSection(sectionId) {
    return {
        sectionId: sectionId,
        saving: false,
        activeTab: 'all',
        isAddingIncome: false,
        isAddingExpense: false,

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

        // Form objects
        newIncome: {
            description: '',
            amount: 0,
            quarter: 'q1',
            file: null
        },
        newExpense: {
            type: 'operating',
            description: '',
            amountIncGst: 0,
            quarter: 'q1',
            isGstFree: false,
            file: null
        },

        // Initialize
        async init() {
            await this.loadData();
        },

        // Load data
        async loadData() {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            try {
                const quarter = this.activeTab === 'all' ? '' : this.activeTab;
                // Load income
                const incomeResponse = await fetch(
                    `/api/work-sections/${this.sectionId}/income${quarter ? '?quarter=' + quarter : ''}`,
                    {
                        headers
                    }
                );

                if (incomeResponse.ok) {
                    const incomeData = await incomeResponse.json();
                    this.income = incomeData.income.map(item => ({
                        id: item.id,
                        description: item.description,
                        amount: parseFloat(item.amount || 0),
                        quarter: item.quarter,
                        attachments: item.attachments || []
                    }));
                }

                // Load expenses
                const expenseResponse = await fetch(
                    `/api/work-sections/${this.sectionId}/expenses${quarter ? '?quarter=' + quarter : ''}`,
                    {
                        headers
                    }
                );

                if (expenseResponse.ok) {
                    const expenseData = await expenseResponse.json();
                    this.expenses = expenseData.expenses.map(item => ({
                        id: item.id,
                        type: item.type,
                        description: item.description,
                        amount_inc_gst: parseFloat(item.amount_inc_gst || 0),
                        gst_amount: parseFloat(item.gst_amount || 0),
                        net_ex_gst: parseFloat(item.net_ex_gst || 0),
                        quarter: item.quarter,
                        is_gst_free: item.is_gst_free || false,
                        attachments: item.attachments || []
                    }));
                }

            } catch (error) {
                console.error('Failed to load data:', error);
            }
        },

        // Tab management
        async changeTab(tab) {
            this.activeTab = tab;
            await this.loadData();
        },

        // Computed properties
        get totalIncome() {
            return this.income.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
        },

        get totalExpensesIncGst() {
            return this.expenses.reduce((sum, item) => sum + parseFloat(item.amount_inc_gst || 0), 0);
        },

        get totalExpensesGst() {
            return this.expenses.reduce((sum, item) => sum + parseFloat(item.gst_amount || 0), 0);
        },

        get totalExpensesNet() {
            return this.expenses.reduce((sum, item) => sum + parseFloat(item.net_ex_gst || 0), 0);
        },

        get netProfit() {
            return this.totalIncome - this.totalExpensesNet;
        },

        // Helper functions
        formatCurrency(number) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'AUD',
            }).format(number || 0);
        },

        detectAuth() {
            const guestInput = document.getElementById('access_token');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');

            const headers = {
                Accept: 'application/json'
            };

            // ✅ GUEST TOKEN FIRST
            if (guestInput?.value) {
                headers['X-ACCESS-TOKEN'] = guestInput.value;
                return headers;
            }

            // ✅ AUTHENTICATED USER SECOND
            if (csrfMeta?.content) {
                headers['X-CSRF-TOKEN'] = csrfMeta.content;
                return headers;
            }

            console.error('No valid auth token found');
            return headers;
        },

        calculateGST(amount, isGstFree) {
            if (isGstFree) return 0;
            return amount / 11;
        },

        calculateExGST(amount, isGstFree) {
            if (isGstFree) return amount;
            return amount - this.calculateGST(amount, isGstFree);
        },

        getQuarterLabel(quarter) {
            const tab = this.tabs.find(t => t.value === quarter);
            return tab ? tab.label : quarter;
        },

        getCurrentTabLabel() {
            const tab = this.tabs.find(t => t.value === this.activeTab);
            return tab ? tab.label : '';
        },

        // Income management
        startAddingIncome() {
            this.isAddingIncome = true;
            this.newIncome = {
                description: '',
                amount: 0,
                quarter: this.activeTab === 'all' ? 'q1' : this.activeTab,
                file: null
            };
        },

        cancelAddingIncome() {
            this.isAddingIncome = false;
            this.resetIncomeForm();
        },

        async saveIncome() {
        const headers = this.detectAuth();
        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';
            this.saving = true;
            try {
                if (!this.newIncome.description || this.newIncome.description.trim() === '') {
                    throw new Error('Description is required');
                }

                if (this.newIncome.amount <= 0) {
                    throw new Error('Amount must be greater than zero');
                }

                const response = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'ctax',
                        description: this.newIncome.description,
                        amount: Number(this.newIncome.amount),
                        quarter: this.newIncome.quarter
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save income');
                }

                // Upload file if selected
                if (this.newIncome.file) {
                    await this.uploadFileForNewEntry(data.income.id, this.newIncome.file, 'income');
                }

                // Add to local array
                this.income.push({
                    id: data.income.id,
                    description: data.income.description,
                    amount: parseFloat(data.income.amount),
                    quarter: data.income.quarter,
                    attachments: []
                });

                this.isAddingIncome = false;
                this.resetIncomeForm();

            } catch (error) {
                console.error('Save error:', error);
                alert(error.message);
            } finally {
                this.saving = false;
            }
        },

        async deleteIncome(id) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!confirm('Are you sure you want to delete this income item?')) {
                return;
            }
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/income/${id}`, {
                    method: 'DELETE',
                    headers,
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete income');
                }

                this.income = this.income.filter(item => item.id !== id);

            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message);
            }
        },

        resetIncomeForm() {
            this.newIncome = {
                description: '',
                amount: 0,
                quarter: 'q1',
                file: null
            };
            const fileInput = document.getElementById('newIncomeFile');
            if (fileInput) fileInput.value = '';
        },

        // Expense management
        startAddingExpense() {
            this.isAddingExpense = true;
            this.newExpense = {
                type: 'operating',
                description: '',
                amountIncGst: 0,
                quarter: this.activeTab === 'all' ? 'q1' : this.activeTab,
                isGstFree: false,
                file: null
            };
        },

        cancelAddingExpense() {
            this.isAddingExpense = false;
            this.resetExpenseForm();
        },

        async saveExpense() {
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';
            this.saving = true;
            try {
                if (!this.newExpense.description || this.newExpense.description.trim() === '') {
                    throw new Error('Description is required');
                }

                if (this.newExpense.amountIncGst <= 0) {
                    throw new Error('Amount must be greater than zero');
                }

                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'ctax',
                        type: this.newExpense.type,
                        description: this.newExpense.description,
                        amount_inc_gst: Number(this.newExpense.amountIncGst),
                        is_gst_free: Boolean(this.newExpense.isGstFree),
                        quarter: this.newExpense.quarter
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save expense');
                }

                // Upload file if selected
                if (this.newExpense.file) {
                    await this.uploadFileForNewEntry(data.expense.id, this.newExpense.file, 'expense');
                }

                // Add to local array
                this.expenses.push({
                    id: data.expense.id,
                    type: data.expense.type,
                    description: data.expense.description,
                    amount_inc_gst: parseFloat(data.expense.amount_inc_gst),
                    gst_amount: parseFloat(data.expense.gst_amount),
                    net_ex_gst: parseFloat(data.expense.net_ex_gst),
                    quarter: data.expense.quarter,
                    is_gst_free: data.expense.is_gst_free,
                    attachments: []
                });

                this.isAddingExpense = false;
                this.resetExpenseForm();

            } catch (error) {
                console.error('Save error:', error);
                alert(error.message);
            } finally {
                this.saving = false;
            }
        },

        async deleteExpense(id) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!confirm('Are you sure you want to delete this expense?')) {
                return;
            }
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses/${id}`, {
                    method: 'DELETE',
                    headers
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete expense');
                }

                this.expenses = this.expenses.filter(item => item.id !== id);

            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message);
            }
        },

        resetExpenseForm() {
            this.newExpense = {
                type: 'operating',
                description: '',
                amountIncGst: 0,
                quarter: 'q1',
                isGstFree: false,
                file: null
            };
            const fileInput = document.getElementById('newExpenseFile');
            if (fileInput) fileInput.value = '';
        },

        // File upload
        async uploadIncomeFile(incomeId, event) {
            const file = event.target.files[0];
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            try {
                const response = await fetch(`/api/work-sections/income/${incomeId}/upload`, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'File upload failed');
                }

                // Update attachments
                const incomeIndex = this.income.findIndex(i => i.id === incomeId);
                if (incomeIndex !== -1) {
                    if (!this.income[incomeIndex].attachments) {
                        this.income[incomeIndex].attachments = [];
                    }
                    this.income[incomeIndex].attachments.push(data.attachment);
                }

                event.target.value = '';
                alert('File uploaded successfully');

            } catch (error) {
                console.error('Upload error:', error);
                alert(error.message);
            }
        },

        async uploadExpenseFile(expenseId, event) {
            const file = event.target.files[0];
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            try {
                const response = await fetch(`/api/work-sections/expenses/${expenseId}/upload`, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'File upload failed');
                }

                // Update attachments
                const expenseIndex = this.expenses.findIndex(e => e.id === expenseId);
                if (expenseIndex !== -1) {
                    if (!this.expenses[expenseIndex].attachments) {
                        this.expenses[expenseIndex].attachments = [];
                    }
                    this.expenses[expenseIndex].attachments.push(data.attachment);
                }

                event.target.value = '';
                alert('File uploaded successfully');

            } catch (error) {
                console.error('Upload error:', error);
                alert(error.message);
            }
        },

        async uploadFileForNewEntry(entryId, file, entryType) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            const formData = new FormData();
            formData.append('file', file);

            try {
                const endpoint = entryType === 'income'
                    ? `/api/work-sections/income/${entryId}/upload`
                    : `/api/work-sections/expenses/${entryId}/upload`;

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'File upload failed');
                }

                // Update the entry with the attachment
                const array = entryType === 'income' ? this.income : this.expenses;
                const index = array.findIndex(item => item.id === entryId);
                if (index !== -1) {
                    if (!array[index].attachments) {
                        array[index].attachments = [];
                    }
                    array[index].attachments.push(data.attachment);
                }

            } catch (error) {
                console.error('Upload error:', error);
            }
        },

        async deleteAttachment(attachmentId, item, itemType) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!confirm('Are you sure you want to delete this file?')) {
                return;
            }

            try {
                const response = await fetch(`/api/work-sections/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete file');
                }

                // Remove from local array
                if (item.attachments) {
                    item.attachments = item.attachments.filter(att => att.id !== attachmentId);
                }

                alert('File deleted successfully');

            } catch (error) {
                console.error('Delete attachment error:', error);
                alert(error.message);
            }
        }
    }
}
</script>
