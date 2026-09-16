<x-filament-panels::page>

    {{ $this->table }}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">

        <div
            class="fi-section-header flex flex-col gap-3 border-b border-gray-100 px-6 py-4 dark:border-white/5 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                Data yang sudah di-generate
            </h3>
        </div>

        <div class="fi-ta-content overflow-x-auto">
            <table class="fi-ta-table w-full text-start">
                <thead class="border-b border-gray-200 dark:border-white/10">
                    <tr>
                        <th class="w-8 px-3 py-3.5 first-of-type:ps-4 first-of-type:sm:ps-6"></th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Nama Lengkap
                        </th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Template
                        </th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Mode
                        </th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Berhasil
                        </th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Gagal
                        </th>
                        <th class="px-3 py-3.5 text-start text-sm font-semibold text-gray-950 dark:text-white">
                            Dibuat
                        </th>
                        <th
                            class="px-3 py-3.5 text-end text-sm font-semibold text-gray-950 last-of-type:pe-4 last-of-type:sm:pe-6 dark:text-white">
                            Aksi
                        </th>
                    </tr>
                </thead>

                @forelse ($this->generates as $generate)
                    @php
                        $items = $generate->items ?? collect();
                        $firstLabel = $items->first()?->display_label;
                        $extraCount = $items->count() > 1 ? $items->count() - 1 : 0;
                        $hasMultiple = $items->count() > 1;
                    @endphp
                    <tbody x-data="{ open: false }" class="divide-y divide-gray-100 dark:divide-white/5">

                        <tr
                            class="border-t border-gray-100 transition hover:bg-gray-50 dark:border-white/5 dark:hover:bg-white/5">
                            <td class="px-3 py-4 first-of-type:ps-4 first-of-type:sm:ps-6">
                                @if ($hasMultiple)
                                    <x-filament::icon-button icon="heroicon-o-chevron-right" label="Lihat detail"
                                        x-on:click="open = !open" x-bind:class="open && 'rotate-90'"
                                        class="transition-transform duration-150" />
                                @endif
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-950 dark:text-white">
                                @if ($firstLabel)
                                    {{ $firstLabel }}
                                    @if ($extraCount > 0)
                                        <span class="ms-1 text-xs text-gray-500 dark:text-gray-400">
                                            + {{ $extraCount }} lainnya
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $generate->template?->name ?? '-' }}
                            </td>

                            <td class="px-3 py-4 text-sm">
                                <x-filament::badge :color="$generate->mode === 'manual' ? 'info' : 'warning'">
                                    {{ $generate->mode === 'manual' ? 'Manual' : 'CSV/Excel' }}
                                </x-filament::badge>
                            </td>

                            <td class="px-3 py-4 text-sm">
                                <x-filament::badge color="success">
                                    {{ $generate->total_success }}
                                </x-filament::badge>
                            </td>

                            <td class="px-3 py-4 text-sm">
                                <x-filament::badge color="danger">
                                    {{ $generate->total_failed }}
                                </x-filament::badge>
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $generate->created_at?->format('d M Y H:i') }}
                            </td>

                            <td class="px-3 py-4 text-end last-of-type:pe-4 last-of-type:sm:pe-6">
                                <div class="flex items-center justify-end gap-3">
                                    {{-- mode manual (cuma 1 item): tampil langsung, gak perlu accordion --}}
                                    @if (!$hasMultiple)
                                        @forelse ($items as $item)
                                            @if ($item->file_url)
                                                <x-filament::link :href="$item->file_url" target="_blank"
                                                    icon="heroicon-m-arrow-down-tray" size="sm">
                                                    Download
                                                </x-filament::link>
                                            @endif

                                            <x-filament::link :href="route('certificate', $item->slug)" target="_blank" icon="heroicon-m-eye"
                                                color="success" size="sm">
                                                Visit
                                            </x-filament::link>
                                        @empty
                                            @if ($generate->file_url)
                                                <x-filament::link :href="$generate->file_url" target="_blank"
                                                    icon="heroicon-m-arrow-down-tray" size="sm">
                                                    Download
                                                </x-filament::link>
                                            @endif
                                        @endforelse
                                    @endif

                                    <x-filament::button color="danger" size="xs" icon="heroicon-m-trash"
                                        wire:click="deleteGenerate('{{ $generate->uuid }}')"
                                        wire:confirm="Yakin hapus riwayat ini?">
                                        Hapus
                                    </x-filament::button>
                                </div>
                            </td>
                        </tr>

                        {{-- BARIS DETAIL (accordion body): cuma ada kalau item > 1 --}}
                        @if ($hasMultiple)
                            <tr x-show="open" x-cloak x-transition style="display:none;">
                                <td colspan="8" class="bg-gray-50 px-3 py-3 dark:bg-white/5">
                                    <div class="overflow-hidden rounded-lg ring-1 ring-gray-950/5 dark:ring-white/10">
                                        <div
                                            class="divide-y divide-gray-100 bg-white dark:divide-white/5 dark:bg-gray-900">
                                            @foreach ($items as $item)
                                                <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                                                    <span class="text-sm text-gray-950 dark:text-white">
                                                        {{ $item->display_label }}
                                                    </span>

                                                    <div class="flex shrink-0 items-center gap-3">
                                                        @if ($item->file_url)
                                                            <x-filament::link :href="$item->file_url" target="_blank"
                                                                icon="heroicon-m-arrow-down-tray" size="sm">
                                                                Download
                                                            </x-filament::link>
                                                        @endif

                                                        <x-filament::link :href="route('certificate', $item->slug)" target="_blank"
                                                            icon="heroicon-m-eye" color="success" size="sm">
                                                            Visit
                                                        </x-filament::link>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-3 py-12">
                                <div class="flex flex-col items-center justify-center gap-3 text-center">
                                    <x-filament::icon icon="heroicon-o-document-duplicate"
                                        class="h-10 w-10 text-gray-400 dark:text-gray-500" />
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada sertifikat yang di-generate.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
    </div>

    <script>
        window.addEventListener('open-download', event => {
            const url = event.detail.url ?? (Array.isArray(event.detail) ? event.detail[0]?.url : null);
            if (url) window.open(url, '_blank');
        });
    </script>

</x-filament-panels::page>
