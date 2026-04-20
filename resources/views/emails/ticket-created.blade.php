<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Berhasil Dibuat</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a2e;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f0f2f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden;">
                    {{-- Header --}}
                    <tr>
                        <td align="center" bgcolor="#1e293b" style="padding: 40px 32px; background-color: #1e293b;">
                            <h1
                                style="margin: 0 0 16px; color: #f8fafc; font-size: 28px; font-weight: 700; letter-spacing: 0.5px;">
                                {{ config('app.name') }}
                            </h1>
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td bgcolor="#334155"
                                        style="background-color: #334155; border-radius: 20px; padding: 6px 20px;">
                                        <span style="color: #e2e8f0; font-size: 13px; font-weight: 500;">✓
                                            &nbsp;Tiket Berhasil Dibuat</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Status Banner --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="4" bgcolor="#3b82f6" style="background-color: #3b82f6;">&nbsp;
                                    </td>
                                    <td style="padding: 16px 24px;">
                                        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.6;">
                                            🔔 &nbsp;Tiket Anda sedang <strong style="color: #1e293b;">menunggu
                                                ditinjau</strong> oleh tim konsultan kami.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px 32px;">

                            {{-- Greeting --}}
                            <p style="margin: 0 0 6px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Halo, {{ $ticket->ticket_name_client }}
                            </p>
                            <p style="margin: 0 0 32px; font-size: 14px; color: #64748b; line-height: 1.8;">
                                Terima kasih telah memilih <strong
                                    style="color: #0f172a;">{{ config('app.name') }}</strong> sebagai mitra bisnis
                                terpercaya Anda.
                                Permintaan konsultasi Anda telah kami terima, dan akan segera ditinjau serta
                                ditindaklanjuti oleh tim konsultan kami.
                            </p>

                            {{-- Ticket Code Highlight --}}
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
                                            {{ $ticket->ticket_code }}
                                        </p>
                                        <p style="margin: 6px 0 0; font-size: 11px; color: #94a3b8;">
                                            Simpan kode ini untuk pengecekan status
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Section Title: Ringkasan --}}
                            <p
                                style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                Ringkasan Tiket
                            </p>

                            {{-- Detail Card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">

                                <tr bgcolor="#f8fafc" style="background-color: #f8fafc;">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Judul
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">
                                        {{ $ticket->ticket_title }}
                                    </td>
                                </tr>

                                <tr bgcolor="#ffffff" style="background-color: #ffffff;">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Klien
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $ticket->ticket_name_client }}
                                    </td>
                                </tr>

                                <tr bgcolor="#f8fafc" style="background-color: #f8fafc;">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        WhatsApp
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        +{{ $ticket->ticket_whatsapp }}
                                    </td>
                                </tr>

                                <tr bgcolor="#ffffff" style="background-color: #ffffff;">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Email
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $ticket->ticket_email }}
                                    </td>
                                </tr>

                                <tr bgcolor="#f8fafc" style="background-color: #f8fafc;">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8;">
                                        Tanggal
                                    </td>
                                    <td style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155;">
                                        {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                                    </td>
                                </tr>

                            </table>

                            {{-- Section Title: Deskripsi --}}
                            <p
                                style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                Deskripsi Masalah
                            </p>

                            {{-- Deskripsi Content --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom: 28px;">
                                <tr>
                                    <td bgcolor="#f8fafc"
                                        style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #475569; border-radius: 8px; padding: 20px 24px;">
                                        <p style="margin: 0; font-size: 14px; color: #334155; line-height: 1.8;">
                                            {{ $ticket->ticket_content }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Lampiran --}}
                            @if ($ticket->ticket_document_support)
                                <p
                                    style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                    Lampiran
                                </p>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="margin-bottom: 28px;">
                                    <tr>
                                        <td bgcolor="#f8fafc"
                                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px;">
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td width="36">
                                                        <span style="font-size: 24px;">📎</span>
                                                    </td>
                                                    <td>
                                                        <p
                                                            style="margin: 0 0 2px; font-size: 13px; font-weight: 600; color: #0f172a;">
                                                            File lampiran telah disertakan
                                                        </p>
                                                        <p style="margin: 0; font-size: 12px; color: #64748b;">
                                                            Dokumen pendukung Anda terlampir pada email ini. Silakan
                                                            buka attachment di bawah.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Info Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 13px; color: #0369a1; line-height: 1.8;">
                                        <strong style="color: #0c4a6e;">⏱ Estimasi Respon:</strong>
                                        Tim konsultan kami akan menghubungi Anda melalui WhatsApp atau email dalam
                                        <strong style="color: #0c4a6e;">1×24 jam kerja</strong>.
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            {{-- <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/ticket/' . $ticket->ticket_code) }}"
                                            style="display: inline-block; background-color: #1e293b; color: #f8fafc; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 14px; font-weight: 600; letter-spacing: 0.5px;">
                                            Lacak Status Tiket →
                                        </a>
                                    </td>
                                </tr>
                            </table> --}}

                            {{-- Divider --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom: 20px;">
                                <tr>
                                    <td style="border-top: 1px solid #e2e8f0; font-size: 0; line-height: 0;">&nbsp;
                                    </td>
                                </tr>
                            </table>

                            {{-- Contact --}}
                            <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.8;">
                                Jikaa ada pertanyaan, jangan ragu untuk menghubungi kami via WhatsApp:
                                <a href="https://wa.me/{{ env('NO_WHATSAPP') }}"
                                    style="color: #0f172a; text-decoration: none; font-weight: 600;">
                                    {{ env('NO_WHATSAPP') }}
                                </a>
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" bgcolor="#f8fafc"
                            style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #475569;">
                                {{ config('app.name') }}
                            </p>
                            <p style="margin: 0 0 8px; font-size: 12px; color: #94a3b8; line-height: 1.8;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas langsung.<br>
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                            <a href="{{ config('app.url') }}"
                                style="font-size: 12px; color: #475569; text-decoration: none; font-weight: 600;">
                                🌐 {{ config('app.url') }}
                            </a>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
