{{-- BAS Section - G1 and G11 with label differentiation --}}
<div class="space-y-6" x-data="basSection({{ $section->id }})" x-init="init()">
    {{-- Quarter Selection --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 bg-orange-100 rounded-lg border border-orange-300">
        <div class="flex flex-wrap items-center gap-4">
            <template x-for="quarter in quarters" :key="quarter.id">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" x-model="quarter.selected">
                    <span class="ml-2 text-sm font-medium" x-text="quarter.label"></span>
                </label>
            </template>
        </div>
        <button type="button" @click="toggleCombinedView" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-xs uppercase font-semibold transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-text="showCombined ? 'Show Individual Quarters' : 'Combine Quarters'"></span>
        </button>
    </div>

    {{-- Individual Quarter View --}}
    <div x-show="!showCombined" x-transition>
        <div class="border-b border-gray-200 pb-3 mb-6">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-900" x-text="quarters[currentQuarter].name"></h4>
                <div class="flex space-x-2">
                    <button type="button" @click="previousQuarter" :disabled="currentQuarter === 0" :class="currentQuarter === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'" class="px-3 py-1 text-sm bg-gray-100 rounded transition">← Prev</button>
                    <button type="button" @click="nextQuarter" :disabled="currentQuarter === 3" :class="currentQuarter === 3 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'" class="px-3 py-1 text-sm bg-gray-100 rounded transition">Next →</button>
                </div>
            </div>
        </div>

        {{-- G1: Sales --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800">G1: Total Sales (GST Inclusive)</h4>
                <button type="button" @click="showSaleForm = !showSaleForm" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    <span x-show="!showSaleForm">+ Add Sale</span>
                    <span x-show="showSaleForm">Cancel</span>
                </button>
            </div>

            <div x-show="showSaleForm" x-transition class="mb-4 bg-orange-50 p-4 rounded-lg border border-orange-200">
                <form @submit.prevent="saveSale">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" x-model="newSale.description" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (Inc GST)</label>
                            <input type="number" step="0.01" x-model="newSale.amount" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newSale.isGstFree" class="rounded border-gray-300 text-orange-600">
                                <span class="ml-2 text-sm">GST Free</span>
                            </label>
                        </div>
                        <div x-show="!newSale.isGstFree && newSale.amount > 0" class="md:col-span-2 text-sm bg-blue-50 p-3 rounded">
                            <strong>Auto-calculated:</strong> GST: $<span x-text="(newSale.amount / 11).toFixed(2)"></span>, Net: $<span x-text="(newSale.amount - (newSale.amount / 11)).toFixed(2)"></span>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showSaleForm = false; resetSaleForm()" class="px-4 py-2 border border-gray-300 rounded-md text-sm">Cancel</button>
                        <button type="submit" :disabled="saving" class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm disabled:opacity-50">
                            <span x-show="!saving">Save</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ex GST</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Attachments</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="sales.length === 0">
                                <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No sales entries</td></tr>
                            </template>
                            <template x-for="sale in sales" :key="sale.id">
                                <tr>
                                    <td class="px-3 py-2" x-text="sale.description"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(sale.amount_inc_gst || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(sale.gst_amount || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(sale.net_ex_gst || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-1">
                                            <template x-if="sale.attachments && sale.attachments.length > 0">
                                                <template x-for="(attachment, idx) in sale.attachments" :key="attachment.id">
                                                    <a :href="`/api/work-sections/attachments/${attachment.id}/download`"
                                                       target="_blank"
                                                       class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 group">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        <span class="truncate max-w-[150px] group-hover:underline" :title="attachment.original_filename" x-text="attachment.original_filename"></span>
                                                    </a>
                                                </template>
                                            </template>
                                            <button type="button" @click="uploadFile(sale.id, 'sale')" class="text-xs text-orange-600 hover:text-orange-700 text-left flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                + Upload
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button @click="deleteSale(sale.id)" class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-orange-50 font-semibold">
                            <tr>
                                <td class="px-3 py-2 text-sm">G1 Total</td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + salesTotal.incGst.toFixed(2)"></td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + salesTotal.gst.toFixed(2)"></td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + salesTotal.exGst.toFixed(2)"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- G11: Purchases --}}
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800">G11: GST on Purchases</h4>
                <button type="button" @click="showPurchaseForm = !showPurchaseForm" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    <span x-show="!showPurchaseForm">+ Add Purchase</span>
                    <span x-show="showPurchaseForm">Cancel</span>
                </button>
            </div>

            <div x-show="showPurchaseForm" x-transition class="mb-4 bg-orange-50 p-4 rounded-lg border border-orange-200">
                <form @submit.prevent="savePurchase">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" x-model="newPurchase.description" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (Inc GST)</label>
                            <input type="number" step="0.01" x-model="newPurchase.amount" required class="w-full rounded-md border-gray-300">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newPurchase.isGstFree" class="rounded border-gray-300 text-orange-600">
                                <span class="ml-2 text-sm">GST Free</span>
                            </label>
                        </div>
                        <div x-show="!newPurchase.isGstFree && newPurchase.amount > 0" class="md:col-span-2 text-sm bg-blue-50 p-3 rounded">
                            <strong>Auto-calculated:</strong> GST: $<span x-text="(newPurchase.amount / 11).toFixed(2)"></span>, Net: $<span x-text="(newPurchase.amount - (newPurchase.amount / 11)).toFixed(2)"></span>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" @click="showPurchaseForm = false; resetPurchaseForm()" class="px-4 py-2 border border-gray-300 rounded-md text-sm">Cancel</button>
                        <button type="submit" :disabled="saving" class="px-4 py-2 bg-orange-600 text-white rounded-md text-sm disabled:opacity-50">
                            <span x-show="!saving">Save</span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Inc GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">GST</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ex GST</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Attachments</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="purchases.length === 0">
                                <tr><td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No purchase entries</td></tr>
                            </template>
                            <template x-for="purchase in purchases" :key="purchase.id">
                                <tr>
                                    <td class="px-3 py-2" x-text="purchase.description"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(purchase.amount_inc_gst || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(purchase.gst_amount || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2 text-right" x-text="'$' + parseFloat(purchase.net_ex_gst || 0).toFixed(2)"></td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-col gap-1">
                                            <template x-if="purchase.attachments && purchase.attachments.length > 0">
                                                <template x-for="(attachment, idx) in purchase.attachments" :key="attachment.id">
                                                    <a :href="`/api/work-sections/attachments/${attachment.id}/download`"
                                                       target="_blank"
                                                       class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 group">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        <span class="truncate max-w-[150px] group-hover:underline" :title="attachment.original_filename" x-text="attachment.original_filename"></span>
                                                    </a>
                                                </template>
                                            </template>
                                            <button type="button" @click="uploadFile(purchase.id, 'purchase')" class="text-xs text-orange-600 hover:text-orange-700 text-left flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                + Upload
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button @click="deletePurchase(purchase.id)" class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-orange-50 font-semibold">
                            <tr>
                                <td class="px-3 py-2 text-sm">G11 Total</td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + purchasesTotal.incGst.toFixed(2)"></td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + purchasesTotal.gst.toFixed(2)"></td>
                                <td class="px-3 py-2 text-sm text-right" x-text="'$' + purchasesTotal.exGst.toFixed(2)"></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAS Summary --}}
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 border-2 border-orange-300">
            <h4 class="text-lg font-bold mb-4" x-text="'BAS Summary - ' + quarters[currentQuarter].label"></h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1">G1: Total Sales</div>
                    <div class="text-xl font-bold text-gray-900" x-text="'$' + salesTotal.incGst.toFixed(2)"></div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1">1A: GST on Sales</div>
                    <div class="text-xl font-bold text-green-600" x-text="'$' + salesTotal.gst.toFixed(2)"></div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1">1B: GST on Purchases</div>
                    <div class="text-xl font-bold text-blue-600" x-text="'$' + purchasesTotal.gst.toFixed(2)"></div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-600 mb-1">Net GST</div>
                    <div class="text-xl font-bold text-orange-600" x-text="'$' + netGST.toFixed(2)"></div>
                </div>
            </div>
            <div class="mt-4 p-4 bg-white rounded-lg border-2 border-orange-400">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold" x-text="quarters[currentQuarter].label + ' Amount Payable to ATO'"></span>
                    <span class="text-2xl font-bold text-orange-600" x-text="'$' + netGST.toFixed(2)"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Combined View with Detailed Per-Quarter Breakdown --}}
    <div x-show="showCombined" x-transition class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg p-6 border-2 border-orange-400">
        <h4 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span>BAS Overview - Annual Summary</span>
        </h4>

        {{-- Per-Quarter Detailed Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <template x-for="(quarter, index) in quarters" :key="quarter.id">
                <div x-show="quarter.selected" class="bg-white p-5 rounded-lg shadow-md border-l-4 border-orange-500">
                    <div class="text-sm font-bold text-gray-800 mb-3 flex items-center justify-between">
                        <span x-text="quarter.label"></span>
                        <span class="text-xs text-gray-500" x-text="quarter.name.split('(')[1]?.replace(')', '')"></span>
                    </div>

                    <div class="space-y-3 text-sm">
                        {{-- G1 Sales --}}
                        <div class="border-b pb-2">
                            <div class="text-xs text-gray-500 mb-1">G1: Sales (Inc GST)</div>
                            <div class="font-semibold text-gray-900" x-text="'$' + (quarterlyTotals[quarter.value]?.salesIncGst || 0).toFixed(2)"></div>
                        </div>

                        {{-- GST on Sales --}}
                        <div class="border-b pb-2">
                            <div class="text-xs text-gray-500 mb-1">1A: GST on Sales</div>
                            <div class="font-semibold text-green-600" x-text="'$' + (quarterlyTotals[quarter.value]?.salesGst || 0).toFixed(2)"></div>
                        </div>

                        {{-- G11 Purchases --}}
                        <div class="border-b pb-2">
                            <div class="text-xs text-gray-500 mb-1">G11: Purchases (Inc GST)</div>
                            <div class="font-semibold text-gray-900" x-text="'$' + (quarterlyTotals[quarter.value]?.purchasesIncGst || 0).toFixed(2)"></div>
                        </div>

                        {{-- GST on Purchases --}}
                        <div class="border-b pb-2">
                            <div class="text-xs text-gray-500 mb-1">1B: GST on Purchases</div>
                            <div class="font-semibold text-blue-600" x-text="'$' + (quarterlyTotals[quarter.value]?.purchasesGst || 0).toFixed(2)"></div>
                        </div>

                        {{-- Net GST --}}
                        <div class="pt-2 bg-orange-50 -mx-2 px-2 py-2 rounded">
                            <div class="text-xs text-gray-700 mb-1 font-medium">Net GST Payable</div>
                            <div class="font-bold text-lg text-orange-600" x-text="'$' + (quarterlyTotals[quarter.value]?.netGst || 0).toFixed(2)"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Annual Total Summary --}}
        <div class="bg-white p-6 rounded-lg shadow-lg border-2 border-orange-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Annual Totals</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Sales (Inc GST):</span>
                            <span class="font-semibold" x-text="'$' + annualTotals.salesIncGst.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total GST on Sales:</span>
                            <span class="font-semibold text-green-600" x-text="'$' + annualTotals.salesGst.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="text-gray-600">Total Purchases (Inc GST):</span>
                            <span class="font-semibold" x-text="'$' + annualTotals.purchasesIncGst.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total GST on Purchases:</span>
                            <span class="font-semibold text-blue-600" x-text="'$' + annualTotals.purchasesGst.toFixed(2)"></span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="bg-gradient-to-r from-orange-100 to-orange-50 p-6 rounded-lg text-center border-2 border-orange-400">
                        <div class="text-sm text-gray-700 mb-2 font-medium">Total GST Payable to ATO</div>
                        <div class="text-xs text-gray-500 mb-3">(Sum of selected quarters)</div>
                        <div class="text-4xl font-bold text-orange-600" x-text="'$' + totalCombinedGST.toFixed(2)"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden File Input --}}
    <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="hidden">
</div>

<script>
function basSection(sectionId) {
    return {
        sectionId,
        currentQuarter: 0,
        showCombined: false,
        showSaleForm: false,
        showPurchaseForm: false,
        saving: false,
        currentFileId: null,
        currentFileType: null,
        quarters: [
            { id: 1, label: 'Q1', name: 'Q1 (Jul-Sep)', value: 'q1', selected: true },
            { id: 2, label: 'Q2', name: 'Q2 (Oct-Dec)', value: 'q2', selected: true },
            { id: 3, label: 'Q3', name: 'Q3 (Jan-Mar)', value: 'q3', selected: true },
            { id: 4, label: 'Q4', name: 'Q4 (Apr-Jun)', value: 'q4', selected: true }
        ],
        sales: [],
        purchases: [],
        quarterlyTotals: {},
        newSale: { description: '', amount: '', isGstFree: false },
        newPurchase: { description: '', amount: '', isGstFree: false },

        get currentQuarterValue() {
            return this.quarters[this.currentQuarter].value;
        },

        get salesTotal() {
            return this.sales.reduce((acc, s) => ({
                incGst: acc.incGst + parseFloat(s.amount_inc_gst || 0),
                gst: acc.gst + parseFloat(s.gst_amount || 0),
                exGst: acc.exGst + parseFloat(s.net_ex_gst || 0)
            }), { incGst: 0, gst: 0, exGst: 0 });
        },

        get purchasesTotal() {
            return this.purchases.reduce((acc, p) => ({
                incGst: acc.incGst + parseFloat(p.amount_inc_gst || 0),
                gst: acc.gst + parseFloat(p.gst_amount || 0),
                exGst: acc.exGst + parseFloat(p.net_ex_gst || 0)
            }), { incGst: 0, gst: 0, exGst: 0 });
        },

        get netGST() {
            return this.salesTotal.gst - this.purchasesTotal.gst;
        },

        get totalCombinedGST() {
            return Object.values(this.quarterlyTotals).reduce((sum, q) => sum + parseFloat(q.netGst || 0), 0);
        },

        get annualTotals() {
            return Object.values(this.quarterlyTotals).reduce((acc, q) => ({
                salesIncGst: acc.salesIncGst + parseFloat(q.salesIncGst || 0),
                salesGst: acc.salesGst + parseFloat(q.salesGst || 0),
                purchasesIncGst: acc.purchasesIncGst + parseFloat(q.purchasesIncGst || 0),
                purchasesGst: acc.purchasesGst + parseFloat(q.purchasesGst || 0)
            }), { salesIncGst: 0, salesGst: 0, purchasesIncGst: 0, purchasesGst: 0 });
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

            try {
                const [salesRes, purchasesRes] = await Promise.all([
                    fetch(`/api/work-sections/${this.sectionId}/expenses?quarter=${this.currentQuarterValue}&label=g1`, { headers }),
                    fetch(`/api/work-sections/${this.sectionId}/expenses?quarter=${this.currentQuarterValue}&label=g11`, { headers })
                ]);

                if (salesRes.ok) this.sales = (await salesRes.json()).expenses;
                if (purchasesRes.ok) this.purchases = (await purchasesRes.json()).expenses;
            } catch (error) {
                console.error('Load error:', error);
            }
        },

        async nextQuarter() {
            if (this.currentQuarter < 3) {
                this.currentQuarter++;
                await this.loadData();
            }
        },

        async previousQuarter() {
            if (this.currentQuarter > 0) {
                this.currentQuarter--;
                await this.loadData();
            }
        },

        async toggleCombinedView() {
            this.showCombined = !this.showCombined;
            if (this.showCombined) await this.loadAllQuarterlyData();
        },

        async loadAllQuarterlyData() {
            for (const quarter of this.quarters) {
                const [sales, purchases] = await Promise.all([
                    this.getQuarterData(quarter.value, 'g1'),
                    this.getQuarterData(quarter.value, 'g11')
                ]);
                this.quarterlyTotals[quarter.value] = {
                    salesIncGst: sales.incGst,
                    salesGst: sales.gst,
                    purchasesIncGst: purchases.incGst,
                    purchasesGst: purchases.gst,
                    netGst: sales.gst - purchases.gst
                };
            }
        },

        async getQuarterData(quarterValue, label) {
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses?quarter=${quarterValue}&label=${label}`, {
                    headers: this.detectAuth()
                });
                if (!res.ok) {
                    console.error('Failed to load quarter', quarterValue, label);
                }

                const data = (await res.json()).expenses;

                return {
                    incGst: data.reduce((sum, item) => sum + parseFloat(item.amount_inc_gst || 0), 0),
                    gst: data.reduce((sum, item) => sum + parseFloat(item.gst_amount || 0), 0)
                };
            } catch (error) {
                console.error('Quarter data error:', error);
            }
            return { incGst: 0, gst: 0 };
        },

        async saveSale() {
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            this.saving = true;
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'g1',
                        description: this.newSale.description,
                        amount_inc_gst: this.newSale.amount,
                        is_gst_free: this.newSale.isGstFree,
                        quarter: this.currentQuarterValue
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.sales.push(data.expense);
                    this.resetSaleForm();
                    this.showSaleForm = false;
                    this.showNotification('Sale added successfully');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showNotification('Error saving sale', 'error');
            } finally {
                this.saving = false;
            }
        },

        async savePurchase() {
            const headers = this.detectAuth();
            headers['Content-Type'] = 'application/json';
            this.saving = true;
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses`, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        label: 'g11',
                        description: this.newPurchase.description,
                        amount_inc_gst: this.newPurchase.amount,
                        is_gst_free: this.newPurchase.isGstFree,
                        quarter: this.currentQuarterValue
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.purchases.push(data.expense);
                    this.resetPurchaseForm();
                    this.showPurchaseForm = false;
                    this.showNotification('Purchase added successfully');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showNotification('Error saving purchase', 'error');
            } finally {
                this.saving = false;
            }
        },

        async deleteSale(id) {
            const headers = this.detectAuth();
            if (!confirm('Delete this sale?')) return;
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses/${id}`, {
                    method: 'DELETE',
                    headers,
                });
                if ((await res.json()).success) {
                    this.sales = this.sales.filter(s => s.id !== id);
                    this.showNotification('Sale deleted');
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
        },

        async deletePurchase(id) {
            const headers = this.detectAuth();
            if (!confirm('Delete this purchase?')) return;
            try {
                const res = await fetch(`/api/work-sections/${this.sectionId}/expenses/${id}`, {
                    method: 'DELETE',
                    headers,
                });
                if ((await res.json()).success) {
                    this.purchases = this.purchases.filter(p => p.id !== id);
                    this.showNotification('Purchase deleted');
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
            const headers = this.detectAuth();
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const res = await fetch(`/api/work-sections/expenses/${this.currentFileId}/upload`, {
                    method: 'POST',
                    headers,
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    this.showNotification('File uploaded successfully');
                    await this.loadData();
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification('Upload failed', 'error');
            }

            event.target.value = '';
        },

        resetSaleForm() {
            this.newSale = { description: '', amount: '', isGstFree: false };
        },

        resetPurchaseForm() {
            this.newPurchase = { description: '', amount: '', isGstFree: false };
        },

        showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }
}
</script>
