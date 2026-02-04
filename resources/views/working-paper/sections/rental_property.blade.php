{{-- Rental Property Section Partial - working-papers/partials/rental-property-section.blade.php --}}

<div class="space-y-6">
    {{-- Add Property Button --}}
    <div class="flex justify-between items-center">
        <h4 class="text-md font-semibold text-gray-800">Rental Properties</h4>
        <button type="button"
                onclick="addRentalProperty()"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Property
        </button>
    </div>

    {{-- Property List --}}
    <div id="rental-properties-list" class="space-y-6">
        {{-- Property 1 (Example) --}}
        <div class="rental-property-card bg-white rounded-lg border-2 border-green-300 p-6">
            {{-- Property Header --}}
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <input type="text"
                           placeholder="Property Address/Nickname"
                           class="text-lg font-semibold border-0 border-b-2 border-gray-300 focus:border-green-500 focus:ring-0 w-full"
                           value="123 Main Street, Sydney">
                </div>
                <button type="button"
                        class="ml-4 text-red-600 hover:text-red-700">
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
                           step="0.01"
                           max="100"
                           placeholder="100.00"
                           class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Rented From</label>
                    <input type="date"
                           class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Rented To</label>
                    <input type="date"
                           class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            {{-- Income Section --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-800">Rental Income</h5>
                    <button type="button" class="text-xs text-green-600 hover:text-green-700 font-medium">
                        + Add Income
                    </button>
                </div>

                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Amount</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Quarter</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Upload</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-xs text-gray-500">
                                    No income entries
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-green-50 font-semibold">
                            <tr>
                                <td class="px-3 py-2 text-xs">Total Income</td>
                                <td class="px-3 py-2 text-xs">$0.00</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Expenses Section --}}
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h5 class="text-sm font-semibold text-gray-800">Property Expenses</h5>
                    <button type="button" class="text-xs text-green-600 hover:text-green-700 font-medium">
                        + Add Expense
                    </button>
                </div>

                <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Category</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Amount</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">GST</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Quarter</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Upload</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-xs text-gray-500">
                                    No expenses added yet
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-green-50 font-semibold">
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-xs">Total Expenses</td>
                                <td class="px-3 py-2 text-xs">$0.00</td>
                                <td class="px-3 py-2 text-xs">$0.00</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Property Summary --}}
            <div class="mt-6 p-4 bg-green-100 rounded-lg border border-green-300">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-xs text-gray-600">Total Income</div>
                        <div class="text-lg font-bold text-green-700">$0.00</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600">Total Expenses</div>
                        <div class="text-lg font-bold text-red-600">$0.00</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600">Net Profit</div>
                        <div class="text-lg font-bold text-gray-900">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Overall Rental Summary --}}
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 border-2 border-green-400">
        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            All Properties Summary
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Total Properties</div>
                <div class="text-2xl font-bold text-gray-900">1</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Total Income</div>
                <div class="text-2xl font-bold text-green-600">$0.00</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Total Expenses</div>
                <div class="text-2xl font-bold text-red-600">$0.00</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-xs text-gray-600 mb-1">Net Position</div>
                <div class="text-2xl font-bold text-green-600">$0.00</div>
            </div>
        </div>
    </div>

    {{-- Comments --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client Comments</label>
            <textarea rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                      placeholder="Add any notes or comments..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                      placeholder="Add internal notes..."></textarea>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let propertyCount = 1;

    function addRentalProperty() {
        propertyCount++;
        const propertyTemplate = `
            <div class="rental-property-card bg-white rounded-lg border-2 border-green-300 p-6">
                <!-- Similar structure as above, with unique IDs -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <input type="text"
                               placeholder="Property Address/Nickname"
                               class="text-lg font-semibold border-0 border-b-2 border-gray-300 focus:border-green-500 focus:ring-0 w-full">
                    </div>
                    <button type="button"
                            onclick="removeProperty(this)"
                            class="ml-4 text-red-600 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 italic">Property #${propertyCount} - Configure details below</p>
            </div>
        `;

        document.getElementById('rental-properties-list').insertAdjacentHTML('beforeend', propertyTemplate);
    }

    function removeProperty(button) {
        if (confirm('Remove this property? This action cannot be undone.')) {
            button.closest('.rental-property-card').remove();
            propertyCount--;
        }
    }
</script>
@endpush
