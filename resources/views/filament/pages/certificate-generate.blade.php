<x-filament-panels::page>

    {{-- tabel template (atas) --}}
    {{ $this->table }}

    {{-- tabel riwayat generate (bawah, dirender manual) --}}
    <div class="mt-8">
        <h2 class="text-base font-semibold mb-3">Data yang sudah di-generate</h2>

        <div class="fi-ta-ctn rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="fi-ta-table w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left">Template</th>
                        <th class="px-4 py-2 text-left">Mode</th>
                        <th class="px-4 py-2 text-left">Berhasil</th>
                        <th class="px-4 py-2 text-left">Gagal</th>
                        <th class="px-4 py-2 text-left">Dibuat</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->generates as $generate)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $generate->template?->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $generate->mode === 'manual' ? 'Manual' : 'CSV/Excel' }}</td>
                            <td class="px-4 py-2">{{ $generate->total_success }}</td>
                            <td class="px-4 py-2">{{ $generate->total_failed }}</td>
                            <td class="px-4 py-2">{{ $generate->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                @if ($generate->file_url)
                                    <a href="{{ $generate->file_url }}" target="_blank"
                                        class="fi-btn fi-btn-size-xs inline-flex items-center px-2 py-1 rounded-md bg-primary-600 text-white">
                                        Download
                                    </a>
                                @endif
                                <button type="button" wire:click="deleteGenerate('{{ $generate->uuid }}')"
                                    wire:confirm="Yakin hapus riwayat ini?"
                                    class="fi-btn fi-btn-size-xs inline-flex items-center px-2 py-1 rounded-md bg-danger-600 text-white">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                Belum ada sertifikat yang di-generate.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
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
