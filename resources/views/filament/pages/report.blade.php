<x-filament-panels::page>
    {{-- FILTER FORM --}}
    <div class="space-y-6">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>

    {{-- LOADING INDICATOR --}}
    <div wire:loading wire:target="lihatProgress"
        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mt-4">
        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
        </svg>
        Sedang memproses laporan...
    </div>

    {{-- REPORT DATA --}}
    @if (!empty($reportData))
        @php
            $summary = $reportData['summary'] ?? [];
            $data = $reportData['data'] ?? [];
        @endphp

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div
                class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Tiket</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['total_tickets'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center text-green-600 dark:text-green-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Periode</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ $summary['date_range'] ?? '-' }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Dibuat oleh</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ $summary['generated_by'] ?? '-' }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Digenerate</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ $summary['generated_at'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- GROUP BY: TICKET                                             --}}
        {{-- ============================================================ --}}
        @if ($groupBy === 'ticket')
            <div class="mt-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Daftar Tiket
                    <span
                        class="ml-1 text-xs font-normal bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded-full">
                        {{ count($data) }} tiket
                    </span>
                </h2>

                @foreach ($data as $ticket)
                    @php
                        $statusColor = match ($ticket['status'] ?? '') {
                            'open' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            'in_progress' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                            'closed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                        };
                        $statusLabel = match ($ticket['status'] ?? '') {
                            'open' => 'Open',
                            'in_progress' => 'In Progress',
                            'closed' => 'Closed',
                            'cancelled' => 'Cancelled',
                            default => $ticket['status'] ?? 'N/A',
                        };
                    @endphp

                    <div x-data="{ open: false }"
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">

                        {{-- TICKET HEADER --}}
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors text-left">
                            <div class="flex items-center gap-3 min-w-0">
                                <span
                                    class="shrink-0 text-xs font-mono font-bold bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 px-2.5 py-1 rounded-lg">
                                    {{ $ticket['ticket_code'] ?? '-' }}
                                </span>
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                    {{ $ticket['ticket_title'] ?? '-' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3 shrink-0 ml-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 hidden sm:block">
                                    {{ $ticket['created_at'] ?? '' }}
                                </span>
                                @if (($ticket['documents_count'] ?? 0) > 0)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ $ticket['documents_count'] }} progress
                                    </span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        {{-- TICKET DETAIL (Accordion) --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="border-t border-gray-100 dark:border-gray-700/60 px-5 py-4">

                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-3 sm:hidden">
                                📅 {{ $ticket['created_at'] ?? '' }}
                            </div>

                            @if (!empty($ticket['documents']))
                                <p
                                    class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">
                                    📋 Progress ({{ $ticket['documents_count'] }})
                                </p>
                                <div class="space-y-3">
                                    @foreach ($ticket['documents'] as $doc)
                                        <div class="p-3 bg-gray-50 dark:bg-gray-800/60 rounded-lg space-y-2">

                                            {{-- Teks Progress --}}
                                            @if (!empty($doc['description']))
                                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                                    {{ $doc['description'] }}
                                                </div>
                                            @endif

                                            {{-- File --}}
                                            @if (!empty($doc['name']))
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach (explode(', ', $doc['name']) as $fileName)
                                                        @php $fileName = trim($fileName); @endphp
                                                        @if ($fileName)
                                                            @php
                                                                $ext = strtolower(
                                                                    pathinfo($fileName, PATHINFO_EXTENSION),
                                                                );
                                                                $icon = match (true) {
                                                                    in_array($ext, ['pdf']) => '📄',
                                                                    in_array($ext, ['doc', 'docx']) => '📝',
                                                                    in_array($ext, ['xls', 'xlsx', 'csv']) => '📊',
                                                                    in_array($ext, [
                                                                        'jpg',
                                                                        'jpeg',
                                                                        'png',
                                                                        'gif',
                                                                        'webp',
                                                                    ])
                                                                        => '🖼️',
                                                                    in_array($ext, ['mp4', 'mov', 'avi', 'mkv'])
                                                                        => '🎥',
                                                                    in_array($ext, ['mp3', 'wav', 'ogg']) => '🎵',
                                                                    in_array($ext, ['zip', 'rar', '7z']) => '🗜️',
                                                                    default => '📎',
                                                                };
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-md border border-blue-100 dark:border-blue-800">
                                                                {{ $icon }} {{ $fileName }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Timestamp --}}
                                            @if (!empty($doc['created_at']))
                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                    📅 {{ $doc['created_at'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                                    Belum ada progress.
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============================================================ --}}
            {{-- GROUP BY: STATUS                                             --}}
            {{-- ============================================================ --}}
        @elseif ($groupBy === 'status')
            <div class="mt-6 space-y-6">
                <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                    Kelompok Per Status
                </h2>

                @foreach ($data as $statusKey => $group)
                    @php
                        $statusColor = match ($statusKey) {
                            'open' => [
                                'border' => 'border-blue-400',
                                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                                'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                            ],
                            'in_progress' => [
                                'border' => 'border-yellow-400',
                                'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                                'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                            ],
                            'closed' => [
                                'border' => 'border-green-400',
                                'bg' => 'bg-green-50 dark:bg-green-900/20',
                                'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                            ],
                            'cancelled' => [
                                'border' => 'border-red-400',
                                'bg' => 'bg-red-50 dark:bg-red-900/20',
                                'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            ],
                            default => [
                                'border' => 'border-gray-300',
                                'bg' => 'bg-gray-50 dark:bg-gray-800',
                                'badge' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            ],
                        };
                    @endphp

                    <div
                        class="bg-white dark:bg-gray-900 border-l-4 {{ $statusColor['border'] }} border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                        <div class="{{ $statusColor['bg'] }} px-5 py-3 flex items-center justify-between">
                            <span class="text-sm font-bold {{ $statusColor['badge'] }} px-3 py-1 rounded-full">
                                {{ strtoupper($group['status'] ?? 'N/A') }}
                            </span>
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                                {{ $group['count'] }} tiket
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach ($group['tickets'] as $ticket)
                                <div
                                    class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span
                                            class="shrink-0 text-xs font-mono font-bold text-primary-600 dark:text-primary-400">
                                            {{ $ticket['code'] ?? '-' }}
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                            {{ $ticket['title'] ?? '-' }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 ml-3">
                                        {{ $ticket['created_at'] ?? '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ============================================================ --}}
            {{-- GROUP BY: MONTH                                              --}}
            {{-- ============================================================ --}}
        @elseif ($groupBy === 'month')
            <div class="mt-6 space-y-6">
                <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Kelompok Per Bulan
                </h2>

                @foreach ($data as $month => $group)
                    <div
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                        <div
                            class="bg-gray-50 dark:bg-gray-800 px-5 py-3 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                                📅 {{ $group['month'] ?? $month }}
                            </span>
                            <span
                                class="text-xs font-semibold bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 px-2.5 py-1 rounded-full">
                                {{ $group['count'] }} tiket
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @foreach ($group['tickets'] as $ticket)
                                <div
                                    class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span
                                            class="shrink-0 text-xs font-mono font-bold text-primary-600 dark:text-primary-400">
                                            {{ $ticket['code'] ?? '-' }}
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                            {{ $ticket['title'] ?? '-' }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 shrink-0 ml-3">
                                        {{ $ticket['created_at'] ?? '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        {{-- EMPTY STATE --}}
        @if (!$isLoading)
            <div class="mt-6 flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada laporan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Atur filter di atas lalu klik <strong>Lihat Progress</strong>
                </p>
            </div>
        @endif
    @endif

</x-filament-panels::page>
