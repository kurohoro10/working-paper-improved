<x-app-layout>
    <input type="hidden" id="access_token" value="{{ $access_token }}" disabled readonly>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $workingPaper->reference_number }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $workingPaper->client->name }} - FY {{ $workingPaper->financial_year }}</p>
            </div>
        </div>
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

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Status</dt>
                                    <dd class="mt-1">
                                        <span class="text-lg font-semibold
                                            {{ $workingPaper->status === 'completed' ? 'text-green-600' : '' }}
                                            {{ $workingPaper->status === 'in_progress' ? 'text-yellow-600' : '' }}
                                            {{ $workingPaper->status === 'draft' ? 'text-gray-600' : '' }}
                                            {{ $workingPaper->status === 'archived' ? 'text-red-600' : '' }}">
                                            {{ ucwords(str_replace('_', ' ', $workingPaper->status)) }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Work Types</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">
                                        {{ $workingPaper->workSections->count() }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Created By</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 truncate">
                                        {{ $workingPaper->creator->name ?? 'System' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Created</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $workingPaper->created_at->format('d M Y') }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Work Sections Tabs --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($workingPaper->workSections->count() > 0)
                    {{-- Tab Navigation --}}
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex overflow-x-auto" aria-label="Tabs">
                            @foreach($workingPaper->workSections as $index => $section)
                            <button type="button"
                                    onclick="switchTab('{{ $section->work_type->value }}')"
                                    id="tab-{{ $section->work_type->value }}"
                                    class="work-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm
                                           {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ ucwords(str_replace('_', ' ', $section->work_type->value)) }}
                            </button>
                            @endforeach
                        </nav>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-6">
                        @foreach($workingPaper->workSections as $index => $section)
                        <div id="content-{{ $section->work_type->value }}"
                             class="tab-content {{ $index === 0 ? '' : 'hidden' }}">
                            @includeIf('working-paper.sections.' . $section->work_type->value, ['section' => $section])
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No work sections</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding work types to this working paper.</p>
                        @if(auth()->user()->isInternal() || auth()->user()->id === $workingPaper->client_id)
                        <div class="mt-6">
                            <a href="{{ route('working-papers.edit', $workingPaper) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Add Work Types
                            </a>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function switchTab(workType) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            document.querySelectorAll('.work-tab').forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            document.getElementById('content-' + workType).classList.remove('hidden');

            // Add active state to selected tab
            const activeTab = document.getElementById('tab-' + workType);
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            activeTab.classList.add('border-blue-500', 'text-blue-600');
        }
    </script>
</x-app-layout>
