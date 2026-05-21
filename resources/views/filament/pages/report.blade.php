<x-filament-panels::page>

    <div class="space-y-6">
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    </div>

    {{-- LOADING INDICATOR --}}
    <div wire:loading wire:target="generateReport"
        class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mt-4">
        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
        </svg>
        Sedang memproses laporan...
    </div>

    @if (!empty($reportData))
        @php
            $summary = $reportData['summary'] ?? [];
            $data = $reportData['data'] ?? [];
        @endphp

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6">
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

        {{-- TICKET LIST --}}
        <div class="mt-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Daftar Tiket
                <span
                    class="ml-1 text-xs font-normal bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded-full">{{ count($data) }}
                    tiket</span>
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

                    $clientDocuments = [];
                    $progressDocuments = [];

                    foreach ($ticket['documents'] ?? [] as $doc) {
                        if (isset($doc['is_client_document']) && $doc['is_client_document'] === true) {
                            $clientDocuments[] = $doc;
                        } else {
                            $cleanOtherFiles = [];
                            foreach ($doc['other_files'] ?? [] as $of) {
                                if (is_array($of) && ($of['type'] ?? '') === 'client') {
                                    continue;
                                }
                                $cleanOtherFiles[] = $of;
                            }
                            $doc['other_files'] = $cleanOtherFiles;
                            $progressDocuments[] = $doc;
                        }
                    }
                @endphp

                <div x-data="{ open: false }"
                    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors text-left">
                        <div class="flex items-center gap-3 min-w-0">
                            <span
                                class="shrink-0 text-xs font-mono font-bold bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 px-2.5 py-1 rounded-lg">{{ $ticket['ticket_code'] ?? '-' }}</span>
                            <span
                                class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $ticket['ticket_title'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 ml-3">
                            <span
                                class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
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

                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="border-t border-gray-100 dark:border-gray-700/60 px-8 pt-6 pb-6">

                        {{-- DOKUMEN PENDUKUNG CLIENT --}}
                        @if (!empty($clientDocuments))
                            <div class="tp-wrapper"
                                style="font-family: inherit; padding: 0.5rem 1.25rem; margin-bottom: 2rem;">
                                <div class="tp-header"
                                    style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #e5e7eb;">
                                    <span class="tp-header-title"
                                        style="font-size:15px; font-weight:500; color:#111827;">Dokumen Pendukung
                                        Client</span>
                                    <span class="tp-header-count"
                                        style="font-size:11px; color:#6b7280; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:999px; padding:2px 10px;">{{ count($clientDocuments) }}
                                        dokumen</span>
                                </div>
                                <div class="tp-timeline"
                                    style="position:relative; padding-left:0; padding-right:52px; max-width:90%; margin-left:auto; margin-right:0;">
                                    <div class="tp-timeline-line"
                                        style="position:absolute; right:17px; left:auto; top:8px; bottom:8px; width:1px; background:#e5e7eb;">
                                    </div>
                                    @foreach ($clientDocuments as $clientIndex => $clientDoc)
                                        @php
                                            $isLatestClient = $clientIndex === count($clientDocuments) - 1;
                                            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                                            $thumbnailFiles = [];
                                            $otherFiles = [];
                                            foreach ($clientDoc['other_files'] ?? [] as $cf) {
                                                if (is_array($cf)) {
                                                    $fileUrl = $cf['url'] ?? ($cf[0] ?? '');
                                                    $fileName = $cf['name'] ?? basename($fileUrl);
                                                    $fileExt = strtolower(
                                                        $cf['ext'] ?? pathinfo($fileName, PATHINFO_EXTENSION),
                                                    );
                                                    $fileItem = [
                                                        'url' => $fileUrl,
                                                        'name' => $fileName,
                                                        'ext' => $fileExt,
                                                        'size_formatted' => $cf['size_formatted'] ?? '',
                                                    ];
                                                    if (in_array($fileExt, $imageExt)) {
                                                        $thumbnailFiles[] = $fileItem;
                                                    } else {
                                                        $otherFiles[] = $fileItem;
                                                    }
                                                } elseif (is_string($cf) && $cf !== '') {
                                                    $fileExt = strtolower(pathinfo($cf, PATHINFO_EXTENSION));
                                                    $fileItem = [
                                                        'url' => $cf,
                                                        'name' => basename($cf),
                                                        'ext' => $fileExt,
                                                        'size_formatted' => '',
                                                    ];
                                                    if (in_array($fileExt, $imageExt)) {
                                                        $thumbnailFiles[] = $fileItem;
                                                    } else {
                                                        $otherFiles[] = $fileItem;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="tp-entry" style="position:relative; margin-bottom:1.25rem;">
                                            <div class="tp-dot {{ $isLatestClient ? 'is-latest' : '' }}"
                                                style="position:absolute; right:-35px; left:auto; top:12px; width:36px; height:36px; border-radius:50%; background:#fff; border:1px solid #d1d5db; display:flex; align-items:center; justify-content:center; flex-shrink:0; {{ $isLatestClient ? 'background:transparent; border-color:#374151; color:#374151;' : 'color:#d1d5db;' }}">
                                                <svg width="10" height="10" viewBox="0 0 24 24"
                                                    fill="currentColor">
                                                    <circle cx="12" cy="12" r="6" />
                                                </svg>
                                            </div>
                                            <div class="tp-card"
                                                style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1rem 1.25rem; {{ $isLatestClient ? 'border-right:3px solid #374151;' : '' }}">
                                                <div class="tp-meta"
                                                    style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
                                                    <span class="tp-badge"
                                                        style="font-size:11px; font-weight:500; border-radius:999px; padding:2px 10px; background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;">Dokumen
                                                        Client</span>
                                                    @if (!empty($clientDoc['uploaded_at'] ?? ($clientDoc['timestamp'] ?? null)))
                                                        <span class="tp-time"
                                                            style="font-size:11px; color:#9ca3af; text-align:right;">{{ \Carbon\Carbon::parse($clientDoc['uploaded_at'] ?? $clientDoc['timestamp'])->translatedFormat('d F Y, H:i') }}</span>
                                                    @endif
                                                </div>
                                                @if (!empty($thumbnailFiles))
                                                    <div class="tp-thumb-grid"
                                                        style="display:grid; grid-template-columns:repeat(auto-fill, minmax(100px, 100px)); gap:8px; margin-top:8px;">
                                                        @foreach ($thumbnailFiles as $thumb)
                                                            <div class="tp-thumb-item"
                                                                onclick="tpOpenLb('{{ $thumb['url'] }}', 'image')"
                                                                style="width:100px; height:80px; border-radius:8px; overflow:hidden; cursor:pointer; border:1px solid #e5e7eb; background:#f9fafb;">
                                                                <img src="{{ $thumb['url'] }}" alt="Thumbnail"
                                                                    loading="lazy"
                                                                    style="width:100%; height:100%; object-fit:cover; display:block;">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if (!empty($otherFiles))
                                                    <div class="tp-files"
                                                        style="display:flex; flex-direction:column; gap:6px; margin-top:0.75rem;">
                                                        @foreach ($otherFiles as $cf)
                                                            <div class="tp-file-row"
                                                                style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px;">
                                                                <div class="tp-file-left"
                                                                    style="display:flex; align-items:center; gap:10px;">
                                                                    <div class="tp-file-icon"
                                                                        style="width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; flex-shrink:0; background:#dbeafe; color:#1e40af;">
                                                                        {{ strtoupper(substr($cf['ext'], 0, 3)) ?: 'DOC' }}
                                                                    </div>
                                                                    <a href="{{ $cf['url'] }}" target="_blank"
                                                                        class="tp-file-name"
                                                                        style="font-size:13px; color:#1e40af; text-decoration:none;">{{ $cf['name'] }}</a>
                                                                </div>
                                                                @if (!empty($cf['size_formatted']))
                                                                    <span class="tp-file-size"
                                                                        style="font-size:11px; color:#9ca3af; white-space:nowrap;">{{ $cf['size_formatted'] }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- RIWAYAT PROGRESS --}}
                        @if (!empty($progressDocuments))
                            <div class="tp-wrapper" style="font-family: inherit; padding: 0.5rem 1.25rem;">
                                <div class="tp-header"
                                    style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #e5e7eb;">
                                    <span class="tp-header-title"
                                        style="font-size:15px; font-weight:500; color:#111827;">Riwayat Progress</span>
                                    <span class="tp-header-count"
                                        style="font-size:11px; color:#6b7280; background:#f3f4f6; border:1px solid #e5e7eb; border-radius:999px; padding:2px 10px;">{{ count($progressDocuments) }}
                                        entri</span>
                                </div>
                                <div class="tp-timeline"
                                    style="position:relative; padding-left:0; padding-right:52px; max-width:90%; margin-left:auto; margin-right:0;">
                                    <div class="tp-timeline-line"
                                        style="position:absolute; right:17px; left:auto; top:8px; bottom:8px; width:1px; background:#e5e7eb;">
                                    </div>

                                    @foreach ($progressDocuments as $index => $doc)
                                        @php
                                            $isLatest = $index === count($progressDocuments) - 1;
                                            $rawText = $doc['text'] ?? '';
                                            if (is_array($rawText)) {
                                                $htmlContent = implode(' ', $rawText);
                                            } elseif (is_object($rawText)) {
                                                $htmlContent = json_encode($rawText);
                                            } else {
                                                $htmlContent = (string) $rawText;
                                            }

                                            // HAPUS CAPTION GAMBAR
                                            $htmlContent = preg_replace(
                                                '/<figure[^>]*data-trix-attachment[^>]*>.*?<\/figure>/s',
                                                '',
                                                $htmlContent,
                                            );
                                            $htmlContent = preg_replace(
                                                '/<figcaption[^>]*>.*?<\/figcaption>/s',
                                                '',
                                                $htmlContent,
                                            );
                                            $htmlContent = preg_replace(
                                                '/<div class="attachment-gallery[^>]*">.*?<\/div>/s',
                                                '',
                                                $htmlContent,
                                            );

                                            // EKSTRAK GAMBAR DARI RICH EDITOR
                                            $inlineImages = [];
                                            $htmlContent = preg_replace_callback(
                                                '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
                                                function ($matches) use (&$inlineImages) {
                                                    $inlineImages[] = $matches[1];
                                                    return '';
                                                },
                                                $htmlContent,
                                            );

                                            // EKSTRAK DARI data-trix-attachment
                                            preg_match_all(
                                                '/data-trix-attachment="[^"]*"href="([^"]+)"/i',
                                                $rawText,
                                                $trixMatches,
                                            );
                                            foreach ($trixMatches[1] ?? [] as $url) {
                                                if (!in_array($url, $inlineImages)) {
                                                    $inlineImages[] = $url;
                                                }
                                            }

                                            // THUMBNAIL FILES dari thumbnail_files
                                            $thumbnailFiles = [];
                                            foreach ($doc['thumbnail_files'] ?? [] as $tf) {
                                                if (is_array($tf)) {
                                                    $u = $tf['url'] ?? ($tf[0] ?? '');
                                                    if ($u !== '') {
                                                        $thumbnailFiles[] = $u;
                                                    }
                                                } elseif (is_string($tf) && $tf !== '') {
                                                    $thumbnailFiles[] = $tf;
                                                }
                                            }

                                            // GAMBAR DARI FILE LANGSUNG (lampiran progress)
                                            $fileImages = [];
                                            foreach ($doc['file'] ?? [] as $file) {
                                                if (is_string($file) && $file !== '') {
                                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                    if (
                                                        in_array($ext, [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                            'webp',
                                                            'svg',
                                                            'bmp',
                                                        ])
                                                    ) {
                                                        $fileUrl = asset('storage/' . $file);
                                                        $fileImages[] = $fileUrl;
                                                    }
                                                }
                                            }

                                            // EMBEDDED IMAGES
                                            $embeddedImages = [];
                                            foreach ($doc['embedded_images'] ?? [] as $img) {
                                                if (is_array($img)) {
                                                    $u = $img['url'] ?? ($img['path'] ?? ($img[0] ?? ''));
                                                    if ($u !== '') {
                                                        $embeddedImages[] = $u;
                                                    }
                                                } elseif (is_string($img) && $img !== '') {
                                                    $embeddedImages[] = $img;
                                                }
                                            }

                                            // GABUNG SEMUA GAMBAR
                                            $allThumbnails = array_unique(
                                                array_merge(
                                                    $inlineImages,
                                                    $thumbnailFiles,
                                                    $fileImages,
                                                    $embeddedImages,
                                                ),
                                            );

                                            // BERSIHKAN HTML
                                            $cleanHtml = preg_replace('/<img[^>]+>/i', '', $htmlContent);
                                            $cleanHtml = preg_replace(
                                                '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                                                '',
                                                $cleanHtml,
                                            );
                                            $cleanHtml = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $cleanHtml);
                                            $cleanHtml = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $cleanHtml);
                                            $cleanHtml = trim($cleanHtml);

                                            // PDF FILES
                                            $pdfFiles = [];
                                            foreach ($doc['pdf_files'] ?? [] as $pf) {
                                                if (is_array($pf)) {
                                                    $pdfFiles[] = [
                                                        'url' => $pf['url'] ?? '#',
                                                        'name' => $pf['name'] ?? 'Document.pdf',
                                                        'size_formatted' => $pf['size_formatted'] ?? '',
                                                    ];
                                                } elseif (is_string($pf) && $pf !== '') {
                                                    $pdfFiles[] = [
                                                        'url' => $pf,
                                                        'name' => basename($pf),
                                                        'size_formatted' => '',
                                                    ];
                                                }
                                            }

                                            // OTHER FILES
                                            $otherFiles = [];
                                            foreach ($doc['other_files'] ?? [] as $lf) {
                                                if (is_array($lf)) {
                                                    $otherFiles[] = [
                                                        'url' => $lf['url'] ?? '#',
                                                        'name' => $lf['name'] ?? 'File',
                                                        'ext' =>
                                                            $lf['ext'] ??
                                                            pathinfo($lf['name'] ?? '', PATHINFO_EXTENSION),
                                                        'size_formatted' => $lf['size_formatted'] ?? '',
                                                    ];
                                                } elseif (is_string($lf) && $lf !== '') {
                                                    $otherFiles[] = [
                                                        'url' => $lf,
                                                        'name' => basename($lf),
                                                        'ext' => pathinfo($lf, PATHINFO_EXTENSION),
                                                        'size_formatted' => '',
                                                    ];
                                                }
                                            }
                                        @endphp

                                        <div class="tp-entry" style="position:relative; margin-bottom:1.25rem;">
                                            <div class="tp-dot {{ $isLatest ? 'is-latest' : '' }}"
                                                style="position:absolute; right:-35px; left:auto; top:12px; width:36px; height:36px; border-radius:50%; background:#fff; border:1px solid #d1d5db; display:flex; align-items:center; justify-content:center; flex-shrink:0; {{ $isLatest ? 'background:transparent; border-color:#374151; color:#374151;' : 'color:#d1d5db;' }}">
                                                <svg width="10" height="10" viewBox="0 0 24 24"
                                                    fill="currentColor">
                                                    <circle cx="12" cy="12" r="6" />
                                                </svg>
                                            </div>
                                            <div class="tp-card"
                                                style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1rem 1.25rem; {{ $isLatest ? 'border-right:3px solid #374151;' : '' }}">
                                                <div class="tp-meta"
                                                    style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
                                                    <span class="tp-badge {{ $isLatest ? 'is-latest' : '' }}"
                                                        style="font-size:11px; font-weight:500; border-radius:999px; padding:2px 10px; {{ $isLatest ? 'background:transparent; color:#1f2937; border:1px solid #374151;' : 'background:#f3f4f6; color:#6b7280;' }}">
                                                        {{ $isLatest ? 'Terbaru' : 'Sebelumnya' }}
                                                    </span>
                                                    <span class="tp-time"
                                                        style="font-size:11px; color:#9ca3af; text-align:right;">{{ \Carbon\Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}</span>
                                                </div>

                                                {{-- HTML CONTENT --}}
                                                @if (!empty($cleanHtml))
                                                    <div class="tp-body"
                                                        style="font-size:14px; color:#374151; line-height:1.65; margin:0.75rem 0;">
                                                        {!! $cleanHtml !!}
                                                    </div>
                                                @endif

                                                {{-- THUMBNAIL GRID - SEMUA GAMBAR TAMPIL DALAM GRID --}}
                                                @if (!empty($allThumbnails))
                                                    <div class="tp-thumb-grid"
                                                        style="display:grid; grid-template-columns:repeat(auto-fill, minmax(100px, 100px)); gap:8px; margin-top:12px;">
                                                        @foreach ($allThumbnails as $thumbUrl)
                                                            <div class="tp-thumb-item"
                                                                onclick="tpOpenLb('{{ $thumbUrl }}', 'image')"
                                                                style="width:100px; height:80px; border-radius:8px; overflow:hidden; cursor:pointer; border:1px solid #e5e7eb; background:#f9fafb; transition:transform 0.2s ease;">
                                                                <img src="{{ $thumbUrl }}" alt="Thumbnail"
                                                                    loading="lazy"
                                                                    style="width:100%; height:100%; object-fit:cover; display:block;">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- PDF FILES --}}
                                                @if (!empty($pdfFiles))
                                                    <div class="tp-files"
                                                        style="display:flex; flex-direction:column; gap:6px; margin-top:0.75rem;">
                                                        @foreach ($pdfFiles as $pf)
                                                            <div class="tp-file-row"
                                                                style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px;">
                                                                <div class="tp-file-left"
                                                                    style="display:flex; align-items:center; gap:10px;">
                                                                    <div class="tp-file-icon"
                                                                        style="width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; flex-shrink:0; background:#fecaca; color:#991b1b;">
                                                                        PDF</div>
                                                                    <a href="{{ $pf['url'] }}" target="_blank"
                                                                        class="tp-file-name"
                                                                        style="font-size:13px; color:#374151; text-decoration:none;">{{ $pf['name'] }}</a>
                                                                </div>
                                                                @if (!empty($pf['size_formatted']))
                                                                    <span class="tp-file-size"
                                                                        style="font-size:11px; color:#9ca3af; white-space:nowrap;">{{ $pf['size_formatted'] }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                {{-- OTHER FILES --}}
                                                @if (!empty($otherFiles))
                                                    <div class="tp-files"
                                                        style="display:flex; flex-direction:column; gap:6px; margin-top:0.75rem;">
                                                        @foreach ($otherFiles as $lf)
                                                            <div class="tp-file-row"
                                                                style="display:flex; align-items:center; justify-content:space-between; padding:8px 12px; background:#f9fafb; border:1px solid #f3f4f6; border-radius:8px;">
                                                                <div class="tp-file-left"
                                                                    style="display:flex; align-items:center; gap:10px;">
                                                                    <div class="tp-file-icon"
                                                                        style="width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; flex-shrink:0; background:rgba(55,65,81,0.15); color:#374151;">
                                                                        {{ strtoupper(substr($lf['ext'], 0, 3)) ?: 'FIL' }}
                                                                    </div>
                                                                    <a href="{{ $lf['url'] }}" target="_blank"
                                                                        class="tp-file-name"
                                                                        style="font-size:13px; color:#374151; text-decoration:none;">{{ $lf['name'] }}</a>
                                                                </div>
                                                                @if (!empty($lf['size_formatted']))
                                                                    <span class="tp-file-size"
                                                                        style="font-size:11px; color:#9ca3af; white-space:nowrap;">{{ $lf['size_formatted'] }}</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Belum ada progress.</p>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @else
        @if (!$isLoading ?? false)
            <div class="mt-6 flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada laporan</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Atur filter di atas lalu klik <strong>Lihat
                        Progress</strong></p>
            </div>
        @endif
    @endif

    {{-- LIGHTBOX --}}
    <div id="tp-lightbox" onclick="if(event.target===this)tpCloseLb()"
        style="position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.88); display:none; align-items:center; justify-content:center; padding:1rem;">
        <div class="tp-lb-inner" style="position:relative; max-width:90vw; max-height:90vh;">
            <button type="button" onclick="tpCloseLb()"
                style="position:absolute; top:-36px; right:0; background:none; border:none; cursor:pointer; color:#fff; opacity:0.75; display:flex; align-items:center; justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>
            <img id="tp-lb-img" src="" alt="Preview"
                style="display:none; max-width:90vw; max-height:88vh; object-fit:contain; border-radius:10px;">
            <video id="tp-lb-vid" controls
                style="display:none; max-width:90vw; max-height:88vh; object-fit:contain; border-radius:10px;"></video>
        </div>
    </div>

</x-filament-panels::page>

@push('styles')
    <style>
        /* DARK MODE */
        .dark .tp-header {
            border-bottom-color: #27272a !important;
        }

        .dark .tp-header-title {
            color: #fafafa !important;
        }

        .dark .tp-header-count {
            background: #18181b !important;
            border-color: #27272a !important;
            color: #a1a1aa !important;
        }

        .dark .tp-timeline-line {
            background: #27272a !important;
        }

        .dark .tp-dot {
            background: #09090b !important;
            border-color: #3f3f46 !important;
            color: #3f3f46 !important;
        }

        .dark .tp-dot.is-latest {
            background: transparent !important;
            border-color: #52525b !important;
            color: #a1a1aa !important;
        }

        .dark .tp-card {
            background: #09090b !important;
            border-color: #27272a !important;
        }

        .dark .tp-badge {
            background: #18181b !important;
            color: #e4e4e7 !important;
        }

        .dark .tp-badge.is-latest {
            background: transparent !important;
            border-color: #52525b !important;
            color: #e4e4e7 !important;
        }

        .dark .tp-time {
            color: #71717a !important;
        }

        .dark .tp-body {
            color: #e4e4e7 !important;
        }

        .dark .tp-thumb-item {
            border-color: #3f3f46 !important;
            background: #18181b !important;
        }

        .dark .tp-file-row {
            background: #18181b !important;
            border-color: #27272a !important;
        }

        .dark .tp-file-icon {
            background: rgba(161, 161, 170, 0.15) !important;
            color: #e4e4e7 !important;
        }

        .dark .tp-file-name {
            color: #e4e4e7 !important;
        }

        .dark .tp-file-name:hover {
            color: #fafafa !important;
        }

        .dark .tp-file-size {
            color: #71717a !important;
        }

        /* THUMBNAIL GRID */
        .tp-thumb-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 100px));
            gap: 8px;
        }

        .tp-thumb-item:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* BODY STYLE */
        .tp-body a {
            color: #2563eb;
            text-decoration: underline;
        }

        .dark .tp-body a {
            color: #60a5fa;
        }

        .tp-body p {
            margin-bottom: 0.75rem;
        }

        .tp-body ul,
        .tp-body ol {
            margin-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .tp-body li {
            margin-bottom: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function tpOpenLb(url, type) {
            const lb = document.getElementById('tp-lightbox');
            const img = document.getElementById('tp-lb-img');
            const vid = document.getElementById('tp-lb-vid');
            if (type === 'image') {
                img.src = url;
                img.style.display = 'block';
                vid.style.display = 'none';
                vid.src = '';
            } else {
                vid.src = url;
                vid.style.display = 'block';
                img.style.display = 'none';
                img.src = '';
            }
            lb.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function tpCloseLb() {
            const lb = document.getElementById('tp-lightbox');
            const vid = document.getElementById('tp-lb-vid');
            if (vid && !vid.paused) vid.pause();
            lb.style.display = 'none';
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tpCloseLb();
        });
        document.addEventListener('livewire:init', function() {
            Livewire.on('open-download-url', function(data) {
                var url = data.url || (data[0]?.url);
                if (url) window.open(url, '_blank');
            });
        });
    </script>
@endpush
