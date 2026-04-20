@php
    use App\Models\Ticket;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $recordId = request()->route('record');
    $ticket = Ticket::where('ticket_code', $recordId)->first();
    $progress = $ticket?->ticket_progress ?? [];

    if (!function_exists('formatFileSize')) {
        function formatFileSize($bytes)
        {
            if ($bytes == 0) {
                return '0 B';
            }
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }
    }

    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv'];
    $pdfExt = ['pdf'];
    $docExt = ['doc', 'docx'];
    $excelExt = ['xls', 'xlsx', 'csv'];
    $archiveExt = ['zip', 'rar', '7z', 'tar', 'gz'];
    $audioExt = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
@endphp

<div class="tp-wrapper" style="font-family: inherit; padding: 1rem 0;">
    <div class="tp-header"
        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
        <span class="tp-header-title" style="font-size: 15px; font-weight: 500; color: #111827;">Riwayat Progress</span>
        <span class="tp-header-count"
            style="font-size: 11px; color: #6b7280; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 999px; padding: 2px 10px;">{{ count($progress) }}
            entri</span>
    </div>

    @if (count($progress) > 0)
        <div class="tp-timeline"
            style="position: relative; padding-left: 0; padding-right: 52px; max-width: 90%; margin-left: auto; margin-right: 0; max-height: 90vh; overflow-y: auto;">
            <div class="tp-timeline-line"
                style="position: absolute; right: 17px; left: auto; top: 8px; bottom: 8px; width: 1px; background: #e5e7eb;">
            </div>

            @foreach ($progress as $index => $item)
                @php
                    // Untuk menentukan yang terbaru, cek apakah ini item terakhir dalam array (karena sekarang urutan lama ke baru)
                    $isLatest = $index === count($progress) - 1;

                    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $item['progress'] ?? '', $imgMatches);
                    $richImgUrls = $imgMatches[1] ?? [];

                    $textOnly = preg_replace('/<img[^>]+>/i', '', $item['progress'] ?? '');
                    $textOnly = preg_replace(
                        '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                        '',
                        $textOnly,
                    );
                    $textOnly = preg_replace(
                        '/[\w\-\.]+\.(jpg|jpeg|png|gif|webp|svg|bmp)\s*[\d\.,]+\s*(B|KB|MB|GB|TB)/i',
                        '',
                        $textOnly,
                    );
                    $textOnly = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $textOnly);
                    $textOnly = trim($textOnly);

                    $thumbFiles = [];
                    $listFiles = [];

                    foreach ($item['file'] ?? [] as $file) {
                        $ext = Str::lower(pathinfo($file, PATHINFO_EXTENSION));
                        $fileSize = Storage::exists('public/' . $file) ? Storage::size('public/' . $file) : 0;
                        $fileUrl = asset('storage/' . $file);
                        $fileName = basename($file);
                        $fileSizeFormatted = $fileSize > 0 ? formatFileSize($fileSize) : '';

                        if (in_array($ext, $imageExtensions) || in_array($ext, $videoExtensions)) {
                            $thumbFiles[] = compact(
                                'ext',
                                'file',
                                'fileUrl',
                                'fileName',
                                'fileSizeFormatted',
                                'fileSize',
                            );
                        } else {
                            $listFiles[] = compact('ext', 'file', 'fileUrl', 'fileName', 'fileSizeFormatted');
                        }
                    }

                    $hasFiles = count($thumbFiles) || count($listFiles) || count($richImgUrls);
                    $hasDivider = trim(strip_tags($textOnly)) && $hasFiles;
                @endphp

                <div class="tp-entry" style="position: relative; margin-bottom: 1.25rem;">
                    <div class="tp-dot {{ $isLatest ? 'is-latest' : '' }}"
                        style="position: absolute; right: -35px; left: auto; top: 12px; width: 36px; height: 36px; border-radius: 50%; background: #fff; border: 1px solid #d1d5db; display: flex; align-items: center; justify-content: center; flex-shrink: 0; {{ $isLatest ? 'background: transparent; border-color: #374151; color: #374151;' : 'color: #d1d5db;' }}">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="12" cy="12" r="6" />
                        </svg>
                    </div>

                    <div class="tp-card"
                        style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 1rem 1.25rem; transition: border-color 0.2s; {{ $isLatest ? 'border-right: 3px solid #374151;' : '' }}">
                        <div class="tp-meta"
                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span class="tp-badge {{ $isLatest ? 'is-latest' : '' }}"
                                style="font-size: 11px; font-weight: 500; border-radius: 999px; padding: 2px 10px; {{ $isLatest ? 'background: transparent; color: #1f2937; border: 1px solid #374151;' : 'background: #f3f4f6; color: #6b7280;' }}">
                                {{ $isLatest ? 'Terbaru' : 'Sebelumnya' }}
                            </span>
                            <span class="tp-time" style="font-size: 11px; color: #9ca3af; text-align: right;">
                                {{ \Carbon\Carbon::parse($item['timestamp'])->translatedFormat('d F Y, H:i') }}
                            </span>
                        </div>

                        @if (trim(strip_tags($textOnly)))
                            <div class="tp-body"
                                style="font-size: 14px; color: #374151; line-height: 1.65; margin-bottom: 0.75rem;">
                                {!! $textOnly !!}
                            </div>
                        @endif

                        @if ($hasDivider)
                            <div class="tp-divider" style="height: 1px; background: #f3f4f6; margin: 0.75rem 0;"></div>
                        @endif

                        @if (count($thumbFiles) || count($richImgUrls))
                            <div class="tp-thumbs"
                                style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 0.5rem;">
                                @foreach ($thumbFiles as $tf)
                                    @if (in_array($tf['ext'], $imageExtensions))
                                        <div class="tp-thumb" onclick="tpOpenLb('{{ $tf['fileUrl'] }}', 'image')"
                                            style="width: 100px; height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 1px solid #e5e7eb; position: relative; flex-shrink: 0; background: #f9fafb;">
                                            <img src="{{ $tf['fileUrl'] }}" alt="{{ $tf['fileName'] }}" loading="lazy"
                                                style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                        </div>
                                    @elseif (in_array($tf['ext'], $videoExtensions))
                                        <div class="tp-thumb" onclick="tpOpenLb('{{ $tf['fileUrl'] }}', 'video')"
                                            style="width: 100px; height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 1px solid #e5e7eb; position: relative; flex-shrink: 0; background: #f9fafb;">
                                            <video src="{{ $tf['fileUrl'] }}" muted preload="metadata"
                                                style="width: 100%; height: 100%; object-fit: cover; display: block;"></video>
                                            <div class="tp-play-overlay"
                                                style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.35);">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @foreach ($richImgUrls as $imgUrl)
                                    <div class="tp-thumb" onclick="tpOpenLb('{{ $imgUrl }}', 'image')"
                                        style="width: 100px; height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 1px solid #e5e7eb; position: relative; flex-shrink: 0; background: #f9fafb;">
                                        <img src="{{ $imgUrl }}" alt="Gambar" loading="lazy"
                                            style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (count($listFiles))
                            <div class="tp-files"
                                style="display: flex; flex-direction: column; gap: 6px; {{ count($thumbFiles) || count($richImgUrls) ? 'margin-top:0.75rem;' : '' }}">
                                @foreach ($listFiles as $lf)
                                    @php
                                        $ext = $lf['ext'];
                                        $iconClass = match (true) {
                                            in_array($ext, $pdfExt) => 'tp-icon-pdf',
                                            in_array($ext, $docExt) => 'tp-icon-doc',
                                            in_array($ext, $excelExt) => 'tp-icon-xls',
                                            in_array($ext, $archiveExt) => 'tp-icon-zip',
                                            in_array($ext, $audioExt) => 'tp-icon-aud',
                                            in_array($ext, $imageExtensions) => 'tp-icon-img',
                                            default => 'tp-icon-gen',
                                        };
                                        $iconLabel = strtoupper(substr($ext, 0, 3)) ?: 'FIL';
                                        $isDownload = in_array($ext, $archiveExt);
                                    @endphp
                                    <div class="tp-file-row"
                                        style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; transition: border-color 0.15s;">
                                        <div class="tp-file-left"
                                            style="display: flex; align-items: center; gap: 10px;">
                                            <div class="tp-file-icon {{ $iconClass }}"
                                                style="width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600; letter-spacing: 0.02em; flex-shrink: 0; background: rgba(55, 65, 81, 0.15); color: #374151;">
                                                {{ $iconLabel }}
                                            </div>
                                            <a href="{{ $lf['fileUrl'] }}"
                                                {{ $isDownload ? 'download' : 'target="_blank"' }} class="tp-file-name"
                                                style="font-size: 13px; color: #374151; text-decoration: none; transition: color 0.15s;">
                                                {{ $lf['fileName'] }}
                                            </a>
                                        </div>
                                        @if ($lf['fileSizeFormatted'])
                                            <span class="tp-file-size"
                                                style="font-size: 11px; color: #9ca3af; white-space: nowrap;">{{ $lf['fileSizeFormatted'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="tp-empty"
            style="text-align: center; padding: 2.5rem; color: #9ca3af; font-size: 14px; border: 1px dashed #e5e7eb; border-radius: 12px;">
            Belum ada progress yang tercatat
        </div>
    @endif
</div>

<div id="tp-lightbox" onclick="if(event.target===this)tpCloseLb()"
    style="position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.88); display: none; align-items: center; justify-content: center; padding: 1rem;">
    <div class="tp-lb-inner" style="position: relative; max-width: 90vw; max-height: 90vh;">
        <button type="button" class="tp-lb-close" onclick="tpCloseLb()"
            style="position: absolute; top: -36px; right: 0; background: none; border: none; cursor: pointer; color: #fff; opacity: 0.75; display: flex; align-items: center; justify-content: center;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
        </button>
        <img id="tp-lb-img" src="" alt="Preview"
            style="display:none; max-width: 90vw; max-height: 88vh; object-fit: contain; border-radius: 10px; display: block;">
        <video id="tp-lb-vid" controls
            style="display:none; max-width: 90vw; max-height: 88vh; object-fit: contain; border-radius: 10px; display: block;"></video>
    </div>
</div>

<style>
    /* Light mode styles (default) */
    .tp-header {
        border-bottom-color: #e5e7eb !important;
    }

    .tp-header-title {
        color: #111827 !important;
    }

    .tp-header-count {
        background: #f3f4f6 !important;
        border-color: #e5e7eb !important;
        color: #6b7280 !important;
    }

    .tp-timeline-line {
        background: #e5e7eb !important;
    }

    .tp-dot {
        background: #fff !important;
        border-color: #d1d5db !important;
        color: #d1d5db !important;
    }

    .tp-dot.is-latest {
        background: transparent !important;
        border-color: #374151 !important;
        color: #374151 !important;
    }

    .tp-card {
        background: #fff !important;
        border-color: #e5e7eb !important;
    }

    .tp-badge {
        background: #f3f4f6 !important;
        color: #6b7280 !important;
    }

    .tp-badge.is-latest {
        background: transparent !important;
        border-color: #374151 !important;
        color: #1f2937 !important;
    }

    .tp-time {
        color: #9ca3af !important;
    }

    .tp-body {
        color: #374151 !important;
    }

    .tp-divider {
        background: #f3f4f6 !important;
    }

    .tp-thumb {
        border-color: #e5e7eb !important;
        background: #f9fafb !important;
    }

    .tp-file-row {
        background: #f9fafb !important;
        border-color: #f3f4f6 !important;
    }

    .tp-file-icon {
        background: rgba(55, 65, 81, 0.15) !important;
        color: #374151 !important;
    }

    .tp-file-name {
        color: #374151 !important;
    }

    .tp-file-name:hover {
        color: #4b5563 !important;
        text-decoration: underline !important;
    }

    .tp-file-size {
        color: #9ca3af !important;
    }

    .tp-empty {
        border-color: #e5e7eb !important;
        color: #9ca3af !important;
    }

    /* Dark mode styles - updated to #09090b */
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

    .dark .tp-card:hover {
        border-color: #3f3f46 !important;
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

    .dark .tp-divider {
        background: #27272a !important;
    }

    .dark .tp-thumb {
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

    .dark .tp-empty {
        border-color: #27272a !important;
        color: #71717a !important;
    }
</style>

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
</script>
