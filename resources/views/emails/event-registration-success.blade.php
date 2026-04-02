<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - {{ $event->event_title }}</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a2e;">

    @php
        // Handle event_type as array or string - MUST BE BEFORE ANY USAGE
        $eventTypes = is_array($event->event_type) ? $event->event_type : [$event->event_type ?? ''];
        $isOnline = in_array('Online', $eventTypes);
        $isOffline = in_array('Offline', $eventTypes);
        $isHybrid = $isOnline && $isOffline;

        // Default to online if no type specified
        if (!$isOnline && !$isOffline) {
            $isOnline = true;
        }

        // Group link
        $groupWhatsapp = $event->event_link ?? null;
        $hasGroup = !empty($groupWhatsapp);
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f0f2f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden;">

                    <!-- Header -->
                    <tr bgcolor="#1e293b">
                        <td align="center" style="padding: 40px 32px;">
                            <h1
                                style="margin: 0 0 16px; color: #f8fafc; font-size: 28px; font-weight: 700; letter-spacing: 0.5px;">
                                {{ config('app.name') }}
                            </h1>
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td bgcolor="#334155"
                                        style="background-color: #334155; border-radius: 20px; padding: 6px 20px;">
                                        <span style="color: #e2e8f0; font-size: 13px; font-weight: 500;">✓
                                            &nbsp;Pendaftaran Berhasil</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Status Banner -->
                    <tr bgcolor="#f8fafc">
                        <td style="padding: 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="4" bgcolor="#3b82f6" style="background-color: #3b82f6;">&nbsp;</td>
                                    <td style="padding: 16px 24px;">
                                        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
                                            @if ($isOnline && !$isOffline)
                                                💻 &nbsp;Pendaftaran Anda telah <strong style="color: #1e293b;">berhasil
                                                    dikonfirmasi</strong>. Link akses event akan segera dikirimkan.
                                            @elseif ($isOffline && !$isOnline)
                                                🎟️ &nbsp;Pendaftaran Anda telah <strong
                                                    style="color: #1e293b;">berhasil dikonfirmasi</strong>. Simpan tiket
                                                Anda untuk registrasi ulang di lokasi.
                                            @else
                                                💻🎟️ &nbsp;Pendaftaran Anda telah <strong
                                                    style="color: #1e293b;">berhasil dikonfirmasi</strong>. Event ini
                                                hybrid
                                                (online & offline).
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 32px;">

                            <!-- Greeting -->
                            <p style="margin: 0 0 6px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Halo, {{ $participant->participant_name }}
                            </p>
                            <p style="margin: 0 0 32px; font-size: 14px; color: #64748b; line-height: 1.8;">
                                Terima kasih telah mendaftar pada event <strong
                                    style="color: #0f172a;">{{ $event->event_title }}</strong>.
                                Pendaftaran Anda telah kami terima dan berikut adalah detail tiket elektronik Anda.
                            </p>

                            <!-- Ticket Code Highlight -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center" bgcolor="#f8fafc"
                                        style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px;">
                                        <p
                                            style="margin: 0 0 4px; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8;">
                                            Kode Tiket Anda
                                        </p>
                                        <p
                                            style="margin: 0; font-size: 22px; font-weight: 700; color: #0f172a; font-family: monospace; letter-spacing: 1px;">
                                            {{ $participant->ticket_code }}
                                        </p>
                                        <p style="margin: 6px 0 0; font-size: 11px; color: #94a3b8;">
                                            @if ($isOnline && !$isOffline)
                                                Gunakan kode ini untuk verifikasi saat mengakses event
                                            @elseif ($isOffline && !$isOnline)
                                                Simpan kode ini untuk registrasi ulang di lokasi
                                            @else
                                                Simpan kode ini untuk registrasi. Link akses akan dikirim terpisah.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Section Title: Ringkasan Event -->
                            <p
                                style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                Ringkasan Event
                            </p>

                            @php
                                $startDate = \Carbon\Carbon::parse($event->event_date_start);
                                $endDate = $event->event_date_end
                                    ? \Carbon\Carbon::parse($event->event_date_end)
                                    : $startDate->copy()->addHours(2);
                                $icsContent =
                                    "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//{{ config('app.name') . ID }}//EN\r\nBEGIN:VEVENT\r\nUID:" .
                                    uniqid() .
                                    "@{{ config('app.url') }}\r\nDTSTAMP:" .
                                    now()->format('Ymd\THis\Z') .
                                    "\r\nDTSTART:" .
                                    $startDate->format('Ymd\THis\Z') .
                                    "\r\nDTEND:" .
                                    $endDate->format('Ymd\THis\Z') .
                                    "\r\nSUMMARY:" .
                                    addslashes($event->event_title) .
                                    "\r\nLOCATION:" .
                                    addslashes(
                                        $event->event_type == 'online' ? 'Online Event' : $event->event_location,
                                    ) .
                                    "\r\nDESCRIPTION:" .
                                    addslashes(
                                        'Peserta: ' .
                                            $participant->participant_name .
                                            "\nKode Tiket: " .
                                            $participant->ticket_code,
                                    ) .
                                    "\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";
                                $icsBase64 = base64_encode($icsContent);
                                $googleCalendarUrl =
                                    'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' .
                                    urlencode($event->event_title) .
                                    '&dates=' .
                                    $startDate->format('Ymd\THis\Z') .
                                    '/' .
                                    $endDate->format('Ymd\THis\Z') .
                                    '&location=' .
                                    urlencode(
                                        $event->event_type == 'online' ? 'Online Event' : $event->event_location,
                                    ) .
                                    '&details=' .
                                    urlencode(
                                        'Peserta: ' .
                                            $participant->participant_name .
                                            "\nKode Tiket: " .
                                            $participant->ticket_code,
                                    );
                            @endphp

                            <!-- Detail Card Event -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Event
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">
                                        {{ $event->event_title }}
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Peserta
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $participant->participant_name }}
                                    </td>
                                </tr>
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Tanggal & Waktu
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ \Carbon\Carbon::parse($event->event_date_start)->isoFormat('dddd, D MMMM YYYY') }}<br>
                                        <span style="color: #64748b;">
                                            {{ \Carbon\Carbon::parse($event->event_date_start)->isoFormat('HH:mm') }}
                                            WIB
                                            @if ($event->event_date_end)
                                                -
                                                {{ \Carbon\Carbon::parse($event->event_date_end)->isoFormat('HH:mm') }}
                                                WIB
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @if ($isOnline && !$isOffline)
                                    <tr bgcolor="#ffffff">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                            Platform
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                            {{ $event->event_platform ?? 'Zoom Meeting' }}
                                        </td>
                                    </tr>
                                    <tr bgcolor="#f8fafc">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                            Link Akses
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                            @if (!empty($event->event_meeting_link))
                                                <a href="{{ $event->event_meeting_link }}" target="_blank"
                                                    style="color: #3b82f6; text-decoration: none; font-weight: 600;">{{ $event->event_meeting_link }}</a>
                                            @else
                                                <span style="color: #f59e0b;">Akan dikirim di group event</span>
                                            @endif
                                        </td>
                                    </tr>
                                @elseif ($isOffline && !$isOnline)
                                    <tr bgcolor="#ffffff">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                            Lokasi
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                            {{ $event->event_location }}
                                        </td>
                                    </tr>
                                @else
                                    <!-- Hybrid event: show both platform and location -->
                                    <tr bgcolor="#ffffff">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                            Platform Online
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                            {{ $event->event_platform ?? 'Zoom Meeting' }}
                                        </td>
                                    </tr>
                                    <tr bgcolor="#f8fafc">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                            Lokasi Offline
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                            {{ $event->event_location }}
                                        </td>
                                    </tr>
                                @endif
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8;">
                                        Tambah ke Kalender
                                    </td>
                                    <td style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155;">
                                        <a href="{{ $googleCalendarUrl }}" target="_blank"
                                            style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #4285F4 0%, #34A853 100%); color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);">
                                            Tambah ke Google Kalender
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Section Title: Data Peserta -->
                            <p
                                style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                Data Peserta
                            </p>

                            <!-- Participant Detail Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Lengkap
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $participant->participant_name }}
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Email
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $participant->participant_email }}
                                    </td>
                                </tr>
                                @if ($participant->participant_no_wa)
                                    <tr bgcolor="#f8fafc">
                                        <td width="140"
                                            style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8;">
                                            WhatsApp
                                        </td>
                                        <td
                                            style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155;">
                                            {{ $participant->participant_no_wa }}
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <!-- Group Link Section -->
                            @if ($hasGroup)
                                <p
                                    style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                    Komunitas & Informasi
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">
                                    <tr bgcolor="#f8fafc">
                                        <td style="padding: 20px 24px;">
                                            <p
                                                style="margin: 0 0 16px; font-size: 13px; color: #334155; line-height: 1.6;">
                                                Bergabunglah dengan grup komunitas resmi untuk mendapatkan informasi
                                                terkini seputar event, pengumuman penting, serta kesempatan berinteraksi
                                                dengan sesama peserta.
                                            </p>

                                            @if (!empty($groupWhatsapp))
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                                    style="margin-bottom: 20px;">
                                                    <tr>
                                                        <td style="vertical-align: middle;">
                                                            <p
                                                                style="margin: 0 0 4px; font-size: 15px; font-weight: 600; color: #0f172a;">
                                                                Grup WhatsApp Resmi
                                                            </p>
                                                            <p style="margin: 0; font-size: 12px; color: #475569;">
                                                                Dapatkan pengumuman real-time dan diskusi bersama
                                                                peserta
                                                            </p>
                                                        </td>
                                                        <td width="140"
                                                            style="vertical-align: middle; text-align: right;">
                                                            <a href="{{ $groupWhatsapp }}" target="_blank"
                                                                style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                                                Gabung →
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif

                                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                                style="background-color: #fef9e6; border-radius: 8px;">
                                                <tr>

                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 13px; color: #0369a1; line-height: 1.8;">
                                        <strong style="color: #0c4a6e;">📌 Informasi Penting:</strong>
                                        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                                            @if ($isOnline && !$isOffline)
                                                <li>Pastikan perangkat Anda mendukung platform
                                                    {{ $event->event_platform ?? 'Zoom' }}</li>
                                                <li>Gunakan kode tiket untuk verifikasi saat memasuki ruang meeting</li>
                                                <li>Gunakan opsi "Tambah ke Kalender" di atas untuk menyimpan jadwal
                                                    event</li>
                                            @elseif ($isOffline && !$isOnline)
                                                <li>Harap tunjukkan kode tiket saat registrasi ulang di lokasi</li>
                                                <li>Datang 30 menit sebelum event dimulai</li>
                                                <li>Simpan email ini sebagai bukti pendaftaran</li>
                                                <li>Gunakan opsi "Tambah ke Kalender" di atas untuk menyimpan jadwal
                                                    event</li>
                                            @else
                                                <li>Event ini hybrid (online & offline)</li>
                                                <li>Pastikan perangkat Anda mendukung platform
                                                    {{ $event->event_platform ?? 'Zoom' }} untuk sesi online</li>
                                                <li>Untuk sesi offline, harap tunjukkan kode tiket saat registrasi ulang
                                                </li>
                                                <li>Gunakan opsi "Tambah ke Kalender" di atas untuk menyimpan jadwal
                                                    event</li>
                                            @endif
                                            @if ($hasGroup)
                                                <li>Bergabunglah dengan grup komunitas melalui link di atas untuk update
                                                    terbaru</li>
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom: 20px;">
                                <tr>
                                    <td style="border-top: 1px solid #e2e8f0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Contact -->
                            <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.8;">
                                Jikaa ada pertanyaan, jangan ragu untuk menghubungi kami via WhatsApp:
                                <a href="https://wa.me/{{ env('NO_WHATSAPP') }}"
                                    style="color: #0f172a; text-decoration: none; font-weight: 600;">
                                    {{ env('NO_WHATSAPP') }}
                                </a>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr bgcolor="#f8fafc">
                        <td align="center" style="padding: 24px 32px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #475569;">
                                {{ config('app.name') }}
                            </p>
                            <p style="margin: 0 0 8px; font-size: 12px; color: #94a3b8; line-height: 1.8;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas langsung.<br>
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <a href="{{ config('app.url', '#') }}"
                                style="font-size: 12px; color: #475569; text-decoration: none; font-weight: 600;">
                                🌐 {{ config('app.url', '#') }}
                            </a>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
