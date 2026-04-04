<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Berhasil - {{ $order->order_product_name }}</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a2e;">

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
                                            &nbsp;Pembelian Berhasil</span>
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
                                            📄 &nbsp;Pembelian letter Anda telah <strong
                                                style="color: #1e293b;">berhasil dikonfirmasi</strong>. File letter
                                            dapat
                                            diunduh di bawah.
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
                                Halo, {{ $order->order_customer_name ?? 'Customer' }}
                            </p>
                            <p style="margin: 0 0 32px; font-size: 14px; color: #64748b; line-height: 1.8;">
                                Terima kasih telah membeli <strong
                                    style="color: #0f172a;">{{ $order->order_product_name }}</strong>.
                                Pembayaran Anda telah kami terima dan berikut adalah detail pesanan Anda.
                            </p>

                            <!-- Order ID Highlight -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center" bgcolor="#f8fafc"
                                        style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px;">
                                        <p
                                            style="margin: 0 0 4px; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #94a3b8;">
                                            Order ID Anda
                                        </p>
                                        <p
                                            style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; font-family: monospace; letter-spacing: 1px;">
                                            {{ $order->order_id }}
                                        </p>
                                        <p style="margin: 6px 0 0; font-size: 11px; color: #94a3b8;">
                                            Simpan ID ini untuk referensi Anda
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Section Title: Ringkasan Pesanan -->
                            <p
                                style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                Ringkasan Pesanan
                            </p>

                            <!-- Order Detail Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Letter
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 600; color: #0f172a; border-bottom: 1px solid #e2e8f0;">
                                        {{ $order->order_product_name }}
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Nama Pemesan
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $order->order_customer_name ?? 'Customer' }}
                                    </td>
                                </tr>
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Email
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155; border-bottom: 1px solid #e2e8f0;">
                                        {{ $order->order_customer_email ?? '-' }}
                                    </td>
                                </tr>
                                <tr bgcolor="#ffffff">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 1px solid #e2e8f0;">
                                        Total Bayar
                                    </td>
                                    <td
                                        style="padding: 13px 20px; font-size: 16px; font-weight: 700; color: #10b981; border-bottom: 1px solid #e2e8f0;">
                                        Rp {{ number_format($order->order_gross_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr bgcolor="#f8fafc">
                                    <td width="140"
                                        style="padding: 13px 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8;">
                                        Tanggal Bayar
                                    </td>
                                    <td style="padding: 13px 20px; font-size: 14px; font-weight: 500; color: #334155;">
                                        {{ $order->order_paid_at ? \Carbon\Carbon::parse($order->order_paid_at)->isoFormat('dddd, D MMMM YYYY HH:mm') : '-' }}
                                        WIB
                                    </td>
                                </tr>
                            </table>

                            <!-- Download Section -->
                            @php
                                $letter = \App\Models\Letter::where('letter_slug', $order->order_product_slug)->first();
                                $downloadUrl =
                                    $letter && $letter->letter_file_path
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->url(
                                            $letter->letter_file_path,
                                        )
                                        : null;
                            @endphp

                            @if ($downloadUrl)
                                <p
                                    style="margin: 0 0 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8;">
                                    Download Letter
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 28px;">
                                    <tr>
                                        <td align="center" style="padding: 32px 24px;">
                                            <p
                                                style="margin: 0 0 20px; font-size: 14px; color: #64748b; line-height: 1.6;">
                                                Klik tombol di bawah untuk mengunduh file letter Anda
                                            </p>
                                            <a href="{{ $downloadUrl }}" target="_blank"
                                                style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-size: 15px; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                                📥 Download Letter
                                            </a>
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
                                            <li>Simpan email ini sebagai bukti pembelian Anda</li>
                                            <li>File letter dapat diunduh melalui tombol di atas</li>
                                            <li>Jika mengalami kendala download, silakan hubungi kami</li>
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
                                Jika ada pertanyaan, jangan ragu untuk menghubungi kami via WhatsApp:
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
