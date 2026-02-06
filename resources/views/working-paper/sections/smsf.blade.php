{{-- SMSF Section - Self-Managed Super Fund --}}
<div class="space-y-6" x-data="smsfSection({{ $section->id }})" x-init="init()">
    <p class="text-sm text-gray-600 italic bg-teal-50 p-3 rounded border-l-4 border-teal-500">
        Self-Managed Super Fund income and expense tracking with quarterly breakdowns
    </p>

    {{-- Quarter Filter --}}
    <div class="flex justify-between items-center p-4 bg-teal-100 rounded-lg border border-teal-300">
        <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">View Quarter:</label>
            <select x-model="selectedQuarter" @change="filterByQuarter" class="rounded-md border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="all">All Quarters</option>
                <option value="q1">Q1 (Jul-Sep)</option>
                <option value="q2">Q2 (Oct-Dec)</option>
                <option value="q3">Q3 (Jan-Mar)</option>
                <option value="q4">Q4 (Apr-Jun)</option>
            </select>
        </div>
    </div>

    {{-- Income Section --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">SMSF Income</h4>
            <button type="button"
                    @click="showIncomeForm = !showIncomeForm"
                    class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                <span x-show="!showIncomeForm">+ Add Income</span>
                <span x-show="showIncomeForm">Cancel</span>
            </button>
        </div>

        {{-- Add Income Form --}}
        <div x-show="showIncomeForm" x-transition class="mb-4 bg-teal-50 p-4 rounded-lg border border-teal-200">
            <form @submit.prevent="saveIncome">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Income Type *</label>
                        <select x-model="newIncome.type" required class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Select Type</option>
                            <option value="contributions">Contributions</option>
                            <option value="interest">Interest</option>
                            <option value="dividends">Dividends</option>
                            <option value="rental">Rental Income</option>
                            <option value="capital_gains">Capital Gains</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" x-model="newIncome.description" class="w-full rounded-md border-gray-300 text-sm" placeholder="Optional details">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" step="0.01" x-model="newIncome.amount" required class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quarter *</label>
                        <select x-model="newIncome.quarter" required class="w-full rounded-md border-gray-300 text-sm">
                            <option value="q1">Q1 (Jul-Sep)</option>
                            <option value="q2">Q2 (Oct-Dec)</option>
                            <option value="q3">Q3 (Jan-Mar)</option>
                            <option value="q4">Q4 (Apr-Jun)</option>
                            <option value="all">All Year</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 flex justify-end space-x-2">
                    <button type="button" @click="showIncomeForm = false; resetIncomeForm()" class="px-3 py-1 border rounded-md text-xs">Cancel</button>
                    <button type="submit" :disabled="saving" class="px-3 py-1 bg-teal-600 text-white rounded-md text-xs disabled:opacity-50">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Income Table --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Quarter</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Attachments</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="filteredIncome.length === 0">
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No income entries</td>
                            </tr>
                        </template>
                        <template x-for="income in filteredIncome" :key="income.id">
                            <tr>
                                <td class="px-3 py-2 text-xs capitalize" x-text="income.label.replace('_', ' ')"></td>
                                <td class="px-3 py-2 text-xs" x-text="income.description || '-'"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="'$' + parseFloat(income.amount || 0).toFixed(2)"></td>
                                <td class="px-3 py-2 text-xs uppercase" x-text="income.quarter"></td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-col gap-1">
                                        <template x-if="income.attachments && income.attachments.length > 0">
                                            <template x-for="attachment in income.attachments" :key="attachment.id">
                                                <a :href="`/api/work-sections/attachments/${attachment.id}/download`"
                                                   target="_blank"
                                                   class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 group">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="truncate max-w-[120px] group-hover:underline" :title="attachment.original_filename" x-text="attachment.original_filename"></span>
                                                </a>
                                            </template>
                                        </template>
                                        <button type="button" @click="uploadFile(income.id, 'income')" class="text-xs text-teal-600 hover:text-teal-700 text-left">
                                            + Upload
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button @click="deleteIncome(income.id)" class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-teal-50 font-semibold">
                        <tr>
                            <td colspan="2" class="px-3 py-2 text-xs">Total Income</td>
                            <td class="px-3 py-2 text-xs text-right" x-text="'$' + incomeTotal.toFixed(2)"></td>
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
            <h4 class="text-md font-semibold text-gray-800">SMSF Expenses</h4>
            <button type="button"
                    @click="showExpenseForm = !showExpenseForm"
                    class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                <span x-show="!showExpenseForm">+ Add Expense</span>
                <span x-show="showExpenseForm">Cancel</span>
            </button>
        </div>

        {{-- Add Expense Form --}}
        <div x-show="showExpenseForm" x-transition class="mb-4 bg-teal-50 p-4 rounded-lg border border-teal-200">
            <form @submit.prevent="saveExpense">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select x-model="newExpense.type" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="admin">Administration</option>
                            <option value="audit">Audit Fees</option>
                            <option value="compliance">Compliance</option>
                            <option value="investment">Investment Fees</option>
                            <option value="insurance">Insurance</option>
                            <option value="legal">Legal Fees</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                        <input type="text" x-model="newExpense.description" required class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Field Type (A/B/C) *</label>
                        <select x-model="newExpense.fieldType" required class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Select</option>
                            <option value="A">A - Deductible</option>
                            <option value="B">B - Non-Deductible</option>
                            <option value="C">C - Capital</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount (Inc GST) *</label>
                        <input type="number" step="0.01" x-model="newExpense.amount" required class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quarter *</label>
                        <select x-model="newExpense.quarter" required class="w-full rounded-md border-gray-300 text-sm">
                            <option value="q1">Q1 (Jul-Sep)</option>
                            <option value="q2">Q2 (Oct-Dec)</option>
                            <option value="q3">Q3 (Jan-Mar)</option>
                            <option value="q4">Q4 (Apr-Jun)</option>
                            <option value="all">All Year</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center text-xs">
                            <input type="checkbox" x-model="newExpense.isGstFree" class="rounded border-gray-300 text-teal-600">
                            <span class="ml-2">GST Free</span>
                        </label>
                    </div>
                    <div x-show="!newExpense.isGstFree && newExpense.amount > 0" class="md:col-span-3 text-xs bg-blue-50 p-2 rounded">
                        <strong>Auto-calculated:</strong>
                        GST: $<span x-text="(newExpense.amount / 11).toFixed(2)"></span>,
                        Net: $<span x-text="(newExpense.amount - (newExpense.amount / 11)).toFixed(2)"></span>
                    </div>
                </div>
                <div class="mt-3 flex justify-end space-x-2">
                    <button type="button" @click="showExpenseForm = false; resetExpenseForm()" class="px-3 py-1 border rounded-md text-xs">Cancel</button>
                    <button type="submit" :disabled="saving" class="px-3 py-1 bg-teal-600 text-white rounded-md text-xs disabled:opacity-50">
                        <span x-show="!saving">Save</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Expenses Table --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Field</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Inc GST</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">GST</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Net</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Quarter</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Attachments</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="filteredExpenses.length === 0">
                            <tr>
                                <td colspan="9" class="px-3 py-8 text-center text-sm text-gray-500">No expenses added yet</td>
                            </tr>
                        </template>
                        <template x-for="expense in filteredExpenses" :key="expense.id">
                            <tr>
                                <td class="px-3 py-2 text-xs capitalize" x-text="expense.type || 'N/A'"></td>
                                <td class="px-3 py-2 text-xs" x-text="expense.description"></td>
                                <td class="px-3 py-2 text-xs uppercase">
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded font-semibold" x-text="expense.field_type || '-'"></span>
                                </td>
                                <td class="px-3 py-2 text-xs text-right" x-text="'$' + parseFloat(expense.amount_inc_gst || 0).toFixed(2)"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="'$' + parseFloat(expense.gst_amount || 0).toFixed(2)"></td>
                                <td class="px-3 py-2 text-xs text-right" x-text="'$' + parseFloat(expense.net_ex_gst || 0).toFixed(2)"></td>
                                <td class="px-3 py-2 text-xs uppercase" x-text="expense.quarter"></td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-col gap-1">
                                        <template x-if="expense.attachments && expense.attachments.length > 0">
                                            <template x-for="attachment in expense.attachments" :key="attachment.id">
                                                <a :href="`/api/work-sections/attachments/${attachment.id}/download`"
                                                   target="_blank"
                                                   class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 group">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="truncate max-w-[100px] group-hover:underline" :title="attachment.original_filename" x-text="attachment.original_filename"></span>
                                                </a>
                                            </template>
                                        </template>
                                        <template x-if="!expense.attachments || expense.attachments.length === 0">
                                            <span class="text-xs text-red-500">⚠️ Required</span>
                                        </template>
                                        <button type="button" @click="uploadFile(expense.id, 'expense')" class="text-xs text-teal-600 hover:text-teal-700 text-left">
                                            + Upload
                                        </button>
                                    </div>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="bg-teal-50 font-semibold">
                        <tr>
                            <td colspan="3" class="px-3 py-2 text-xs">Total Expenses</td>
                            <td class="px-3 py-2 text-xs text-right" x-text="'$' + expenseTotal.incGst.toFixed(2)"></td>
                            <td class="px-3 py-2 text-xs text-right" x-text="'$' + expenseTotal.gst.toFixed(2)"></td>
                            <td class="px-3 py-2 text-xs text-right" x-text="'$' + expenseTotal.net.toFixed(2)"></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- SMSF Summary --}}
    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg p-6 border-2 border-teal-400">
        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            SMSF Summary
            <span x-show="selectedQuarter !== 'all'" class="ml-2 text-sm text-teal-600" x-text="'(' + selectedQuarter.toUpperCase() + ')'"></span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Total Income</div>
                <div class="text-2xl font-bold text-green-600" x-text="'$' + incomeTotal.toFixed(2)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Total Expenses (Net)</div>
                <div class="text-2xl font-bold text-red-600" x-text="'$' + expenseTotal.net.toFixed(2)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Net Position</div>
                <div class="text-2xl font-bold" :class="(incomeTotal - expenseTotal.net) >= 0 ? 'text-teal-600' : 'text-red-600'" x-text="'$' + (incomeTotal - expenseTotal.net).toFixed(2)"></div>
            </div>
        </div>
    </div>

    {{-- Hidden File Input --}}
    <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="hidden">
</div>

<script>
function smsfSection(sectionId) {
    return {
        sectionId,
        saving: false,
        selectedQuarter: 'all',
        showIncomeForm: false,
        showExpenseForm: false,
        incomes: [],
        expenses: [],
        currentFileId: null,
        currentFileType: null,
        newIncome: {
            type: '',
            description: '',
            amount: '',
            quarter: 'all'
        },
        newExpense: {
            type: 'admin',
            description: '',
            fieldType: '',
            amount: '',
            quarter: 'all',
            isGstFree: false
        },

        get filteredIncome() {
            if (this.selectedQuarter === 'all') return this.incomes;
            return this.incomes.filter(i => i.quarter === this.selectedQuarter || i.quarter === 'all');
        },

        get filteredExpenses() {
            if (this.selectedQuarter === 'all') return this.expenses;
            return this.expenses.filter(e => e.quarter === this.selectedQuarter || e.quarter === 'all');
        },

        get incomeTotal() {
            return this.filteredIncome.reduce((sum, income) => sum + parseFloat(income.amount || 0), 0);
        },

        get expenseTotal() {
            return this.filteredExpenses.reduce((acc, expense) => ({
                incGst: acc.incGst + parseFloat(expense.amount_inc_gst || 0),
                gst: acc.gst + parseFloat(expense.gst_amount || 0),
                net: acc.net + parseFloat(expense.net_ex_gst || 0)
            }), { incGst: 0, gst: 0, net: 0 });
        },

        async init() {
            await this.loadData();
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

        async loadData() {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';

            try {
                // Load income (label='smsf')
                const incomeRes = await fetch(`/api/work-sections/${this.sectionId}/income?label=smsf`, { headers });
                if (incomeRes.ok) {
                    const data = await incomeRes.json();
                    this.incomes = data.income;
                }

                // Load expenses (label='smsf')
                const expenseRes = await fetch(`/api/work-sections/${this.sectionId}/expenses?label=smsf`, { headers });
                if (expenseRes.ok) {
                    const data = await expenseRes.json();
                    this.expenses = data.expenses;
                }
            } catch (error) {
                console.error('Load error:', error);
            }
        },

        filterByQuarter() {
            // Reactive property will auto-filter
        },

        async saveIncome() {
            this.saving = true;
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: this.newIncome.type, // Use type as label (contributions, interest, etc.)
                        description: this.newIncome.description || this.newIncome.type,
                        amount: this.newIncome.amount,
                        quarter: this.newIncome.quarter
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.incomes.push(data.income);
                    this.resetIncomeForm();
                    this.showIncomeForm = false;
                    this.showNotification('Income added');
                }
            } catch (error) {
                console.error('Save income error:', error);
                this.showNotification('Error saving income', 'error');
            } finally {
                this.saving = false;
            }
        },

        async saveExpense() {
            if (!this.newExpense.fieldType) {
                this.showNotification('Field Type (A/B/C) is required', 'error');
                return;
            }

            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';

            this.saving = true;
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'smsf',
                        description: this.newExpense.description,
                        type: this.newExpense.type,
                        field_type: this.newExpense.fieldType,
                        amount_inc_gst: this.newExpense.amount,
                        is_gst_free: this.newExpense.isGstFree,
                        quarter: this.newExpense.quarter
                    })
                });

                // Handle the response
                if (res.status === 422) {
                    const errors = await res.json();
                    console.error('Validation errors:', errors);
                    this.showNotification('Validation failed', 'error');
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    this.expenses.push(data.expense);
                    this.resetExpenseForm();
                    this.showExpenseForm = false;
                    this.showNotification('Expense added - Please upload receipt');
                }
            } catch (error) {
                console.error('Save expense error:', error);
                this.showNotification('Error saving expense', 'error');
            } finally {
                this.saving = false;
            }
        },

        async deleteIncome(incomeId) {
            if (!confirm('Delete this income?')) return;
            const headers = this.detectAuth();

            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/income/${incomeId}`, {
                    method: 'DELETE',
                    headers
                });

                if ((await res.json()).success) {
                    this.incomes = this.incomes.filter(i => i.id !== incomeId);
                    this.showNotification('Income deleted');
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
        },

        async deleteExpense(expenseId) {
            if (!confirm('Delete this expense?')) return;
            const headers = this.detectAuth();

            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses/${expenseId}`, {
                    method: 'DELETE',
                    headers
                });

                if ((await res.json()).success) {
                    this.expenses = this.expenses.filter(e => e.id !== expenseId);
                    this.showNotification('Expense deleted');
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
        },

        uploadFile(itemId, type) {
            this.currentFileId = itemId;
            this.currentFileType = type;
            this.$refs.fileInput.click();
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const headers = this.detectAuth();
            const formData = new FormData();
            formData.append('file', file);

            try {
                const endpoint = this.currentFileType === 'income'
                    ? `/api/work-sections/income/${this.currentFileId}/upload`
                    : `/api/work-sections/expenses/${this.currentFileId}/upload`;

                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    this.showNotification('File uploaded');
                    await this.loadData();
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification('Upload failed', 'error');
            }

            event.target.value = '';
        },

        resetIncomeForm() {
            this.newIncome = { type: '', description: '', amount: '', quarter: 'all' };
        },

        resetExpenseForm() {
            this.newExpense = { type: 'admin', description: '', fieldType: '', amount: '', quarter: 'all', isGstFree: false };
        },

        showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-teal-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 300ms';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }
}
</script>
