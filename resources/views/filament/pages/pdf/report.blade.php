<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Progress - {{ $summary['ticket_code'] ?? '-' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1f2937;
            margin: 24px 28px;
            background: white;
        }

        /* ===================== HEADER ===================== */
        .header-wrapper {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .header-wrapper td.logo-cell {
            width: 90px;
            vertical-align: middle;
            text-align: center;
            padding-right: 12px;
            border: none;
        }

        .header-wrapper td.info-cell {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .header-logo img {
            width: 80px;
            height: auto;
            max-height: 72px;
        }

        .logo-placeholder {
            width: 80px;
            height: 60px;
            background: #1e3a5f;
            border-radius: 6px;
            display: inline-block;
            vertical-align: middle;
            text-align: center;
            padding-top: 14px;
        }

        .logo-placeholder span {
            color: white;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header-company-name {
            font-size: 9px;
            color: #6b7280;
            margin-top: 5px;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .header-table td {
            border: 1px solid #d1d5db;
            padding: 6px 10px;
            vertical-align: middle;
            text-align: left;
        }

        .header-table .label {
            background-color: #f0f4f8;
            font-weight: bold;
            color: #374151;
            white-space: nowrap;
            width: 110px;
        }

        .header-table .val {
            color: #1f2937;
        }

        /* ===================== SECTION TITLE ===================== */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 16px;
            margin-top: 22px;
            color: #1e3a5f;
            text-align: left;
            letter-spacing: 0.2px;
        }

        /* ===================== BADGE ===================== */
        .badge-client {
            background: #f3f4f6;
            color: #6b7280;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-latest {
            border: 1.5px solid #1e3a5f;
            background: transparent;
            color: #1e3a5f;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-previous {
            background: #f3f4f6;
            color: #6b7280;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: 600;
            display: inline-block;
        }

        .date-wrapper {
            color: #9ca3af;
            font-size: 10px;
        }

        /* ===================== CARD SECTIONS ===================== */
        .client-section {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .client-title {
            color: #8b5cf6;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
            width: 92%;
        }

        .progress-section {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .progress-title {
            font-weight: bold;
            margin-bottom: 12px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: flex-end;
            width: 92%;
            color: #1f2937;
        }

        .card {
            width: 92%;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px 16px;
            background: white;
            margin-left: auto;
            margin-right: 0;
        }

        .card-header {
            width: 100%;
            margin-bottom: 12px;
        }

        .card-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .card-header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        /* ===================== PROGRESS TEXT ===================== */
        .progress-text {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
            font-size: 11px;
            line-height: 1.6;
            color: #374151;
        }

        .progress-text h1 {
            font-size: 15px;
            font-weight: bold;
            margin: 14px 0 10px 0;
            color: #1f2937;
        }

        .progress-text h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 12px 0 8px 0;
            color: #1f2937;
        }

        .progress-text h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0 6px 0;
            color: #1f2937;
        }

        .progress-text p {
            margin: 6px 0;
            line-height: 1.6;
        }

        .progress-text strong,
        .progress-text b {
            font-weight: bold;
            color: #1f2937;
        }

        .progress-text em,
        .progress-text i {
            font-style: italic;
        }

        .progress-text ul {
            margin: 6px 0;
            padding-left: 22px;
            list-style-type: disc;
        }

        .progress-text ol {
            margin: 6px 0;
            padding-left: 22px;
            list-style-type: decimal;
        }

        .progress-text li {
            margin: 3px 0;
            line-height: 1.6;
        }

        .progress-text blockquote {
            border-left: 3px solid #e5e7eb;
            padding-left: 14px;
            margin: 10px 0;
            color: #6b7280;
            font-style: italic;
        }

        /* ===================== FILE LIST ===================== */
        .file-list {
            margin-top: 10px;
            width: 100%;
        }

        .file-item {
            width: 100%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-bottom: 4px;
            padding: 0;
        }

        .file-item table {
            width: 100%;
            border-collapse: collapse;
        }

        .file-item td {
            border: none;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .file-ext-badge {
            font-size: 8px;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
            white-space: nowrap;
        }

        .file-ext-pdf {
            background: #fee2e2;
            color: #b91c1c;
        }

        .file-ext-other {
            background: #e5e7eb;
            color: #374151;
        }

        .file-name {
            font-size: 10px;
            color: #1f2937;
        }

        .file-size {
            font-size: 9px;
            color: #9ca3af;
            text-align: right;
            white-space: nowrap;
        }

        .thumbnail-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .thumbnail {
            width: 68px;
            height: 68px;
            object-fit: cover;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }

        /* ===================== LAMPIRAN ===================== */
        .page-break {
            page-break-before: always;
        }

        .lampiran-cover {
            background: #f9fafb;
            margin: 0 -28px;
            padding: 0 28px 0 28px;
        }

        .lampiran-cover-line {
            background: #1f2937;
            height: 2.5px;
            margin: 0 -28px;
        }

        .lampiran-cover-body {
            padding: 18px 0 14px 0;
            text-align: center;
        }

        .lampiran-cover-title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            letter-spacing: 0.8px;
        }

        .lampiran-cover-sub {
            font-size: 10.5px;
            color: #6b7280;
            margin-top: 6px;
        }

        .lampiran-list-area {
            padding: 20px 0 0 0;
        }

        .lampiran-list-heading {
            font-size: 11.5px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }

        .lampiran-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .lampiran-row td {
            border: none;
            padding: 2px 0;
            vertical-align: middle;
            font-size: 10.5px;
            color: #1f2937;
        }

        .lamp-no-col {
            width: 32px;
            color: #374151;
        }

        .lamp-name-col {
            color: #1f2937;
        }

        .lamp-url {
            font-size: 8.5px;
            color: #9ca3af;
            margin-top: 1px;
            word-break: break-all;
        }

        .lamp-type-col {
            width: 70px;
            text-align: right;
            font-style: italic;
            color: #8b5cf6;
            font-size: 10px;
        }

        .lamp-ticket-col {
            width: 130px;
            text-align: right;
            color: #9ca3af;
            font-size: 10px;
        }

        /* ===================== FOOTER ===================== */
        @page {
            margin-bottom: 40px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9.5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding: 8px 28px 6px 28px;
            background: white;
        }

        .summary-box {
            background: #f8fafc;
            border-left: 3px solid #1e3a5f;
            border-radius: 0 6px 6px 0;
            padding: 12px 16px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    @php $attachedPdfs = $attachedPdfs ?? []; @endphp

    <!-- ===== HEADER ===== -->
    <table class="header-wrapper" style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <tr>
            <td class="logo-cell"
                style="width:90px; vertical-align:middle; text-align:center; padding-right:12px; border:none;">
                <img src="{{ public_path('storage/meta/01KF044971QVAFZJTQ6V5MKX3T.png') }}" alt="Logo"
                    style="width:80px; height:auto; max-height:72px;">
                @if (!empty($summary['company_name']))
                    <div class="header-company-name">{{ Str::limit($summary['company_name'], 16) }}</div>
                @endif
            </td>
            <td class="info-cell" style="vertical-align:top; border:none; padding:0;">
                <table class="header-table">
                    <tr>
                        <td class="label">ID Laporan</td>
                        <td class="val">{{ $summary['ticket_code'] ?? '-' }}</td>
                        <td class="label">Klien</td>
                        <td class="val">{{ Str::limit($summary['client_name'] ?? '-', 28) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td class="val" colspan="3">{{ Str::limit($summary['proposal_for'] ?? '-', 70) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dilaporkan oleh</td>
                        <td class="val">{{ $summary['generated_by'] ?? '-' }}</td>
                        <td class="label">Periode Laporan</td>
                        <td class="val">{{ $summary['date_range'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ===== SUMMARY / CATATAN ===== -->
    @if (!empty($notes))
        <div class="section-title">Summary / Catatan</div>
        <div class="summary-box">
            <div class="progress-text">
                {!! $notes !!}
            </div>
        </div>
    @endif

    <!-- ===== PROGRES PEKERJAAN ===== -->
    <div class="section-title">Progres Pekerjaan</div>

    @foreach ($data as $ticket)
        @php
            $docs = $ticket['documents'] ?? [];
            $hasClientDocs = false;
            $progressDocs = [];
            foreach ($docs as $doc) {
                if ($doc['is_client_document'] ?? false) {
                    $hasClientDocs = true;
                } else {
                    $progressDocs[] = $doc;
                }
            }
        @endphp

        <!-- DOKUMEN CLIENT -->
        @if ($hasClientDocs)
            <div class="client-section">
                <div class="client-title">Dokumen Pendukung Client</div>
                @foreach ($docs as $doc)
                    @if ($doc['is_client_document'] ?? false)
                        <div class="card">
                            <div class="card-header">
                                <table>
                                    <tr>
                                        <td style="text-align:left; border:none; padding:0; vertical-align:middle;">
                                            <span class="badge-client">Dokumen Client</span>
                                        </td>
                                        <td style="text-align:right; border:none; padding:0; vertical-align:middle;">
                                            <span
                                                class="date-wrapper">{{ \Carbon\Carbon::parse($doc['timestamp'])->translatedFormat('d F Y, H:i') }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            @php
                                // FILTER CLIENT TEXT: HANYA HAPUS GAMBAR DAN CAPTION, PERTAHANKAN DESKRIPSI
                                $clientRawText = $doc['text_html'] ?? '';
                                // Hapus seluruh figure block (gambar + caption)
                                $clientCleanText = preg_replace(
                                    '/<figure[^>]*data-trix-attachment[^>]*>.*?<\/figure>/s',
                                    '',
                                    $clientRawText,
                                );
                                // Hapus sisa tag yang tidak perlu
                                $clientCleanText = preg_replace(
                                    '/<div class="attachment-gallery[^>]*">.*?<\/div>/s',
                                    '',
                                    $clientCleanText,
                                );
                                $clientCleanText = preg_replace(
                                    '/<figcaption[^>]*>.*?<\/figcaption>/s',
                                    '',
                                    $clientCleanText,
                                );
                                $clientCleanText = preg_replace('/<img[^>]+>/i', '', $clientCleanText);
                                $clientCleanText = preg_replace(
                                    '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                                    '',
                                    $clientCleanText,
                                );
                                $clientCleanText = preg_replace('/&nbsp;/', ' ', $clientCleanText);
                                $clientCleanText = trim($clientCleanText);
                            @endphp

                            @if (!empty($clientCleanText))
                                <div class="progress-text">
                                    {!! $clientCleanText !!}
                                </div>
                            @endif

                            @if (!empty($doc['other_files']))
                                <div class="file-list">
                                    @foreach ($doc['other_files'] as $file)
                                        @if (in_array(strtolower($file['ext'] ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            @php
                                                $imgSrc = null;
                                                if (!empty($file['local_path']) && file_exists($file['local_path'])) {
                                                    $imgSrc = $file['local_path'];
                                                } elseif (!empty($file['file'])) {
                                                    $p = storage_path('app/public/' . ltrim($file['file'], '/'));
                                                    if (file_exists($p)) {
                                                        $imgSrc = $p;
                                                    }
                                                }
                                            @endphp
                                            @if ($imgSrc)
                                                <img src="{{ $imgSrc }}" class="thumbnail">
                                            @endif
                                        @else
                                            <div class="file-item">
                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:36px;">
                                                            <span
                                                                class="file-ext-badge {{ strtolower($file['ext'] ?? '') === 'pdf' ? 'file-ext-pdf' : 'file-ext-other' }}">
                                                                {{ strtoupper(substr($file['ext'] ?? 'FILE', 0, 4)) }}
                                                            </span>
                                                        </td>
                                                        <td class="file-name">{{ $file['name'] }}</td>
                                                        <td class="file-size">{{ $file['size_formatted'] ?? '' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- RIWAYAT PROGRESS -->
        @if (!empty($progressDocs))
            <div class="progress-section">
                <div class="progress-title">Riwayat Progress</div>
                @foreach ($progressDocs as $idx => $doc)
                    <div class="card">
                        <div class="card-header">
                            <table>
                                <tr>
                                    <td style="text-align:left; border:none; padding:0; vertical-align:middle;">
                                        @if ($idx === count($progressDocs) - 1)
                                            <span class="badge-latest">Terbaru</span>
                                        @else
                                            <span class="badge-previous">Sebelumnya</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; border:none; padding:0; vertical-align:middle;">
                                        <span
                                            class="date-wrapper">{{ \Carbon\Carbon::parse($doc['timestamp'])->translatedFormat('d F Y, H:i') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @php
                            // ============================================================
                            // FILTER PROGRESS TEXT: HANYA HAPUS GAMBAR DAN CAPTION
                            // PERTAHANKAN SEMUA TEKS DESKRIPSI (proses Pencabutan., list, dll)
                            // ============================================================
                            $rawText = $doc['text_html'] ?? '';

                            // HAPUS SELURUH FIGURE BLOCK (gambar + caption) - INI KUNCI UTAMANYA
                            $cleanText = preg_replace(
                                '/<figure[^>]*data-trix-attachment[^>]*>.*?<\/figure>/s',
                                '',
                                $rawText,
                            );

                            // BERSIHKAN SISA TAG YANG TIDAK PERLU
                            $cleanText = preg_replace(
                                '/<div class="attachment-gallery[^>]*">.*?<\/div>/s',
                                '',
                                $cleanText,
                            );
                            $cleanText = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/s', '', $cleanText);
                            $cleanText = preg_replace('/<img[^>]+>/i', '', $cleanText);
                            $cleanText = preg_replace(
                                '/<a[^>]*href=["\'][^"\']*\.(jpg|jpeg|png|gif|webp|svg|bmp)["\'][^>]*>.*?<\/a>/is',
                                '',
                                $cleanText,
                            );

                            // HANYA HAPUS TAG KOSONG, BUKAN TEKS NYA
                            $cleanText = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $cleanText);
                            $cleanText = preg_replace('/<div[^>]*>\s*<\/div>/i', '', $cleanText);
                            $cleanText = preg_replace('/&nbsp;/', ' ', $cleanText);
                            $cleanText = trim($cleanText);
                        @endphp

                        @if (!empty($cleanText))
                            <div class="progress-text">
                                {!! $cleanText !!}
                            </div>
                        @endif

                        @if (!empty($doc['thumbnail_files']) || !empty($doc['embedded_images']))
                            @php
                                $allThumbs = [];
                                foreach ($doc['thumbnail_files'] as $thumb) {
                                    $imgSrc = null;
                                    if (!empty($thumb['local_path']) && file_exists($thumb['local_path'])) {
                                        $imgSrc = $thumb['local_path'];
                                    } elseif (!empty($thumb['file'])) {
                                        $p = storage_path('app/public/' . ltrim($thumb['file'], '/'));
                                        if (file_exists($p)) {
                                            $imgSrc = $p;
                                        }
                                    }
                                    if ($imgSrc) {
                                        $allThumbs[] = $imgSrc;
                                    }
                                }
                                foreach ($doc['embedded_images'] as $img) {
                                    if (!empty($img['path']) && file_exists($img['path'])) {
                                        $allThumbs[] = $img['path'];
                                    }
                                }
                                $perRow = 5;
                                $chunks = array_chunk($allThumbs, $perRow);
                            @endphp
                            @if (!empty($allThumbs))
                                <table
                                    style="width:100%; border-collapse:collapse; margin-top:10px; margin-bottom:4px;">
                                    @foreach ($chunks as $row)
                                        <tr>
                                            @foreach ($row as $imgSrc)
                                                <td
                                                    style="padding:2px; border:none; width:{{ floor(100 / $perRow) }}%;">
                                                    <img src="{{ $imgSrc }}"
                                                        style="width:100%; height:72px; object-fit:cover; border:1px solid #e5e7eb; border-radius:4px; display:block;">
                                                </td>
                                            @endforeach
                                            @for ($pad = count($row); $pad < $perRow; $pad++)
                                                <td style="border:none; width:{{ floor(100 / $perRow) }}%;"></td>
                                            @endfor
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        @endif

                        @if (!empty($doc['pdf_files']) || !empty($doc['other_files']))
                            <div class="file-list">
                                @foreach ($doc['pdf_files'] as $pdf)
                                    <div class="file-item">
                                        <table style="width:100%;">
                                            <tr>
                                                <td style="width:36px;">
                                                    <span class="file-ext-badge file-ext-pdf">PDF</span>
                                                </td>
                                                <td class="file-name">{{ $pdf['name'] }}</td>
                                                <td class="file-size">{{ $pdf['size_formatted'] ?? '' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @endforeach
                                @foreach ($doc['other_files'] as $file)
                                    @if (!in_array(strtolower($file['ext'] ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <div class="file-item">
                                            <table style="width:100%;">
                                                <tr>
                                                    <td style="width:36px;">
                                                        <span
                                                            class="file-ext-badge file-ext-other">{{ strtoupper(substr($file['ext'] ?? 'FILE', 0, 4)) }}</span>
                                                    </td>
                                                    <td class="file-name">{{ $file['name'] }}</td>
                                                    <td class="file-size">{{ $file['size_formatted'] ?? '' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    @endforeach

    {{-- ===== DAFTAR LAMPIRAN ===== --}}
    @if (!empty($attachedPdfs))
        <div class="page-break"></div>

        <div class="lampiran-cover">
            <div class="lampiran-cover-line"></div>
            <div class="lampiran-cover-body">
                <div class="lampiran-cover-title">LAMPIRAN PDF</div>
                <div class="lampiran-cover-sub">Dokumen PDF yang dilampirkan pada laporan ini</div>
            </div>
            <div class="lampiran-cover-line"></div>
        </div>

        <div class="lampiran-list-area">
            <div class="lampiran-list-heading">Daftar Lampiran:</div>

            @foreach ($attachedPdfs as $i => $lamp)
                <table class="lampiran-row">
                    <tr>
                        <td class="lamp-no-col">{{ $i + 1 }}.</td>
                        <td class="lamp-name-col">
                            {{ $lamp['name'] }}
                            @if (!empty($lamp['url']))
                                <div class="lamp-url">{{ $lamp['url'] }}</div>
                            @elseif (!empty($lamp['path']))
                                <div class="lamp-url">{{ $lamp['path'] }}</div>
                            @endif
                        </td>
                        <td class="lamp-type-col">
                            {{ $lamp['type'] === 'client_document' ? 'Client' : 'Progress' }}
                        </td>
                        <td class="lamp-ticket-col">[{{ $lamp['ticket_code'] }}]</td>
                    </tr>
                </table>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="text-align:left; border:none; padding:0; color:#9ca3af; font-size:9.5px;">
                    Laporan Progress Tiket: {{ $summary['ticket_code'] ?? '-' }}
                </td>
                <td style="text-align:right; border:none; padding:0; color:#9ca3af; font-size:9.5px;">
                    Halaman <span class="pagenum"></span>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman " . $pdf->get_page_number() . " dari " . $pdf->get_page_count();
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 8;
            $color = [0.6, 0.6, 0.6];
            $width = $pdf->get_width();
            $height = $pdf->get_height();
            $textWidth = $fontMetrics->get_text_width($text, $font, $size);
            $pdf->text($width - $textWidth - 28, $height - 22, $text, $font, $size, $color);
        }
    </script>

</body>

</html>
