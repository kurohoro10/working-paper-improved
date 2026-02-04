<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Create Working Paper') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-linear-to-br from-slate-50 to-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 animate-in fade-in slide-in-from-top-4 duration-700">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('working-papers.index') }}" class="group flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-all">
                        <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Working Papers
                    </a>
                </div>
                <h1 class="text-4xl font-bold text-slate-900 tracking-tight font-display">Create Working Paper</h1>
                <p class="mt-2 text-slate-600">Set up a new working paper for client tax and bookkeeping data capture.</p>
            </div>

            <form method="POST" action="{{ route('working-papers.store') }}" x-data="workingPaperForm()" @submit="isSubmitting = true">
                @csrf

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden animate-in fade-in slide-in-from-bottom-6 duration-700 fill-mode-both delay-150">

                    <div class="p-8 border-b border-slate-100">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Client Information</h2>
                                <p class="text-sm text-slate-500">Select the client and reporting period</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="client_id" :value="__('Client')" class="font-semibold text-slate-700" />
                                <select name="client_id" id="client_id" class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all" required>
                                    <option value="">Select a client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                            {{ $client->name }} ({{ $client->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('client_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="financial_year" :value="__('Financial Year')" class="font-semibold text-slate-700" />
                                <x-text-input id="financial_year" name="financial_year" type="text" class="mt-1 block w-full rounded-xl"
                                    :value="old('financial_year', $defaultFinancialYear)"
                                    placeholder="e.g., 2024-2025" required />
                                <p class="mt-2 text-xs text-slate-400">Format: YYYY-YYYY</p>
                                <x-input-error :messages="$errors->get('financial_year')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Work Types</h2>
                                <p class="text-sm text-slate-500">Select modules to include in this paper</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $types = [
                                    ['id' => 'wage', 'name' => 'Wage', 'desc' => 'PAYG, salary, and deductions', 'tag' => null],
                                    ['id' => 'rental_property', 'name' => 'Rental', 'desc' => 'Property income/expenses', 'tag' => null],
                                    ['id' => 'sole_trader', 'name' => 'Sole Trader', 'desc' => 'Business tracking', 'tag' => 'GST'],
                                    ['id' => 'bas', 'name' => 'BAS', 'desc' => 'Quarterly activity statement', 'tag' => 'Quarterly'],
                                    ['id' => 'ctax', 'name' => 'Company Tax', 'desc' => 'Corporate tax compliance', 'tag' => 'GST'],
                                    ['id' => 'ttax', 'name' => 'Trust Tax', 'desc' => 'Trust distributions', 'tag' => 'GST'],
                                    ['id' => 'smsf', 'name' => 'SMSF', 'desc' => 'Super fund contributions', 'tag' => 'Super'],
                                ];
                            @endphp

                            @foreach($types as $type)
                            <label
                                class="relative flex flex-col p-4 bg-white border-2 rounded-2xl cursor-pointer transition-all hover:border-blue-400 select-none"
                                :class="selectedTypes.includes('{{ $type['id'] }}') ? 'border-blue-600 ring-4 ring-blue-50 shadow-md' : 'border-slate-200'"
                            >
                                <input type="checkbox" name="selected_work_types[]" value="{{ $type['id'] }}" class="hidden"
                                    x-model="selectedTypes"
                                    @checked(is_array(old('selected_work_types')) && in_array($type['id'], old('selected_work_types')))>

                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-slate-900">{{ $type['name'] }}</span>
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                        :class="selectedTypes.includes('{{ $type['id'] }}') ? 'bg-blue-600 border-blue-600' : 'bg-white border-slate-300'">
                                        <svg x-show="selectedTypes.includes('{{ $type['id'] }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed mb-3">{{ $type['desc'] }}</p>
                                @if($type['tag'])
                                    <span class="inline-flex items-center w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $type['tag'] }}
                                    </span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('selected_work_types')" class="mt-4" />
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="notes" :value="__('Internal Notes')" class="font-semibold text-slate-700" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm" placeholder="Add any private instructions...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="max-w-xs">
                                <x-input-label for="status" :value="__('Initial Status')" class="font-semibold text-slate-700" />
                                <select name="status" id="status" class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                                    <option value="draft" @selected(old('status', 'draft') == 'draft')>Draft</option>
                                    <option value="in_progress" @selected(old('status') == 'in_progress')>In Progress</option>
                                    <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 italic" x-show="selectedTypes.length > 0">
                            <span x-text="selectedTypes.length"></span> module(s) selected
                        </p>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a href="{{ route('working-papers.index') }}" class="flex-1 sm:flex-none text-center px-6 py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-8 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all disabled:opacity-50"
                                :disabled="isSubmitting || selectedTypes.length === 0"
                                @click="isSubmitting = false">
                                <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <svg x-show="isSubmitting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSubmitting ? 'Creating...' : 'Create Working Paper'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function workingPaperForm() {
            return {
                selectedTypes: @json(old('selected_work_types', [])),
                isSubmitting: false,
            }
        }
    </script>
</x-app-layout>
