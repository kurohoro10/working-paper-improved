{{-- Rental Property Section Partial - working-papers/partials/rental-property-section.blade.php --}}

<div class="space-y-6" x-data="rentalPropertySection({{ $section->id }})">
    {{-- Add Property Button --}}
    <div class="flex justify-between items-center">
        <h4 class="text-md font-semibold text-gray-800">Rental Properties</h4>
        <button type="button"
                @click="addProperty"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Property
        </button>
    </div>

    {{-- Property List --}}
    <div class="space-y-6">
        <template x-for="(property, propIndex) in properties" :key="property.id">
            <div class="bg-white rounded-lg border-2 border-green-300 p-6">
                {{-- Property Header --}}
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <input type="text"
                               x-model="property.address_label"
                               @blur="updateProperty(propIndex)"
                               placeholder="Property Address/Nickname"
                               class="text-lg font-semibold border-0 border-b-2 border-gray-300 focus:border-green-500 focus:ring-0 w-full">
                    </div>
                    <button type="button"
                            @click="removeProperty(propIndex)"
                            class="ml-4 text-red-600 hover:text-red-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>

                {{-- Property Details --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-green-50 rounded-lg">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ownership %</label>
                        <input type="number"
                               x-model="property.ownership_percentage"
                               @change="updateProperty(propIndex)"
                               step="0.01"
                               max="100"
                               placeholder="100.00"
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Rented From</label>
                        <input type="date"
                               x-model="property.period_rented_from"
                               @change="updateProperty(propIndex)"
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Rented To</label>
                        <input type="date"
                               x-model="property.period_rented_to"
                               @change="updateProperty(propIndex)"
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>

                {{-- Income Section --}}
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="text-sm font-semibold text-gray-800">Rental Income</h5>
                        <button type="button"
                                @click="startAddingIncome(propIndex)"
                                x-show="!property.isAddingIncome"
                                class="text-xs text-green-600 hover:text-green-700 font-medium transition">
                            + Add Income
                        </button>
                    </div>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quarter</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    {{-- Add Income Form Row --}}
                                    <tr x-show="property.isAddingIncome" class="bg-blue-50">
                                        <td class="px-3 py-2">
                                            <input type="text"
                                                   x-model="property.newIncome.description"
                                                   placeholder="Description"
                                                   class="w-full rounded border-gray-300 text-xs">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number"
                                                   x-model.number="property.newIncome.amount"
                                                   placeholder="0.00"
                                                   step="0.01"
                                                   class="w-full rounded border-gray-300 text-xs text-right">
                                        </td>
                                        <td class="px-3 py-2">
                                            <select x-model="property.newIncome.quarter" class="w-full rounded border-gray-300 text-xs">
                                                <option value="q1">Q1</option>
                                                <option value="q2">Q2</option>
                                                <option value="q3">Q3</option>
                                                <option value="q4">Q4</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="file"
                                                   :id="'incomeFile-' + propIndex"
                                                   @change="property.newIncome.file = $event.target.files[0]"
                                                   class="w-full text-xs">
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex justify-center space-x-2">
                                                <button type="button"
                                                        @click="saveIncome(propIndex)"
                                                        :disabled="saving"
                                                        class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 disabled:opacity-50">
                                                    Save
                                                </button>
                                                <button type="button"
                                                        @click="cancelAddingIncome(propIndex)"
                                                        class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">
                                                    Cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Existing Income Items --}}
                                    <template x-for="(income, incIndex) in property.income" :key="income.id">
                                        <tr>
                                            <td class="px-3 py-2" x-text="income.description"></td>
                                            <td class="px-3 py-2 text-right" x-text="formatCurrency(income.amount)"></td>
                                            <td class="px-3 py-2 uppercase" x-text="income.quarter"></td>
                                            <td class="px-3 py-2">
                                                <template x-if="income.attachments && income.attachments.length > 0">
                                                    <a :href="'/attachments/' + income.attachments[0].id + '/download'"
                                                       target="_blank"
                                                       class="text-xs text-blue-600 hover:text-blue-800 underline"
                                                       x-text="income.attachments[0].original_filename"></a>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button type="button"
                                                        @click="deleteIncome(propIndex, incIndex, income.id)"
                                                        class="text-red-600 hover:text-red-800 text-xs">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </template>

                                    {{-- Empty State --}}
                                    <tr x-show="!property.isAddingIncome && property.income.length === 0">
                                        <td colspan="5" class="px-3 py-6 text-center text-xs text-gray-500">
                                            No income entries
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-green-50 font-semibold">
                                    <tr>
                                        <td class="px-3 py-2 text-xs">Total Income</td>
                                        <td class="px-3 py-2 text-xs text-right" x-text="formatCurrency(getPropertyIncomeTotal(property))"></td>
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
                        <h5 class="text-sm font-semibold text-gray-800">Property Expenses</h5>
                        <button type="button"
                                @click="startAddingExpense(propIndex)"
                                x-show="!property.isAddingExpense"
                                class="text-xs text-green-600 hover:text-green-700 font-medium transition">
                            + Add Expense
                        </button>
                    </div>

                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inc GST</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">GST</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ex GST</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quarter</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    {{-- Add Expense Form Row --}}
                                    <tr x-show="property.isAddingExpense" class="bg-blue-50">
                                        <td class="px-3 py-2">
                                            <select x-model="property.newExpense.type" class="w-full rounded border-gray-300 text-xs">
                                                <option value="repairs">Repairs</option>
                                                <option value="rates">Rates</option>
                                                <option value="insurance">Insurance</option>
                                                <option value="management">Management</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text"
                                                   x-model="property.newExpense.description"
                                                   placeholder="Description"
                                                   class="w-full rounded border-gray-300 text-xs">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number"
                                                   x-model.number="property.newExpense.amount"
                                                   placeholder="0.00"
                                                   step="0.01"
                                                   class="w-full rounded border-gray-300 text-xs text-right">
                                        </td>
                                        <td class="px-3 py-2 text-right text-xs text-gray-600" x-text="formatCurrency(calculateGST(property.newExpense.amount, property.newExpense.isGstFree))"></td>
                                        <td class="px-3 py-2 text-right text-xs text-gray-600" x-text="formatCurrency(calculateExGST(property.newExpense.amount, property.newExpense.isGstFree))"></td>
                                        <td class="px-3 py-2">
                                            <select x-model="property.newExpense.quarter" class="w-full rounded border-gray-300 text-xs">
                                                <option value="q1">Q1</option>
                                                <option value="q2">Q2</option>
                                                <option value="q3">Q3</option>
                                                <option value="q4">Q4</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="space-y-1">
                                                <input type="file"
                                                       :id="'expenseFile-' + propIndex"
                                                       @change="property.newExpense.file = $event.target.files[0]"
                                                       class="w-full text-xs">
                                                <label class="flex items-center text-xs">
                                                    <input type="checkbox" x-model="property.newExpense.isGstFree" class="rounded border-gray-300 text-green-600">
                                                    <span class="ml-1">GST Free</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex justify-center space-x-2">
                                                <button type="button"
                                                        @click="saveExpense(propIndex)"
                                                        :disabled="saving"
                                                        class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 disabled:opacity-50">
                                                    Save
                                                </button>
                                                <button type="button"
                                                        @click="cancelAddingExpense(propIndex)"
                                                        class="px-2 py-1 bg-gray-300 text-gray-700 text-xs rounded hover:bg-gray-400">
                                                    Cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Existing Expense Items --}}
                                    <template x-for="(expense, expIndex) in property.expenses" :key="expense.id">
                                        <tr>
                                            <td class="px-3 py-2" x-text="expense.type"></td>
                                            <td class="px-3 py-2" x-text="expense.description"></td>
                                            <td class="px-3 py-2 text-right" x-text="formatCurrency(expense.amount_inc_gst)"></td>
                                            <td class="px-3 py-2 text-right" x-text="formatCurrency(expense.gst_amount)"></td>
                                            <td class="px-3 py-2 text-right" x-text="formatCurrency(expense.net_ex_gst)"></td>
                                            <td class="px-3 py-2 uppercase" x-text="expense.quarter"></td>
                                            <td class="px-3 py-2">
                                                <div class="flex flex-col gap-1">
                                                    <span x-show="expense.is_gst_free" class="text-xs text-gray-500">GST Free</span>
                                                    <template x-if="expense.attachments && expense.attachments.length > 0">
                                                        <a :href="'/attachments/' + expense.attachments[0].id + '/download'"
                                                           target="_blank"
                                                           class="text-xs text-blue-600 hover:text-blue-800 underline"
                                                           x-text="expense.attachments[0].original_filename"></a>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button type="button"
                                                        @click="deleteExpense(propIndex, expIndex, expense.id)"
                                                        class="text-red-600 hover:text-red-800 text-xs">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </template>

                                    {{-- Empty State --}}
                                    <tr x-show="!property.isAddingExpense && property.expenses.length === 0">
                                        <td colspan="8" class="px-3 py-6 text-center text-xs text-gray-500">
                                            No expenses added yet
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-green-50 font-semibold">
                                    <tr>
                                        <td colspan="2" class="px-3 py-2 text-xs">Total Expenses</td>
                                        <td class="px-3 py-2 text-xs text-right" x-text="formatCurrency(getPropertyExpenseTotal(property, 'inc'))"></td>
                                        <td class="px-3 py-2 text-xs text-right" x-text="formatCurrency(getPropertyExpenseTotal(property, 'gst'))"></td>
                                        <td class="px-3 py-2 text-xs text-right" x-text="formatCurrency(getPropertyExpenseTotal(property, 'ex'))"></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Property Summary --}}
                <div class="mt-6 p-4 bg-green-100 rounded-lg border border-green-300">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-xs text-gray-600">Total Income</div>
                            <div class="text-lg font-bold text-green-700" x-text="formatCurrency(getPropertyIncomeTotal(property))"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Total Expenses</div>
                            <div class="text-lg font-bold text-red-600" x-text="formatCurrency(getPropertyExpenseTotal(property, 'ex'))"></div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600">Net Profit</div>
                            <div class="text-lg font-bold"
                                 :class="getPropertyProfit(property) >= 0 ? 'text-green-700' : 'text-red-600'"
                                 x-text="formatCurrency(getPropertyProfit(property))"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Overall Rental Summary --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border-2 border-green-400">
        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            All Properties Summary
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Properties</div>
                <div class="text-2xl font-bold text-gray-900" x-text="properties.length"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Income</div>
                <div class="text-2xl font-bold text-green-600" x-text="formatCurrency(getTotalIncome())"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Total Expenses</div>
                <div class="text-2xl font-bold text-red-600" x-text="formatCurrency(getTotalExpenses())"></div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">Net Position</div>
                <div class="text-2xl font-bold"
                     :class="getTotalProfit() >= 0 ? 'text-green-600' : 'text-red-600'"
                     x-text="formatCurrency(getTotalProfit())"></div>
            </div>
        </div>
    </div>

    {{-- Comments --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client Comments</label>
            <textarea rows="3"
                      x-model="clientComments"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition"
                      placeholder="Add any notes or comments..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3"
                      x-model="internalComments"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition"
                      placeholder="Add internal notes..."></textarea>
        </div>
    </div>
</div>

<script>
function rentalPropertySection(sectionId) {
    return {
        sectionId: sectionId,
        saving: false,
        properties: [],
        clientComments: '',
        internalComments: '',

        async init() {
            await this.loadProperties();
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

        async loadProperties() {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/rental-properties`, {
                    headers
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.properties && data.properties.length > 0) {
                        this.properties = data.properties.map(prop => ({
                            id: prop.id,
                            label: prop.label || 'rental',
                            address_label: prop.address_label || '',
                            full_address: prop.full_address || '',
                            ownership_percentage: prop.ownership_percentage || 100,
                            period_rented_from: prop.period_rented_from || '',
                            period_rented_to: prop.period_rented_to || '',
                            income: prop.income_items || [],
                            expenses: prop.expense_items || [],
                            isAddingIncome: false,
                            isAddingExpense: false,
                            newIncome: this.createEmptyIncome(),
                            newExpense: this.createEmptyExpense()
                        }));
                    } else {
                        // No properties exist, add one empty one
                        await this.addProperty();
                    }
                } else {
                    // Error loading, add one empty property
                    await this.addProperty();
                }
            } catch (error) {
                console.error('Failed to load properties:', error);
                // On error, add one empty property
                await this.addProperty();
            }
        },

        async addProperty() {
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';
            try {
                // Create property in database first
                const response = await fetch(`/api/work-sections/${this.sectionId}/rental-properties`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'rental',
                        address_label: '',
                        full_address: null,
                        ownership_percentage: 100,
                        period_rented_from: null,
                        period_rented_to: null
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to create property');
                }

                const data = await response.json();

                // Add to frontend
                this.properties.push({
                    id: data.property.id,
                    label: data.property.label || 'rental',
                    address_label: data.property.address_label || '',
                    full_address: data.property.full_address || '',
                    ownership_percentage: data.property.ownership_percentage || 100,
                    period_rented_from: data.property.period_rented_from || '',
                    period_rented_to: data.property.period_rented_to || '',
                    income: [],
                    expenses: [],
                    isAddingIncome: false,
                    isAddingExpense: false,
                    newIncome: this.createEmptyIncome(),
                    newExpense: this.createEmptyExpense()
                });
            } catch (error) {
                console.error('Add property error:', error);
                alert('Failed to add property: ' + error.message);
            }
        },

        async removeProperty(index) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            if (!confirm('Remove this property? This will delete all associated income and expenses. This action cannot be undone.')) {
                return;
            }

            const property = this.properties[index];

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/rental-properties/${property.id}`, {
                    method: 'DELETE',
                    headers
                });

                if (!response.ok) {
                    throw new Error('Failed to delete property');
                }

                this.properties.splice(index, 1);
            } catch (error) {
                console.error('Remove property error:', error);
                alert('Failed to remove property: ' + error.message);
            }
        },

        createEmptyIncome() {
            return {
                description: '',
                amount: 0,
                quarter: 'q1',
                file: null
            };
        },

        createEmptyExpense() {
            return {
                type: 'repairs',
                description: '',
                amount: 0,
                quarter: 'q1',
                isGstFree: false,
                file: null
            };
        },

        startAddingIncome(propIndex) {
            this.properties[propIndex].isAddingIncome = true;
            this.properties[propIndex].newIncome = this.createEmptyIncome();
        },

        cancelAddingIncome(propIndex) {
            this.properties[propIndex].isAddingIncome = false;
            this.properties[propIndex].newIncome = this.createEmptyIncome();
            const fileInput = document.getElementById(`incomeFile-${propIndex}`);
            if (fileInput) fileInput.value = '';
        },

        startAddingExpense(propIndex) {
            this.properties[propIndex].isAddingExpense = true;
            this.properties[propIndex].newExpense = this.createEmptyExpense();
        },

        cancelAddingExpense(propIndex) {
            this.properties[propIndex].isAddingExpense = false;
            this.properties[propIndex].newExpense = this.createEmptyExpense();
            const fileInput = document.getElementById(`expenseFile-${propIndex}`);
            if (fileInput) fileInput.value = '';
        },

        async updateProperty(propIndex) {
            const property = this.properties[propIndex];
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/rental-properties/${property.id}`, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify({
                        label: property.label || 'rental',
                        address_label: property.address_label,
                        full_address: property.full_address,
                        ownership_percentage: property.ownership_percentage,
                        period_rented_from: property.period_rented_from,
                        period_rented_to: property.period_rented_to
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to update property');
                }

                const data = await response.json();
                console.log('Property updated:', data);
            } catch (error) {
                console.error('Update property error:', error);
            }
        },

        async saveIncome(propIndex) {
            const property = this.properties[propIndex];
            const income = property.newIncome;
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';

            if (!income.description || income.amount <= 0) {
                alert('Please provide description and amount');
                return;
            }

            this.saving = true;

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'rental',
                        description: income.description,
                        amount: income.amount,
                        quarter: income.quarter,
                        rental_property_id: property.id  // Link to property
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save income');
                }

                const newIncome = {
                    id: data.income.id,
                    description: data.income.description,
                    amount: this.safeParseFloat(data.income.amount),
                    quarter: data.income.quarter,
                    attachments: []
                };

                // Upload file if provided
                if (income.file) {
                    const attachment = await this.uploadFile(data.income.id, income.file, 'income');
                    if (attachment) {
                        newIncome.attachments = [attachment];
                    }
                }

                property.income.push(newIncome);
                this.cancelAddingIncome(propIndex);

            } catch (error) {
                console.error('Save income error:', error);
                alert(error.message);
            } finally {
                this.saving = false;
            }
        },

        async saveExpense(propIndex) {
            const property = this.properties[propIndex];
            const expense = property.newExpense;
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            headers['Accept'] = 'application/json';

            if (!expense.description || expense.amount <= 0) {
                alert('Please provide description and amount');
                return;
            }

            this.saving = true;

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'rental',
                        description: expense.description,
                        type: expense.type,
                        amount_inc_gst: expense.amount,
                        is_gst_free: expense.isGstFree,
                        quarter: expense.quarter,
                        rental_property_id: property.id  // Link to property
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save expense');
                }

                const newExpense = {
                    id: data.expense.id,
                    description: data.expense.description,
                    type: data.expense.type,
                    amount_inc_gst: this.safeParseFloat(data.expense.amount_inc_gst),
                    gst_amount: this.safeParseFloat(data.expense.gst_amount),
                    net_ex_gst: this.safeParseFloat(data.expense.net_ex_gst),
                    quarter: data.expense.quarter,
                    is_gst_free: data.expense.is_gst_free,
                    attachments: []
                };

                // Upload file if provided
                if (expense.file) {
                    const attachment = await this.uploadFile(data.expense.id, expense.file, 'expense');
                    if (attachment) {
                        newExpense.attachments = [attachment];
                    }
                }

                property.expenses.push(newExpense);
                this.cancelAddingExpense(propIndex);

            } catch (error) {
                console.error('Save expense error:', error);
                alert(error.message);
            } finally {
                this.saving = false;
            }
        },

        async uploadFile(itemId, file, type) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            const formData = new FormData();
            formData.append('file', file);

            const endpoint = type === 'income'
                ? `/api/work-sections/income/${itemId}/upload`
                : `/api/work-sections/expenses/${itemId}/upload`;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'File upload failed');
                }

                return data.attachment;
            } catch (error) {
                console.error('Upload error:', error);
                return null;
            }
        },

        async getIncomeItem(id) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            const response = await fetch(`/api/work-sections/${this.sectionId}/income`, {
                headers
            });

            if (response.ok) {
                const data = await response.json();
                return data.income.find(i => i.id === id) || {};
            }
            return {};
        },

        async getExpenseItem(id) {
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';
            const response = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                headers
            });

            if (response.ok) {
                const data = await response.json();
                return data.expenses.find(e => e.id === id) || {};
            }
            return {};
        },

        async deleteIncome(propIndex, incIndex, incomeId) {
            if (!confirm('Delete this income entry?')) return;
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/income/${incomeId}`, {
                    method: 'DELETE',
                    headers
                });

                if (!response.ok) {
                    throw new Error('Failed to delete income');
                }

                this.properties[propIndex].income.splice(incIndex, 1);

            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message);
            }
        },

        async deleteExpense(propIndex, expIndex, expenseId) {
            if (!confirm('Delete this expense entry?')) return;
            const headers = this.detectAuth();
            headers['Accept'] = 'application/json';

            try {
                const response = await fetch(`/api/work-sections/${this.sectionId}/expenses/${expenseId}`, {
                    method: 'DELETE',
                    headers
                });

                if (!response.ok) {
                    throw new Error('Failed to delete expense');
                }

                this.properties[propIndex].expenses.splice(expIndex, 1);

            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message);
            }
        },

        // Calculations
        calculateGST(amount, isGstFree) {
            if (!amount || isGstFree) return 0;
            return this.safeParseFloat(amount) / 11;
        },

        calculateExGST(amount, isGstFree) {
            const amt = this.safeParseFloat(amount);
            if (isGstFree) return amt;
            return amt - (amt / 11);
        },

        getPropertyIncomeTotal(property) {
            return property.income.reduce((sum, item) => sum + this.safeParseFloat(item.amount), 0);
        },

        getPropertyExpenseTotal(property, type) {
            if (type === 'inc') {
                return property.expenses.reduce((sum, item) => sum + this.safeParseFloat(item.amount_inc_gst), 0);
            } else if (type === 'gst') {
                return property.expenses.reduce((sum, item) => sum + this.safeParseFloat(item.gst_amount), 0);
            } else {
                return property.expenses.reduce((sum, item) => sum + this.safeParseFloat(item.net_ex_gst), 0);
            }
        },

        getPropertyProfit(property) {
            return this.getPropertyIncomeTotal(property) - this.getPropertyExpenseTotal(property, 'ex');
        },

        getTotalIncome() {
            return this.properties.reduce((sum, prop) => sum + this.getPropertyIncomeTotal(prop), 0);
        },

        getTotalExpenses() {
            return this.properties.reduce((sum, prop) => sum + this.getPropertyExpenseTotal(prop, 'ex'), 0);
        },

        getTotalProfit() {
            return this.getTotalIncome() - this.getTotalExpenses();
        },

        // Helpers
        formatCurrency(value) {
            const num = parseFloat(value);
            if (isNaN(num)) return '$0.00';
            return '$' + num.toFixed(2);
        },

        safeParseFloat(value) {
            const num = parseFloat(value);
            return isNaN(num) ? 0 : num;
        }
    }
}
</script>
