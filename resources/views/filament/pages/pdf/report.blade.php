<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Progress Tiket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
            padding: 20px;
        }

        /* HEADER TABLE */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }

        .info-table td {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
        }

        .bg-gray {
            background-color: #f3f4f6;
        }

        /* TYPOGRAPHY */
        h3 {
            font-size: 14px;
            margin-bottom: 12px;
            margin-top: 16px;
        }

        h4 {
            font-size: 13px;
            margin-bottom: 8px;
            margin-top: 12px;
        }

        /* TIMELINE WRAPPER */
        .tp-wrapper {
            margin-bottom: 24px;
        }

        .tp-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .tp-header-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
        }

        .tp-header-count {
            font-size: 11px;
            color: #6b7280;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 2px 10px;
        }

        /* TIMELINE */
        .tp-timeline {
            position: relative;
            padding-right: 52px;
            max-width: 90%;
            margin-left: auto;
            margin-right: 0;
        }

        .tp-timeline-line {
            position: absolute;
            right: 17px;
            top: 8px;
            bottom: 8px;
            width: 1px;
            background: #e5e7eb;
        }

        .tp-entry {
            position: relative;
            margin-bottom: 20px;
        }

        .tp-dot {
            position: absolute;
            right: -35px;
            top: 8px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tp-dot.is-latest {
            background: transparent;
            border-color: #374151;
            color: #374151;
        }

        .tp-dot svg {
            width: 10px;
            height: 10px;
        }

        .tp-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 18px;
        }

        .tp-card.is-latest {
            border-right: 3px solid #374151;
        }

        .tp-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .tp-badge {
            font-size: 10px;
            font-weight: 500;
            border-radius: 999px;
            padding: 2px 10px;
            background: #f3f4f6;
            color: #6b7280;
        }

        .tp-badge.is-latest {
            background: transparent;
            color: #1f2937;
            border: 1px solid #374151;
        }

        .tp-badge.client {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .tp-time {
            font-size: 10px;
            color: #9ca3af;
        }

        .tp-body {
            font-size: 12px;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .tp-body p {
            margin-bottom: 6px;
        }

        .tp-body ul,
        .tp-body ol {
            margin-left: 20px;
            margin-bottom: 6px;
        }

        .tp-body li {
            margin-bottom: 2px;
        }

        /* THUMBNAILS */
        .tp-thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .tp-thumb {
            width: 80px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .tp-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* FILES */
        .tp-files {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 10px;
        }

        .tp-file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 10px;
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
        }

        .tp-file-row.client {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .tp-file-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tp-file-icon {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 600;
            background: rgba(55, 65, 81, 0.1);
            color: #374151;
        }

        .tp-file-icon.client {
            background: #dbeafe;
            color: #1e40af;
        }

        .tp-file-icon.pdf {
            background: #fecaca;
            color: #991b1b;
        }

        .tp-file-name {
            font-size: 11px;
            color: #374151;
            text-decoration: none;
            word-break: break-all;
        }

        .tp-file-name.client {
            color: #1e40af;
        }

        .tp-file-size {
            font-size: 10px;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* DIVIDER */
        .divider {
            border-bottom: 1px solid #e5e7eb;
            margin: 8px 0;
        }

        /* NOTES */
        .notes-box {
            border: 1px solid #e5e7eb;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #fafafa;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            padding: 10px;
            border-top: 1px solid #e5e7eb;
        }

        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- HEADER TABLE WITH LOGO --}}
    <table class="header-table">
        <tr>
            <td style="width: 80px;">
                @if (file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" style="width: 50px;">
                @else
                    <div style="font-weight: bold; color: #9ca3af;">[LOGO]</div>
                @endif
            </td>
            <td>
                <table class="info-table">
                    <tr>
                        <td style="width: 25%; background: #f3f4f6; font-weight: 600;">ID Laporan</td>
                        <td style="width: 25%;">{{ $summary['proposal_id'] ?? '-' }}</td>
                        <td style="width: 25%; background: #f3f4f6; font-weight: 600;">Klien</td>
                        <td style="width: 25%;">{{ $summary['client_name'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="background: #f3f4f6; font-weight: 600;">Pekerjaan</td>
                        <td colspan="3">{{ $summary['proposal_for'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="background: #f3f4f6; font-weight: 600;">Dilaporkan oleh</td>
                        <td>{{ $summary['generated_by'] ?? '-' }}</td>
                        <td style="background: #f3f4f6; font-weight: 600;">Periode Laporan</td>
                        <td>{{ $summary['date_range'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- NOTES / SUMMARY --}}
    @if (!empty($notes))
        <div class="notes-box">
            <strong>Summary / Catatan</strong>
            <div style="margin-top: 8px;">{!! nl2br(e($notes)) !!}</div>
        </div>
    @endif

    {{-- DAFTAR TIKET & PROGRESS --}}
    <h3>Daftar Tiket & Progress</h3>

    @foreach ($data as $ticket)
        @php
            $clientDocuments = [];
            $progressDocuments = [];
            foreach ($ticket['documents'] ?? [] as $doc) {
                if (isset($doc['is_client_document']) && $doc['is_client_document'] === true) {
                    $clientDocuments[] = $doc;
                } else {
                    $progressDocuments[] = $doc;
                }
            }
        @endphp

        {{-- ==================== CLIENT DOCUMENTS ==================== --}}
        @if (!empty($clientDocuments))
            <div class="tp-wrapper">
                <div class="tp-header">
                    <span class="tp-header-title">📎 Dokumen Pendukung Client</span>
                    <span class="tp-header-count">{{ count($clientDocuments) }} dokumen</span>
                </div>
                <div class="tp-timeline">
                    <div class="tp-timeline-line"></div>

                    @foreach ($clientDocuments as $clientIndex => $clientDoc)
                        @php
                            $isLatestClient = $clientIndex === count($clientDocuments) - 1;
                            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
                            $thumbnailFiles = [];
                            $otherFiles = [];
                            foreach ($clientDoc['other_files'] ?? [] as $cf) {
                                $ext = strtolower($cf['ext'] ?? '');
                                if (in_array($ext, $imageExt)) {
                                    $thumbnailFiles[] = $cf;
                                } else {
                                    $otherFiles[] = $cf;
                                }
                            }
                        @endphp
                        <div class="tp-entry">
                            <div class="tp-dot {{ $isLatestClient ? 'is-latest' : '' }}">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="6" />
                                </svg>
                            </div>
                            <div class="tp-card">
                                <div class="tp-meta">
                                    <span class="tp-badge client">Dokumen Client</span>
                                    <span
                                        class="tp-time">{{ \Carbon\Carbon::parse($clientDoc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}</span>
                                </div>

                                {{-- Thumbnails --}}
                                @if (!empty($thumbnailFiles))
                                    <div class="tp-thumbs">
                                        @foreach ($thumbnailFiles as $thumb)
                                            <div class="tp-thumb">
                                                <img src="{{ $thumb['url'] }}" alt="thumbnail">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Other files --}}
                                @if (!empty($otherFiles))
                                    <div class="tp-files">
                                        @foreach ($otherFiles as $cf)
                                            <div class="tp-file-row client">
                                                <div class="tp-file-left">
                                                    <div class="tp-file-icon client">
                                                        {{ strtoupper(substr($cf['ext'], 0, 3)) ?: 'DOC' }}</div>
                                                    <span class="tp-file-name client">{{ $cf['name'] }}</span>
                                                </div>
                                                @if (!empty($cf['size_formatted']))
                                                    <span class="tp-file-size">{{ $cf['size_formatted'] }}</span>
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

        {{-- ==================== PROGRESS DOCUMENTS ==================== --}}
        @if (!empty($progressDocuments))
            <div class="tp-wrapper">
                <div class="tp-header">
                    <span class="tp-header-title">Riwayat Progress</span>
                    <span class="tp-header-count">{{ count($progressDocuments) }} entri</span>
                </div>
                <div class="tp-timeline">
                    <div class="tp-timeline-line"></div>

                    @foreach ($progressDocuments as $index => $doc)
                        @php
                            $isLatest = $index === count($progressDocuments) - 1;
                            $htmlContent = $doc['text'] ?? '';
                            $thumbnailFiles = $doc['thumbnail_files'] ?? [];
                            $embeddedImages = $doc['embedded_images'] ?? [];
                            $otherFiles = $doc['other_files'] ?? [];
                            $pdfFiles = $doc['pdf_files'] ?? [];
                        @endphp
                        <div class="tp-entry">
                            <div class="tp-dot {{ $isLatest ? 'is-latest' : '' }}">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="6" />
                                </svg>
                            </div>
                            <div class="tp-card {{ $isLatest ? 'is-latest' : '' }}">
                                <div class="tp-meta">
                                    <span
                                        class="tp-badge {{ $isLatest ? 'is-latest' : '' }}">{{ $isLatest ? 'Terbaru' : 'Sebelumnya' }}</span>
                                    <span
                                        class="tp-time">{{ \Carbon\Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}</span>
                                </div>

                                {{-- HTML content --}}
                                @if (!empty($htmlContent))
                                    <div class="tp-body">
                                        {!! $htmlContent !!}
                                    </div>
                                @endif

                                {{-- Thumbnail files --}}
                                @if (!empty($thumbnailFiles))
                                    <div class="tp-thumbs">
                                        @foreach ($thumbnailFiles as $thumbUrl)
                                            <div class="tp-thumb">
                                                <img src="{{ is_array($thumbUrl) ? $thumbUrl['url'] ?? '#' : $thumbUrl }}"
                                                    alt="thumbnail">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Embedded images --}}
                                @if (!empty($embeddedImages))
                                    <div class="tp-thumbs">
                                        @foreach ($embeddedImages as $imgUrl)
                                            <div class="tp-thumb">
                                                <img src="{{ is_array($imgUrl) ? $imgUrl['url'] ?? '#' : $imgUrl }}"
                                                    alt="image">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- PDF files --}}
                                @if (!empty($pdfFiles))
                                    <div class="tp-files">
                                        @foreach ($pdfFiles as $pf)
                                            <div class="tp-file-row">
                                                <div class="tp-file-left">
                                                    <div class="tp-file-icon pdf">PDF</div>
                                                    <span
                                                        class="tp-file-name">{{ $pf['name'] ?? 'Document.pdf' }}</span>
                                                </div>
                                                @if (!empty($pf['size_formatted']))
                                                    <span class="tp-file-size">{{ $pf['size_formatted'] }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Other files --}}
                                @if (!empty($otherFiles))
                                    <div class="tp-files">
                                        @foreach ($otherFiles as $lf)
                                            <div class="tp-file-row">
                                                <div class="tp-file-left">
                                                    <div class="tp-file-icon">
                                                        {{ strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3)) ?: 'FIL' }}
                                                    </div>
                                                    <span class="tp-file-name">{{ $lf['name'] ?? 'File' }}</span>
                                                </div>
                                                @if (!empty($lf['size_formatted']))
                                                    <span class="tp-file-size">{{ $lf['size_formatted'] }}</span>
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

        {{-- Separator antar tiket --}}
        <div class="divider"></div>
    @endforeach

    <div class="footer">
        Laporan Progress Tiket {{ !empty($summary['client_name']) ? ' - ' . $summary['client_name'] : '' }}
    </div>

</body>

</html>
