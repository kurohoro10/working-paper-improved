{{-- Wage Section Partial - working-papers/partials/wage-section.blade.php --}}

<div class="space-y-6" x-data="wageSection({{ $section->id }})">
    {{-- Income Section --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">Income</h4>
            <button type="button"
                    @click="startAddingIncome"
                    x-show="!isAddingIncome"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium transition">
                + Add Income
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Salary/Wages</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax Withheld</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Add Income Form Row --}}
                        <tr x-show="isAddingIncome" class="bg-blue-50">
                            <td class="px-4 py-3">
                                <input type="text"
                                       x-model="newIncome.description"
                                       placeholder="e.g., ABC Company Ltd"
                                       class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       step="0.01"
                                       x-model.number="newIncome.salary"
                                       placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 text-sm text-right focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       step="0.01"
                                       x-model.number="newIncome.taxWithheld"
                                       placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 text-sm text-right focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="file"
                                       id="newIncomeFile"
                                       @change="newIncome.file = $event.target.files[0]"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
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
                                <td class="px-4 py-3 text-sm" x-text="item.description"></td>
                                <td class="px-4 py-3 text-sm text-right" x-text="'$' + parseFloat(item.amount || 0).toFixed(2)"></td>
                                <td class="px-4 py-3 text-sm text-right" x-text="getTaxWithheld(item)"></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <input type="file"
                                               :id="'income-file-' + item.id"
                                               @change="uploadIncomeFile(item.id, $event)"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
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
                                            class="text-red-600 hover:text-red-700">
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
                                No income entries
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-sm">Total</td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalIncome.toFixed(2)"></td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalTaxWithheld.toFixed(2)"></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Work-Related Expenses --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">Work-Related Expenses</h4>
            <button type="button"
                    @click="startAddingExpense"
                    x-show="!isAddingExpense"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium transition">
                + Add Expense
            </button>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Upload</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Add Expense Form Row --}}
                        <tr x-show="isAddingExpense" class="bg-blue-50">
                            <td class="px-4 py-3">
                                <input type="text"
                                       x-model="newExpense.description"
                                       placeholder="e.g., Car expenses, Home office"
                                       class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                       step="0.01"
                                       x-model.number="newExpense.amount"
                                       placeholder="0.00"
                                       class="w-full rounded-md border-gray-300 text-sm text-right focus:border-blue-500 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <select x-model="newExpense.quarter"
                                        class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="all">All Year</option>
                                    <option value="q1">Q1 (Jul-Sep)</option>
                                    <option value="q2">Q2 (Oct-Dec)</option>
                                    <option value="q3">Q3 (Jan-Mar)</option>
                                    <option value="q4">Q4 (Apr-Jun)</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="file"
                                       id="newExpenseFile"
                                       @change="newExpense.file = $event.target.files[0]"
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                       class="text-xs w-full">
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
                                <td class="px-4 py-3 text-sm" x-text="item.description"></td>
                                <td class="px-4 py-3 text-sm text-right" x-text="'$' + parseFloat(item.amount_inc_gst || 0).toFixed(2)"></td>
                                <td class="px-4 py-3 text-sm" x-text="formatQuarter(item.quarter)"></td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <input type="file"
                                               :id="'expense-file-' + item.id"
                                               @change="uploadExpenseFile(item.id, $event)"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                               class="hidden">
                                        <label :for="'expense-file-' + item.id"
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
                                            class="text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <tr x-show="!isAddingExpense && expenses.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                No expenses added yet
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-sm">Total</td>
                            <td class="px-4 py-3 text-sm text-right" x-text="'$' + totalExpenses.toFixed(2)"></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border-2 border-blue-300">
        <h4 class="text-lg font-bold text-gray-900 mb-4">Wage Summary</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Income</div>
                <div class="text-2xl font-bold text-green-600" x-text="'$' + totalIncome.toFixed(2)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Tax Withheld</div>
                <div class="text-2xl font-bold text-orange-600" x-text="'$' + totalTaxWithheld.toFixed(2)"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Work Expenses</div>
                <div class="text-2xl font-bold text-red-600" x-text="'$' + totalExpenses.toFixed(2)"></div>
            </div>
        </div>
    </div>
</div>

<script>
function wageSection(sectionId) {
    return {
        sectionId: sectionId,
        saving: false,
        isAddingIncome: false,
        isAddingExpense: false,

        income: [],
        expenses: [],

        newIncome: {
            description: '',
            salary: 0,
            taxWithheld: 0,
            file: null
        },

        newExpense: {
            description: '',
            amount: 0,
            quarter: 'all',
            file: null
        },

        // Initialize
        async init() {
            await this.loadData();
        },

        // Load data
        async loadData() {
            try {
                // Load income
                const incomeResponse = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (incomeResponse.ok) {
                    const incomeData = await incomeResponse.json();
                    this.income = incomeData.income.map(item => ({
                        id: item.id,
                        description: item.description,
                        amount: parseFloat(item.amount || 0),
                        client_comment: item.client_comment,
                        attachments: item.attachments || []
                    }));
                }

                // Load expenses
                const expenseResponse = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (expenseResponse.ok) {
                    const expenseData = await expenseResponse.json();
                    this.expenses = expenseData.expenses.map(item => ({
                        id: item.id,
                        description: item.description,
                        amount_inc_gst: parseFloat(item.amount_inc_gst || 0),
                        quarter: item.quarter,
                        attachments: item.attachments || []
                    }));
                }

            } catch (error) {
                console.error('Failed to load data:', error);
            }
        },

        // Computed properties
        get totalIncome() {
            return this.income.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0);
        },

        get totalTaxWithheld() {
            return this.income.reduce((sum, item) => {
                const taxWithheld = this.extractTaxWithheld(item.client_comment);
                return sum + taxWithheld;
            }, 0);
        },

        get totalExpenses() {
            return this.expenses.reduce((sum, item) => sum + parseFloat(item.amount_inc_gst || 0), 0);
        },

        // Helper functions
        getTaxWithheld(item) {
            const taxWithheld = this.extractTaxWithheld(item.client_comment);
            return '$' + taxWithheld.toFixed(2);
        },

        extractTaxWithheld(comment) {
            if (!comment) return 0;
            const match = comment.match(/Tax Withheld:\s*\$?([\d,]+\.?\d*)/i);
            if (match) {
                return parseFloat(match[1].replace(/,/g, ''));
            }
            return 0;
        },

        formatQuarter(quarter) {
            const quarters = {
                'all': 'All Year',
                'q1': 'Q1 (Jul-Sep)',
                'q2': 'Q2 (Oct-Dec)',
                'q3': 'Q3 (Jan-Mar)',
                'q4': 'Q4 (Apr-Jun)'
            };
            return quarters[quarter] || quarter;
        },

        // Income management
        startAddingIncome() {
            this.isAddingIncome = true;
            this.newIncome = { description: '', salary: 0, taxWithheld: 0, file: null };
        },

        cancelAddingIncome() {
            this.isAddingIncome = false;
            this.resetIncomeForm();
        },

        async saveIncome() {
            this.saving = true;

            try {
                if (!this.newIncome.description || this.newIncome.description.trim() === '') {
                    throw new Error('Description is required');
                }

                if (this.newIncome.salary <= 0) {
                    throw new Error('Salary must be greater than zero');
                }

                const response = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        label: 'wage',
                        description: this.newIncome.description,
                        amount: Number(this.newIncome.salary),
                        client_comment: `Tax Withheld: $${this.newIncome.taxWithheld}`,
                        quarter: 'all'
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
                    client_comment: data.income.client_comment,
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
            if (!confirm('Are you sure you want to delete this income item?')) {
                return;
            }

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/income/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
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
            this.newIncome = { description: '', salary: 0, taxWithheld: 0, file: null };
            const fileInput = document.getElementById('newIncomeFile');
            if (fileInput) fileInput.value = '';
        },

        // Expense management
        startAddingExpense() {
            this.isAddingExpense = true;
            this.newExpense = { description: '', amount: 0, quarter: 'all', file: null };
        },

        cancelAddingExpense() {
            this.isAddingExpense = false;
            this.resetExpenseForm();
        },

        async saveExpense() {
            this.saving = true;

            try {
                if (!this.newExpense.description || this.newExpense.description.trim() === '') {
                    throw new Error('Description is required');
                }

                if (this.newExpense.amount <= 0) {
                    throw new Error('Amount must be greater than zero');
                }

                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        label: 'wage',
                        description: this.newExpense.description,
                        amount_inc_gst: Number(this.newExpense.amount),
                        is_gst_free: true, // Wage expenses are typically GST-free
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
                    description: data.expense.description,
                    amount_inc_gst: parseFloat(data.expense.amount_inc_gst),
                    quarter: data.expense.quarter,
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
            if (!confirm('Are you sure you want to delete this expense?')) {
                return;
            }

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
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
            this.newExpense = { description: '', amount: 0, quarter: 'all', file: null };
            const fileInput = document.getElementById('newExpenseFile');
            if (fileInput) fileInput.value = '';
        },

        // File upload
        async uploadIncomeFile(incomeId, event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch(`/api/work-sections/income/${incomeId}/upload`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
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
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch(`/api/work-sections/expenses/${expenseId}/upload`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
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
            const formData = new FormData();
            formData.append('file', file);

            try {
                const endpoint = entryType === 'income'
                    ? `/api/work-sections/income/${entryId}/upload`
                    : `/api/work-sections/expenses/${entryId}/upload`;

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
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
            if (!confirm('Are you sure you want to delete this file?')) {
                return;
            }

            try {
                const response = await fetch(`/api/work-sections/attachments/${attachmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
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
