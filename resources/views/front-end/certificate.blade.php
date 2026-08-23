<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

@php
    // BARU: 'nama' gak lagi dijamin ada (field sekarang custom per-template).
    // Judul halaman fallback ke nilai field dinamis pertama yang keisi,
    // baru ke kolom 'nama' lama (data sebelum fitur ini dinamis), baru "Sertifikat".
    $displayTitle =
        $certificateData['nama'] ?? ($certificateData['dynamic_fields'][0]['value'] ?? (null ?? 'Sertifikat'));
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Certificate Verification' }}</title>
    <meta name="title" content="{{ $displayTitle }}" />
    <meta name="description" content="Verifikasi sertifikat dengan aman dan cepat." />
    <meta name="keywords" content="verifikasi sertifikat" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $displayTitle }}" />
    <meta property="og:description" content="Verifikasi sertifikat digital yang aman, cepat, dan terpercaya." />
    <meta property="og:site_name" content="Certificate Verification" />
    <meta property="og:locale" content="id_ID" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .animate-slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .pdf-viewer-container {
            width: 100%;
            height: 500px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            background: #f9fafb;
        }

        .pdf-embed {
            width: 100%;
            height: 100%;
            border: none;
        }

        @media (max-width: 1024px) {
            .pdf-viewer-container {
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            .pdf-viewer-container {
                height: 350px;
            }
        }

        @media (max-width: 640px) {
            .pdf-viewer-container {
                height: 300px;
            }
        }
    </style>
</head>

<body class="text-gray-700 bg-gray-50 min-h-screen">

    @include('front-end.layouts.components.header')

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 lg:pt-24 py-6 lg:py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900">Certificate Verification</h1>
                    <p class="text-gray-600 mt-1">Validate certificate authenticity <span
                            class="text-red-600 font-bold">nuparis.id</span></p>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $displayTitle }}
                </h2>
                <div class="flex flex-wrap items-center gap-4 text-gray-600 text-sm">
                    <span class="flex items-center">
                        <i class="fas fa-calendar mr-2"></i>
                        <span>{{ date('M d, Y') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden lg:grid lg:grid-cols-3 gap-6">
            <!-- Kiri: Detail Sertifikat -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fade-in">
                    <div class="flex flex-row lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
                        <div>
                            <div
                                class="bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-md font-mono font-semibold text-sm inline-block">
                                {{ $certificateData['id'] ?? 'N/A' }}
                            </div>
                        </div>

                        @php
                            $status = $statusDoc ?? 'Unknown';
                            $statusConfig = [
                                'valid' => ['color' => 'text-green-600', 'bg' => 'bg-green-500', 'text' => 'Valid'],
                                'expired' => ['color' => 'text-red-600', 'bg' => 'bg-red-500', 'text' => 'Expired'],
                                'pending' => [
                                    'color' => 'text-yellow-600',
                                    'bg' => 'bg-yellow-500',
                                    'text' => 'Pending',
                                ],
                                'verified' => [
                                    'color' => 'text-green-600',
                                    'bg' => 'bg-green-500',
                                    'text' => 'Verified',
                                ],
                                'rejected' => ['color' => 'text-red-600', 'bg' => 'bg-red-500', 'text' => 'Rejected'],
                                'tidak berlaku' => [
                                    'color' => 'text-red-600',
                                    'bg' => 'bg-red-500',
                                    'text' => 'Tidak Berlaku',
                                ],
                            ];
                            $statusKey = strtolower($status);
                            $statusData = $statusConfig[$statusKey] ?? [
                                'color' => 'text-gray-600',
                                'bg' => 'bg-gray-500',
                                'text' => $status,
                            ];
                        @endphp

                        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border">
                            <span class="w-2 h-2 rounded-full {{ $statusData['bg'] }}"></span>
                            <span class="font-medium {{ $statusData['color'] }}">{{ $statusData['text'] }}</span>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Document
                        Description</h3>
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <p class="text-gray-700">
                                {{ $certificateData['deskripsi'] ?? ($certificateData['keterangan'] ?? 'Tidak ada keterangan.') }}
                            </p>
                        </div>

                        @if (!empty($certificateData['catatan']))
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-900 mb-2">Additional Notes</h4>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                    <p class="text-gray-700">{!! $certificateData['catatan'] !!}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Metadata -->
                    @if (!empty($certificateData['dynamic_fields']))
                        {{-- BARU: field dinamis, sesuai template yang dipakai saat sertifikat ini digenerate --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            @foreach ($certificateData['dynamic_fields'] as $f)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-500 mb-1">{{ $f['label'] }}</p>
                                    <p class="font-medium text-gray-900">{{ $f['value'] !== '' ? $f['value'] : '-' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @elseif (!empty($certificateData['tempat']) || !empty($certificateData['tanggal']) || !empty($certificateData['tahun']))
                        {{-- Fallback: sertifikat lama (sebelum field jadi dinamis) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Tempat</p>
                                <p class="font-medium text-gray-900">{{ $certificateData['tempat'] ?: '-' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Tanggal</p>
                                <p class="font-medium text-gray-900">{{ $certificateData['tanggal'] ?: '-' }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Tahun</p>
                                <p class="font-medium text-gray-900">{{ $certificateData['tahun'] ?: '-' }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Document Details -->
                    @if ($url_document)
                        <div class="bg-white rounded-lg border mt-6">
                            <div class="p-4 border-b">
                                <h4 class="font-medium text-gray-900">Document Details</h4>
                            </div>
                            <div class="p-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-gray-600">File Size</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $size_document ? number_format($size_document / 1024, 1) . ' KB' : 'Unknown' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-gray-600">File Type</span>
                                        <span
                                            class="font-medium text-gray-900">{{ $size_mime_type ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-3">
                                        <span class="text-gray-600">File Format</span>
                                        <span
                                            class="font-medium text-gray-900">{{ strtoupper(pathinfo($url_document, PATHINFO_EXTENSION)) ?: 'Unknown' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kanan: Preview & QR -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Preview
                        Certificate</h3>

                    @if ($url_document)
                        <div class="mb-4">
                            <div class="pdf-viewer-container">
                                <embed
                                    src="{{ asset('storage/' . $url_document) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                                    type="application/pdf" class="pdf-embed">
                            </div>
                        </div>

                        <div class="space-y-3 mt-4">
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ asset('storage/' . $url_document) }}" target="_blank"
                                    class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                    <i class="fas fa-external-link-alt mr-2"></i>Buka
                                </a>
                                <a href="{{ asset('storage/' . $url_document) }}"
                                    download="{{ $displayTitle . '.pdf' }}"
                                    class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                            <button onclick="printDocument()"
                                class="bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                                <i class="fas fa-print mr-2"></i>Print Document
                            </button>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-12 text-center">
                            <i class="fas fa-file-pdf text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">Sertifikat tidak tersedia</p>
                        </div>
                    @endif

                    <!-- Document Statistics -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-3">Document Statistics</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">Verification Date</p>
                                <p class="font-medium text-sm">{{ date('M d, Y') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs text-gray-500">Security Level</p>
                                <p class="font-medium text-sm text-red-600">High</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-slide-up">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 p-4 border-b border-gray-100">Digital
                        Verification</h3>

                    <div class="text-center mb-6">
                        <div class="bg-white rounded-lg border border-gray-300 inline-block mb-4">
                            <div class="w-48 h-48 bg-white p-4 mx-auto">
                                @if (isset($qr_code) && !empty($qr_code))
                                    <img src="{{ asset('storage/' . $qr_code) }}" alt="QR Code"
                                        class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 rounded">
                                        <p class="text-gray-500 text-sm">QR Code</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-600 text-sm mb-4">Scan QR code untuk verifikasi keaslian sertifikat</p>

                        <div class="space-y-3">
                            <button onclick="saveQRCode()"
                                class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-500 cursor-pointer w-full">
                                <i class="fas fa-save mr-2"></i>Save QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet Layout -->
        <div class="lg:hidden space-y-6">
            <!-- 1. Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Preview
                    Certificate</h3>

                @if ($url_document)
                    <div class="mb-4">
                        <div class="pdf-viewer-container">
                            <embed
                                src="{{ asset('storage/' . $url_document) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                                type="application/pdf" class="pdf-embed">
                        </div>
                    </div>

                    <div class="space-y-3 mt-4">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ asset('storage/' . $url_document) }}" target="_blank"
                                class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                <i class="fas fa-external-link-alt mr-2"></i>Buka
                            </a>
                            <a href="{{ asset('storage/' . $url_document) }}"
                                download="{{ $displayTitle . '.pdf' }}"
                                class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                        <button onclick="printDocument()"
                            class="bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                            <i class="fas fa-print mr-2"></i>Print Document
                        </button>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-12 text-center">
                        <i class="fas fa-file-pdf text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Sertifikat tidak tersedia</p>
                    </div>
                @endif

                <!-- Document Statistics -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-3">Document Statistics</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Verification Date</p>
                            <p class="font-medium text-sm">{{ date('M d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Security Level</p>
                            <p class="font-medium text-sm text-red-600">High</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Detail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fade-in">
                <div class="flex flex-row justify-between items-start gap-4 mb-4">
                    <div>
                        <div
                            class="bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-md font-mono font-semibold text-sm inline-block">
                            {{ $certificateData['id'] ?? 'N/A' }}
                        </div>
                    </div>

                    @php
                        $status = $statusDoc ?? 'Unknown';
                        $statusConfig = [
                            'berlaku' => ['color' => 'text-green-600', 'bg' => 'bg-green-500', 'text' => 'Berlaku'],
                            'valid' => ['color' => 'text-green-600', 'bg' => 'bg-green-500', 'text' => 'Valid'],
                            'tidak berlaku' => [
                                'color' => 'text-red-600',
                                'bg' => 'bg-red-500',
                                'text' => 'Tidak Berlaku',
                            ],
                        ];
                        $statusKey = strtolower($status);
                        $statusData = $statusConfig[$statusKey] ?? [
                            'color' => 'text-gray-600',
                            'bg' => 'bg-gray-500',
                            'text' => $status,
                        ];
                    @endphp

                    <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border">
                        <span class="w-2 h-2 rounded-full {{ $statusData['bg'] }}"></span>
                        <span class="font-medium {{ $statusData['color'] }}">{{ $statusData['text'] }}</span>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Keterangan</h3>
                <div class="mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-700">
                            {{ $certificateData['deskripsi'] ?? ($certificateData['keterangan'] ?? 'Tidak ada keterangan.') }}
                        </p>
                    </div>

                    @if (!empty($certificateData['catatan']))
                        <div class="mt-6">
                            <h4 class="font-medium text-gray-900 mb-2">Catatan Tambahan</h4>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <p class="text-gray-700">{!! $certificateData['catatan'] !!}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if (!empty($certificateData['dynamic_fields']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @foreach ($certificateData['dynamic_fields'] as $f)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">{{ $f['label'] }}</p>
                                <p class="font-medium text-gray-900">{{ $f['value'] !== '' ? $f['value'] : '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                @elseif (!empty($certificateData['tempat']) || !empty($certificateData['tanggal']) || !empty($certificateData['tahun']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Tempat</p>
                            <p class="font-medium text-gray-900">{{ $certificateData['tempat'] ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Tanggal</p>
                            <p class="font-medium text-gray-900">{{ $certificateData['tanggal'] ?: '-' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Tahun</p>
                            <p class="font-medium text-gray-900">{{ $certificateData['tahun'] ?: '-' }}</p>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-lg border">
                    <div class="p-4 border-b">
                        <h4 class="font-medium text-gray-900">Masa Berlaku</h4>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600">Periode</span>
                            <span class="font-medium text-gray-900">{{ $certificateData['expired'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Document Details -->
                @if ($url_document)
                    <div class="bg-white rounded-lg border mt-6">
                        <div class="p-4 border-b">
                            <h4 class="font-medium text-gray-900">Document Details</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">File Size</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $size_document ? number_format($size_document / 1024, 1) . ' KB' : 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">File Type</span>
                                    <span class="font-medium text-gray-900">{{ $size_mime_type ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3">
                                    <span class="text-gray-600">File Format</span>
                                    <span
                                        class="font-medium text-gray-900">{{ strtoupper(pathinfo($url_document, PATHINFO_EXTENSION)) ?: 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 3. QR Code -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Digital
                    Verification</h3>

                <div class="text-center mb-6">
                    <div class="bg-white rounded-lg p-5 border border-gray-300 inline-block mb-4">
                        <div class="w-48 h-48 bg-white p-4 mx-auto">
                            @if (isset($qr_code) && !empty($qr_code))
                                <img src="{{ asset('storage/' . $qr_code) }}" alt="QR Code"
                                    class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 rounded">
                                    <p class="text-gray-500 text-sm">QR Code</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <p class="text-gray-600 text-sm mb-4">Scan QR code untuk verifikasi keaslian sertifikat</p>

                    <div class="space-y-3">
                        <button onclick="saveQRCode()"
                            class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                            <i class="fas fa-mobile-alt mr-2"></i>Save QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('front-end.layouts.components.footer')
    @include('front-end.layouts.components.bottom-bar')
    @include('front-end.layouts.components.chat')

    <script>
        function printDocument() {
            @if ($url_document)
                const pdfUrl = "{{ asset('storage/' . $url_document) }}";
                const printWindow = window.open(pdfUrl, '_blank');
                setTimeout(() => {
                    if (printWindow) {
                        printWindow.print();
                    }
                }, 1000);
            @endif
        }

        function saveQRCode() {
            @if (isset($qr_code) && !empty($qr_code))
                const qrUrl = "{{ asset('storage/' . $qr_code) }}";
                const link = document.createElement('a');
                link.href = qrUrl;
                link.download = 'qr-code.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            @endif
        }
    </script>
</body>

</html>
