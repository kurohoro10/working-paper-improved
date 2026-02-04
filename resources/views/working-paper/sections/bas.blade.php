{{-- BAS Section Partial - working-papers/partials/bas-section.blade.php --}}

<div class="space-y-6" x-data="basSection()">
    {{-- Quarter Selection & Combine Feature --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 bg-orange-100 rounded-lg border border-orange-300">
        <div class="flex flex-wrap items-center gap-4">
            <template x-for="quarter in quarters" :key="quarter.id">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox"
                           class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                           x-model="quarter.selected">
                    <span class="ml-2 text-sm font-medium" x-text="quarter.label"></span>
                </label>
            </template>
        </div>

        <button type="button"
                @click="toggleCombinedView"
                class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
            </svg>
            <span x-text="showCombined ? 'Show Individual Quarters' : 'Combine Selected Quarters'"></span>
        </button>
    </div>

    {{-- Individual Quarter View --}}
    <div x-show="!showCombined" x-transition>
        {{-- Current Quarter Navigation --}}
        <div class="border-b border-gray-200 pb-3 mb-6">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-900" x-text="quarters[currentQuarter].name"></h4>
                <div class="flex space-x-2">
                    <button type="button"
                            @click="previousQuarter"
                            :disabled="currentQuarter === 0"
                            :class="currentQuarter === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                            class="px-3 py-1 text-sm bg-gray-100 rounded transition">
                        ← Prev
                    </button>
                    <button type="button"
                            @click="nextQuarter"
                            :disabled="currentQuarter === 3"
                            :class="currentQuarter === 3 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                            class="px-3 py-1 text-sm bg-gray-100 rounded transition">
                        Next →
                    </button>
                </div>
            </div>
        </div>

        {{-- GST Sales (G1) --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800">G1: Total Sales (GST Inclusive)</h4>
                <button type="button"
                        @click="addSale"
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium transition">
                    + Add Sale
                </button>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inc GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ex GST</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    No sales entries for this quarter
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-orange-50 font-semibold">
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-sm">G1 Total</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- GST on Purchases (G11) --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800">G11: GST on Purchases</h4>
                <button type="button"
                        @click="addPurchase"
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium transition">
                    + Add Purchase
                </button>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inc GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ex GST</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    No purchase entries for this quarter
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-orange-50 font-semibold">
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-sm">G11 Total</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td class="px-3 py-2 text-sm text-right">$0.00</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAS Summary Card --}}
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border-2 border-orange-300">
            <h4 class="text-lg font-bold text-gray-900 mb-4" x-text="'BAS Summary - ' + quarters[currentQuarter].label"></h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">G1: Total Sales</div>
                    <div class="text-xl font-bold text-gray-900">$0.00</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">1A: GST on Sales</div>
                    <div class="text-xl font-bold text-green-600">$0.00</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide">1B: GST on Purchases</div>
                    <div class="text-xl font-bold text-blue-600">$0.00</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1 uppercase tracking-wide" x-text="quarters[currentQuarter].label + ' Net GST'"></div>
                    <div class="text-xl font-bold text-orange-600">$0.00</div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-white rounded-lg border-2 border-orange-400 shadow-sm">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700" x-text="quarters[currentQuarter].label + ' Amount Payable to ATO'"></span>
                    <span class="text-2xl font-bold text-orange-600">$0.00</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Annual Consolidated View (shown when combining quarters) --}}
    <div x-show="showCombined"
         x-transition
         class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg p-6 border-2 border-orange-400">
        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-text="'BAS Overview - ' + getSelectedQuartersLabel()"></span>
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <template x-for="quarter in quarters" :key="quarter.id">
                <div x-show="quarter.selected" class="bg-white p-4 rounded-lg shadow border-l-4 border-orange-500">
                    <div class="text-xs text-gray-600 mb-1 font-semibold" x-text="quarter.label"></div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sales GST:</span>
                            <span class="font-semibold text-green-600">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Purchase GST:</span>
                            <span class="font-semibold text-blue-600">$0.00</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="text-gray-700 font-medium">Net GST:</span>
                            <span class="font-bold text-orange-600">$0.00</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-orange-500">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <div class="text-sm text-gray-600 mb-1">Total GST Payable to ATO</div>
                    <div class="text-xs text-gray-500">(Sum of selected quarters)</div>
                </div>
                <span class="text-3xl font-bold text-orange-600">$0.00</span>
            </div>
        </div>
    </div>

    {{-- Comments --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Client Comments</label>
            <textarea rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition"
                      placeholder="Add any notes or comments..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Comments</label>
            <textarea rows="3"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition"
                      placeholder="Add internal notes..."></textarea>
        </div>
    </div>
</div>

<script>
function basSection() {
    return {
        currentQuarter: 0,
        showCombined: false,
        quarters: [
            { id: 1, label: 'Q1', name: 'Quarter 1 (July - September)', selected: true },
            { id: 2, label: 'Q2', name: 'Quarter 2 (October - December)', selected: true },
            { id: 3, label: 'Q3', name: 'Quarter 3 (January - March)', selected: true },
            { id: 4, label: 'Q4', name: 'Quarter 4 (April - June)', selected: true }
        ],

        nextQuarter() {
            if (this.currentQuarter < 3) {
                this.currentQuarter++;
            }
        },

        previousQuarter() {
            if (this.currentQuarter > 0) {
                this.currentQuarter--;
            }
        },

        toggleCombinedView() {
            this.showCombined = !this.showCombined;
        },

        getSelectedQuartersLabel() {
            const selected = this.quarters.filter(q => q.selected).map(q => q.label);
            if (selected.length === 0) return 'No Quarters Selected';
            if (selected.length === 4) return 'Q1-Q4 Combined';
            return selected.join(', ') + ' Combined';
        },

        addSale() {
            // Handle add sale logic
            console.log('Add sale for quarter:', this.quarters[this.currentQuarter].label);
        },

        addPurchase() {
            // Handle add purchase logic
            console.log('Add purchase for quarter:', this.quarters[this.currentQuarter].label);
        }
    }
}
</script>
