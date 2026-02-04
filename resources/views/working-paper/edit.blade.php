<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Working Paper') }} - {{ $workingPaper->reference_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('working-papers.update', $workingPaper) }}">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    {{-- Client Information Section --}}
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Client <span class="text-red-500">*</span>
                                </label>
                                <select name="client_id"
                                        id="client_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        disabled>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $workingPaper->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }} ({{ $client->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Client cannot be changed after creation</p>
                            </div>

                            <div>
                                <label for="financial_year" class="block text-sm font-medium text-gray-700 mb-2">
                                    Financial Year <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="financial_year"
                                       id="financial_year"
                                       value="{{ old('financial_year', $workingPaper->financial_year) }}"
                                       placeholder="e.g., 2024-2025"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('financial_year') border-red-500 @enderror"
                                       required>
                                @error('financial_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Work Types Section --}}
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Types</h3>
                        <p class="text-sm text-gray-600 mb-4">Select the work types for this working paper</p>

                        @error('selected_work_types')
                            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $selectedTypes = old('selected_work_types', $workingPaper->workSections->pluck('work_type')->toArray());
                                $workTypes = [
                                    ['value' => 'wage', 'label' => 'Wage', 'desc' => 'PAYG, salary & deductions'],
                                    ['value' => 'rental_property', 'label' => 'Rental Property', 'desc' => 'Property income & expenses'],
                                    ['value' => 'sole_trader', 'label' => 'Sole Trader', 'desc' => 'Business with GST'],
                                    ['value' => 'bas', 'label' => 'BAS', 'desc' => 'Quarterly statements'],
                                    ['value' => 'ctax', 'label' => 'Company Tax', 'desc' => 'Corporate tax return'],
                                    ['value' => 'ttax', 'label' => 'Trust Tax', 'desc' => 'Trust distributions'],
                                    ['value' => 'smsf', 'label' => 'SMSF', 'desc' => 'Super fund management'],
                                ];
                            @endphp

                            @foreach($workTypes as $type)
                            <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition {{ in_array($type['value'], $selectedTypes) ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <input type="checkbox"
                                       name="selected_work_types[]"
                                       value="{{ $type['value'] }}"
                                       {{ in_array($type['value'], $selectedTypes) ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-gray-900">{{ $type['label'] }}</span>
                                    <span class="block text-xs text-gray-600">{{ $type['desc'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Additional Information --}}
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>

                        <div class="space-y-6">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes
                                </label>
                                <textarea name="notes"
                                          id="notes"
                                          rows="4"
                                          placeholder="Add any additional notes or instructions..."
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $workingPaper->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select name="status"
                                        id="status"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="draft" {{ old('status', $workingPaper->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="in_progress" {{ old('status', $workingPaper->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $workingPaper->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="archived" {{ old('status', $workingPaper->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="p-6 bg-gray-50 flex items-center justify-between">
                        <a href="{{ route('working-papers.show', $workingPaper) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Working Paper
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
