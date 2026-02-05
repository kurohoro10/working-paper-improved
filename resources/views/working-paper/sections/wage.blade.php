{{-- Wage Section with Alpine.js and API Integration --}}
<div class="space-y-6" x-data="wageSection({{ $section->id }})" x-init="init()">
    {{-- Income Section --}}
    <div>
        <h4 class="text-md font-semibold text-gray-800 mb-3">Income</h4>
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <form @submit.prevent="saveIncome">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salary/Wages</label>
                        <input type="number"
                               step="0.01"
                               x-model="incomeForm.salary"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Withheld</label>
                        <input type="number"
                               step="0.01"
                               x-model="incomeForm.taxWithheld"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload PAYG Summary (Optional)</label>
                    <input type="file"
                           @change="handleIncomeFileUpload($event)"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            :disabled="saving"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!saving">Save Income</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Work-Related Expenses --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h4 class="text-md font-semibold text-gray-800">Work-Related Expenses</h4>
            <button type="button"
                    @click="showExpenseForm = !showExpenseForm"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                <span x-show="!showExpenseForm">+ Add Expense</span>
                <span x-show="showExpenseForm">Cancel</span>
            </button>
        </div>

        {{-- Add Expense Form --}}
        <div x-show="showExpenseForm"
             x-transition
             class="mb-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
            <form @submit.prevent="addExpense">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text"
                               x-model="newExpense.description"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., Car expenses, Home office, Tools">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                        <input type="number"
                               step="0.01"
                               x-model="newExpense.amount"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                        <select x-model="newExpense.quarter"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all">All Year</option>
                            <option value="q1">Q1 (Jul-Sep)</option>
                            <option value="q2">Q2 (Oct-Dec)</option>
                            <option value="q3">Q3 (Jan-Mar)</option>
                            <option value="q4">Q4 (Apr-Jun)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea x-model="newExpense.client_comment"
                                  rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button"
                            @click="showExpenseForm = false; resetExpenseForm()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="saving"
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!saving">Add Expense</span>
                        <span x-show="saving">Adding...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Expenses Table --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarter</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attachments</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-if="expenses.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                No expenses added yet
                            </td>
                        </tr>
                    </template>
                    <template x-for="expense in expenses" :key="expense.id">
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900" x-text="expense.description"></td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="'$' + parseFloat(expense.amount_inc_gst).toFixed(2)"></td>
                            <td class="px-4 py-3 text-sm text-gray-500" x-text="formatQuarter(expense.quarter)"></td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-2">
                                    <span x-show="expense.attachments && expense.attachments.length > 0"
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                          x-text="expense.attachments.length + ' file(s)'"></span>
                                    <button type="button"
                                            @click="uploadFile(expense.id)"
                                            class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Upload
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        @click="deleteExpense(expense.id)"
                                        class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
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

    {{-- File Upload Modal (Hidden file input) --}}
    <input type="file"
           x-ref="fileInput"
           @change="handleFileUpload($event)"
           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
           class="hidden">
</div>

@push('scripts')
<script>
function wageSection(sectionId) {
    return {
        sectionId: sectionId,
        expenses: [],
        showExpenseForm: false,
        saving: false,
        currentExpenseId: null,

        incomeForm: {
            salary: '',
            taxWithheld: ''
        },

        newExpense: {
            description: '',
            amount: '',
            quarter: 'all',
            client_comment: ''
        },

        get totalExpenses() {
            return this.expenses.reduce((sum, expense) => {
                return sum + parseFloat(expense.amount_inc_gst || 0);
            }, 0);
        },

        init() {
            this.loadExpenses();
        },

        async loadExpenses() {
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`);
                const data = await response.json();
                if (data.success) {
                    this.expenses = data.expenses;
                }
            } catch (error) {
                console.error('Error loading expenses:', error);
            }
        },

        async saveIncome() {
            this.saving = true;
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        description: 'Salary/Wages',
                        amount: this.incomeForm.salary,
                        client_comment: `Tax Withheld: $${this.incomeForm.taxWithheld}`
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Income saved successfully', 'success');
                } else {
                    this.showNotification('Failed to save income', 'error');
                }
            } catch (error) {
                console.error('Error saving income:', error);
                this.showNotification('An error occurred', 'error');
            } finally {
                this.saving = false;
            }
        },

        async addExpense() {
            this.saving = true;
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        description: this.newExpense.description,
                        amount_inc_gst: this.newExpense.amount,
                        quarter: this.newExpense.quarter,
                        client_comment: this.newExpense.client_comment,
                        is_gst_free: true // Wage expenses typically don't have GST
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.expenses.push(data.expense);
                    this.showNotification('Expense added successfully', 'success');
                    this.resetExpenseForm();
                    this.showExpenseForm = false;
                } else {
                    this.showNotification('Failed to add expense', 'error');
                }
            } catch (error) {
                console.error('Error adding expense:', error);
                this.showNotification('An error occurred', 'error');
            } finally {
                this.saving = false;
            }
        },

        async deleteExpense(expenseId) {
            if (!confirm('Are you sure you want to delete this expense?')) {
                return;
            }

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses/${expenseId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.expenses = this.expenses.filter(e => e.id !== expenseId);
                    this.showNotification('Expense deleted successfully', 'success');
                } else {
                    this.showNotification('Failed to delete expense', 'error');
                }
            } catch (error) {
                console.error('Error deleting expense:', error);
                this.showNotification('An error occurred', 'error');
            }
        },

        uploadFile(expenseId) {
            this.currentExpenseId = expenseId;
            this.$refs.fileInput.click();
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch(`/api/work-sections/expenses/${this.currentExpenseId}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    // Update expense attachments
                    const expense = this.expenses.find(e => e.id === this.currentExpenseId);
                    if (expense) {
                        if (!expense.attachments) {
                            expense.attachments = [];
                        }
                        expense.attachments.push(data.attachment);
                    }
                    this.showNotification('File uploaded successfully', 'success');
                } else {
                    this.showNotification('Failed to upload file', 'error');
                }
            } catch (error) {
                console.error('Error uploading file:', error);
                this.showNotification('An error occurred', 'error');
            }

            // Reset file input
            event.target.value = '';
        },

        async handleIncomeFileUpload(event) {
            // Similar to expense file upload but for income
            const file = event.target.files[0];
            if (!file) return;

            // Upload logic here
            console.log('Upload income file:', file);
        },

        resetExpenseForm() {
            this.newExpense = {
                description: '',
                amount: '',
                quarter: 'all',
                client_comment: ''
            };
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

        showNotification(message, type) {
            // Simple notification - you can enhance this
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endpush
