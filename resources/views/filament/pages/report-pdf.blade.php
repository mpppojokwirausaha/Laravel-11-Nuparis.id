<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Progress</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
            padding: 20px;
        }

        /* Summary Cards */
        .summary-cards {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-icon.green {
            background: #dcfce7;
            color: #166534;
        }

        .card-icon.purple {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .card-icon.orange {
            background: #ffedd5;
            color: #9a3412;
        }

        .card-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        /* Info Grid */
        .info-grid {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-grid tr:last-child td {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 25%;
        }

        /* Ticket Card */
        .ticket-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 16px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .ticket-header {
            padding: 14px 18px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ticket-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ticket-code {
            background: #e0e7ff;
            color: #3730a3;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 8px;
            font-family: monospace;
        }

        .ticket-title {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 999px;
        }

        .status-open {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-in_progress {
            background: #fef3c7;
            color: #b45309;
        }

        .status-closed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-default {
            background: #f3f4f6;
            color: #4b5563;
        }

        .ticket-meta {
            font-size: 10px;
            color: #6b7280;
            display: flex;
            gap: 12px;
        }

        /* ============================================================
           TIMELINE — dot di KANAN, garis vertikal kanan
           Mirror persis dari PHP FPDI: timelineX di kanan, card di kiri
           ============================================================ */
        .tp-wrapper {
            padding: 16px 20px;
        }

        /* Section label separator (Dokumen Client / Riwayat Progress) */
        .tp-section-label {
            font-size: 11px;
            font-weight: 700;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .tp-section-label.client {
            color: #7c3aed;
            border-bottom: 1px solid #ede9fe;
        }

        .tp-section-label.progress {
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
        }

        .tp-section-label.has-top {
            margin-top: 20px;
        }

        /* Timeline container: padding kanan buat ruang dot (32px) + gap (12px) */
        .tp-timeline {
            position: relative;
            padding-right: 44px;
        }

        /* Garis vertikal di KANAN — center dot 32px → right: 15px (margin 6px + setengah dot 16px) */
        .tp-timeline-line {
            position: absolute;
            right: 15px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: #e5e7eb;
        }

        /* Satu baris entry */
        .tp-entry {
            position: relative;
            margin-bottom: 20px;
        }

        /* Dot bulat di KANAN — diposisikan absolut keluar padding */
        .tp-dot {
            position: absolute;
            right: -44px;
            top: 8px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        /* Dot entry terbaru → border gelap, sama dengan PDF "filled circle dark" */
        .tp-dot.is-latest {
            border-color: #374151;
        }

        .tp-dot svg {
            width: 10px;
            height: 10px;
            color: #d1d5db;
        }

        .tp-dot.is-latest svg {
            color: #374151;
        }

        /* Card konten */
        .tp-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
        }

        /* Entry terbaru → border kanan tebal gelap (sama dengan PDF border-right) */
        .tp-card.is-latest {
            border-right: 3px solid #374151;
        }

        /* Baris meta: badge (kiri) + timestamp (kanan) */
        .tp-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        /* Badge "Sebelumnya" — abu-abu filled, sama dengan PDF Rect gray */
        .tp-badge {
            font-size: 10px;
            font-weight: 500;
            background: #f3f4f6;
            color: #6b7280;
            padding: 3px 12px;
            border-radius: 999px;
        }

        /* Badge "Terbaru" — outline gelap, sama dengan PDF Rect outline */
        .tp-badge.is-latest {
            background: transparent;
            border: 1px solid #374151;
            color: #1f2937;
            font-weight: 600;
        }

        /* Badge "Dokumen Client" — biru */
        .tp-badge.client-doc {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-weight: 600;
        }

        .tp-time {
            font-size: 10px;
            color: #9ca3af;
        }

        /* Body teks progress */
        .tp-body {
            font-size: 11px;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .tp-body p {
            margin: 4px 0;
        }

        .tp-body ul,
        .tp-body ol {
            padding-left: 16px;
            margin: 4px 0;
        }

        .tp-body li {
            margin: 2px 0;
        }

        /* Thumbnail gambar */
        .tp-thumbs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 8px 0;
        }

        .tp-thumb {
            width: 70px;
            height: 56px;
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

        /* Daftar file */
        .tp-files {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* File row — abu default (other_files) */
        .tp-file-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
        }

        /* File row PDF — merah muda */
        .tp-file-row.pdf {
            background: #fef2f2;
            border-color: #fecaca;
        }

        /* File row dokumen client — ungu muda */
        .tp-file-row.client {
            background: #f5f3ff;
            border-color: #ddd6fe;
        }

        .tp-file-ext {
            font-size: 8px;
            font-weight: 700;
            padding: 2px 5px;
            background: #e5e7eb;
            color: #374151;
            border-radius: 4px;
            text-transform: uppercase;
            flex-shrink: 0;
            min-width: 28px;
            text-align: center;
        }

        .tp-file-row.pdf .tp-file-ext {
            background: #fecaca;
            color: #b91c1c;
        }

        .tp-file-row.client .tp-file-ext {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .tp-file-name {
            font-size: 10px;
            color: #374151;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tp-file-tag {
            font-size: 9px;
            font-style: italic;
            color: #7c3aed;
            flex-shrink: 0;
        }

        .tp-file-size {
            font-size: 9px;
            color: #9ca3af;
            flex-shrink: 0;
        }

        /* Notes */
        .notes-section {
            margin-bottom: 24px;
        }

        .notes-heading {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .notes-content {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            font-size: 11px;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    {{-- SUMMARY CARDS --}}
    <div class="summary-cards">
        <div class="card">
            <div class="card-icon green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="card-label">Periode</div>
                <div class="card-value">{{ $summary['date_range'] ?? '-' }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-icon purple">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <div class="card-label">Dibuat oleh</div>
                <div class="card-value">{{ $summary['generated_by'] ?? '-' }}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-icon orange">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="card-label">Digenerate</div>
                <div class="card-value">{{ $summary['generated_at'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- INFO TABLE --}}
    <div class="info-grid">
        <table>
            <tr>
                <td class="info-label">Nama Client</td>
                <td>{{ $summary['client_name'] ?? '-' }}</td>
                <td class="info-label">Proposal ID</td>
                <td>{{ $summary['proposal_id'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Proposal For</td>
                <td>{{ $summary['proposal_for'] ?? '-' }}</td>
                <td class="info-label">Enquiry</td>
                <td>{{ $summary['enquiry'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode</td>
                <td>{{ $summary['date_range'] ?? '-' }}</td>
                <td class="info-label">Dibuat Oleh</td>
                <td>{{ $summary['generated_by'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Total Tiket</td>
                <td colspan="3">{{ $summary['total_tickets'] ?? 0 }} tiket</td>
            </tr>
        </table>
    </div>

    {{-- NOTES --}}
    @if (!empty($notes))
        <div class="notes-section">
            <div class="notes-heading">Summary / Catatan</div>
            <div class="notes-content">{!! $notes !!}</div>
        </div>
    @endif

    {{-- TICKET LIST --}}
    @foreach ($data as $ticket)
        @php
            $statusClass = match ($ticket['status'] ?? '') {
                'open' => 'status-open',
                'in_progress' => 'status-in_progress',
                'closed' => 'status-closed',
                'cancelled' => 'status-cancelled',
                default => 'status-default',
            };
            $statusLabel = match ($ticket['status'] ?? '') {
                'open' => 'Open',
                'in_progress' => 'In Progress',
                'closed' => 'Closed',
                'cancelled' => 'Cancelled',
                default => $ticket['status'] ?? 'N/A',
            };

            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            $clientDocs = array_values(
                array_filter($ticket['documents'] ?? [], fn($d) => $d['is_client_document'] ?? false),
            );
            $progressDocs = array_values(
                array_filter($ticket['documents'] ?? [], fn($d) => !($d['is_client_document'] ?? false)),
            );
        @endphp

        <div class="ticket-card">

            {{-- HEADER --}}
            <div class="ticket-header">
                <div class="ticket-left">
                    <span class="ticket-code">{{ $ticket['ticket_code'] ?? '-' }}</span>
                    <span class="ticket-title">{{ $ticket['ticket_title'] ?? '-' }}</span>
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <div class="ticket-meta">
                    <span>Dibuat: {{ $ticket['created_at'] ?? '-' }}</span>
                    <span>•</span>
                    <span>{{ $ticket['documents_count'] ?? 0 }} progress</span>
                </div>
            </div>

            <div class="tp-wrapper">

                {{-- ── DOKUMEN PENDUKUNG CLIENT ── --}}
                @if (!empty($clientDocs))
                    <div class="tp-section-label client">Dokumen Pendukung Client</div>

                    <div class="tp-timeline">
                        <div class="tp-timeline-line"></div>

                        @foreach ($clientDocs as $idx => $doc)
                            @php
                                $isLatest = $idx === count($clientDocs) - 1;
                                $clientThumbs = array_values(
                                    array_filter(
                                        $doc['other_files'] ?? [],
                                        fn($f) => in_array(strtolower($f['ext'] ?? ''), $imageExt),
                                    ),
                                );
                                $clientOthers = array_values(
                                    array_filter(
                                        $doc['other_files'] ?? [],
                                        fn($f) => !in_array(strtolower($f['ext'] ?? ''), $imageExt),
                                    ),
                                );
                            @endphp

                            <div class="tp-entry">
                                <div class="tp-dot {{ $isLatest ? 'is-latest' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="6" />
                                    </svg>
                                </div>

                                <div class="tp-card {{ $isLatest ? 'is-latest' : '' }}">
                                    <div class="tp-meta">
                                        <span class="tp-badge client-doc">Dokumen Client</span>
                                        <span class="tp-time">
                                            {{ \Carbon\Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}
                                        </span>
                                    </div>

                                    @if (!empty($doc['text']))
                                        <div class="tp-body">{!! $doc['text'] !!}</div>
                                    @endif

                                    @if (!empty($clientThumbs))
                                        <div class="tp-thumbs">
                                            @foreach (array_slice($clientThumbs, 0, 4) as $tf)
                                                <div class="tp-thumb">
                                                    <img src="{{ $tf['url'] }}" alt="{{ $tf['name'] }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if (!empty($clientOthers))
                                        <div class="tp-files">
                                            @foreach (array_slice($clientOthers, 0, 5) as $lf)
                                                @php $isPdf = strtolower($lf['ext'] ?? '') === 'pdf'; @endphp
                                                <div class="tp-file-row {{ $isPdf ? 'pdf' : 'client' }}">
                                                    <span
                                                        class="tp-file-ext">{{ strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3)) }}</span>
                                                    <span class="tp-file-name">{{ $lf['name'] ?? '-' }}</span>
                                                    <span class="tp-file-tag">Client Doc</span>
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
                @endif

                {{-- ── RIWAYAT PROGRESS ── --}}
                @if (!empty($progressDocs))
                    <div class="tp-section-label progress {{ !empty($clientDocs) ? 'has-top' : '' }}">
                        Riwayat Progress
                    </div>

                    <div class="tp-timeline">
                        <div class="tp-timeline-line"></div>

                        @foreach ($progressDocs as $idx => $doc)
                            @php $isLatest = $idx === count($progressDocs) - 1; @endphp

                            <div class="tp-entry">
                                <div class="tp-dot {{ $isLatest ? 'is-latest' : '' }}">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="6" />
                                    </svg>
                                </div>

                                <div class="tp-card {{ $isLatest ? 'is-latest' : '' }}">
                                    <div class="tp-meta">
                                        <span class="tp-badge {{ $isLatest ? 'is-latest' : '' }}">
                                            {{ $isLatest ? 'Terbaru' : 'Sebelumnya' }}
                                        </span>
                                        <span class="tp-time">
                                            {{ \Carbon\Carbon::parse($doc['timestamp'] ?? now())->translatedFormat('d F Y, H:i') }}
                                        </span>
                                    </div>

                                    @if (!empty($doc['text']))
                                        <div class="tp-body">{!! $doc['text'] !!}</div>
                                    @endif

                                    @if (!empty($doc['thumbnail_files']))
                                        <div class="tp-thumbs">
                                            @foreach (array_slice($doc['thumbnail_files'], 0, 4) as $tf)
                                                <div class="tp-thumb">
                                                    <img src="{{ $tf['url'] }}" alt="{{ $tf['name'] }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if (!empty($doc['embedded_images']))
                                        <div class="tp-thumbs">
                                            @foreach (array_slice($doc['embedded_images'], 0, 4) as $img)
                                                <div class="tp-thumb">
                                                    <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if (!empty($doc['pdf_files']))
                                        <div class="tp-files">
                                            @foreach (array_slice($doc['pdf_files'], 0, 5) as $pf)
                                                <div class="tp-file-row pdf">
                                                    <span class="tp-file-ext">PDF</span>
                                                    <span class="tp-file-name">{{ $pf['name'] ?? '-' }}</span>
                                                    @if (!empty($pf['size_formatted']))
                                                        <span class="tp-file-size">{{ $pf['size_formatted'] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if (!empty($doc['other_files']))
                                        <div class="tp-files">
                                            @foreach (array_slice($doc['other_files'], 0, 5) as $lf)
                                                <div class="tp-file-row">
                                                    <span
                                                        class="tp-file-ext">{{ strtoupper(substr($lf['ext'] ?? 'FILE', 0, 3)) }}</span>
                                                    <span class="tp-file-name">{{ $lf['name'] ?? '-' }}</span>
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
                @endif

            </div>{{-- /tp-wrapper --}}
        </div>{{-- /ticket-card --}}
    @endforeach

    <div class="footer">
        <span>Laporan Progress Tiket{{ !empty($summary['client_name']) ? ' - ' . $summary['client_name'] : '' }}</span>
        <span>{{ $summary['generated_at'] ?? '' }}</span>
    </div>

</body>

</html>
