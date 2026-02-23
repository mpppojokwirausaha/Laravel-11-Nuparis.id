<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toss | Document Verification</title>
    <meta name="title" content="{{ $documentData['name'] }}" />
    <meta name="description" content="Verifikasi dokumen dengan aman dan cepat dengan sistem Toss." />
    <meta name="keywords" content="verifikasi dokumen" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $documentData['name'] }}" />
    <meta property="og:description"
        content="Verifikasi dokumen digital yang aman, cepat, dan terpercaya. Validasi dokumen resmi." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Toss Document Verification" />
    <meta property="og:site_name" content="Toss" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="Toss" />
    <meta property="article:section" content="Document Services" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="{{ $documentData['name'] }}" />
    <meta property="twitter:description"
        content="Verifikasi dokumen digital dengan mudah dan aman. Sistem validasi dokumen terpercaya dari Toss." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Toss Document Verification Interface" />
    <meta property="twitter:site" content="@toss_verification" />
    <meta property="twitter:creator" content="@toss_verification" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="{{ $documentData['name'] }}" />
    <meta property="linkedin:description"
        content="Solusi verifikasi dokumen digital untuk bisnis dan individu. Tingkatkan keamanan dan validitas dokumen dengan Toss." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Professional Document Verification Service" />
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

        /* PDF Viewer Container */
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

        /* Responsive adjustments */
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
                    <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900">Document Verification</h1>
                    <p class="text-gray-600 mt-1">Validate document authenticity <span
                            class="text-red-500 font-bold">nuparis.id</span></p>
                </div>
            </div>

            <!-- Document Header -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $documentData['name'] ?? 'Untitled Document' }}
                </h2>
                <div class="flex flex-wrap items-center gap-4 text-gray-600 text-sm">
                    <span class="flex items-center">
                        <i class="fas fa-calendar mr-2"></i>
                        <span>{{ date('M d, Y') }}</span>
                    </span>
                    @if (isset($documentData['created_at']))
                        <span class="flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>Created: {{ date('M d, Y', strtotime($documentData['created_at'])) }}</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Desktop Layout (lg: ke atas) -->
        <div class="hidden lg:grid lg:grid-cols-3 gap-6">
            <!-- Kiri: Document Details (2 kolom) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Document ID and Status Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fade-in">
                    <!-- [Konten Document Details sama seperti sebelumnya] -->
                    <div class="flex flex-row lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
                        <div>
                            <div
                                class="bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-md font-mono font-semibold text-sm inline-block">
                                {{ $documentData['id'] ?? 'N/A' }}
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

                    <!-- Description -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Document
                        Description</h3>
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <p class="text-gray-700">
                                {{ $documentData['description'] ?? 'No description available for this document.' }}
                            </p>
                        </div>

                        @if (!empty($documentData['notes']))
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-900 mb-2">Additional Notes</h4>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                    <p class="text-gray-700">{!! $documentData['notes'] !!}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Signed By</p>
                            <p class="font-medium text-gray-900">{{ $documentData['signedBy'] ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Received By</p>
                            <p class="font-medium text-gray-900">{{ $documentData['receivedBy'] ?? 'Unknown' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Document Type</p>
                            <p class="font-medium text-gray-900">{{ $documentData['action'] ?? 'General' }}</p>
                        </div>
                    </div>

                    <!-- Document Details -->
                    <div class="bg-white rounded-lg border">
                        <div class="p-4 border-b">
                            <h4 class="font-medium text-gray-900">Document Details</h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">File Size</span>
                                    <span class="font-medium text-gray-900"
                                        id="fileSize">{{ $size_document ?? '0 KB' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">File Type</span>
                                    <span class="font-medium text-gray-900">{{ $size_mime_type ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">File Format</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $file_format ?? strtoupper(pathinfo($url_document ?? '', PATHINFO_EXTENSION)) ?: 'Unknown' }}</span>
                                </div>
                                {{-- <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-gray-600">Expiry Status</span>
                                    <span
                                        class="font-medium {{ $documentData['expired'] ?? false ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $documentData['expired'] ?? false ? 'Expired' : 'Active' }}
                                    </span>
                                </div> --}}
                                @if (isset($documentData['expiry_date']))
                                    <div class="flex justify-between items-center py-3">
                                        <span class="text-gray-600">Expiry Date</span>
                                        <span
                                            class="font-medium text-gray-900">{{ date('M d, Y', strtotime($documentData['expiry_date'])) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Preview & QR (1 kolom) -->
            <div class="space-y-6">
                <!-- Preview Document -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Document Preview
                    </h3>

                    <!-- PDF Viewer -->
                    @if ($url_document)
                        <div class="mb-4">
                            <div class="pdf-viewer-container">
                                <embed
                                    src="{{ asset('storage/' . $url_document) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                                    type="application/pdf" class="pdf-embed">
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-12 text-center">
                            <i class="fas fa-file-pdf text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No document available</p>
                        </div>
                    @endif

                    <!-- Tombol aksi -->
                    @if ($url_document)
                        <div class="space-y-3 mt-4">
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ asset('storage/' . $url_document) }}" target="_blank"
                                    class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                    <i class="fas fa-external-link-alt mr-2"></i>Open Full
                                </a>
                                <a href="{{ asset('storage/' . $url_document) }}"
                                    download="{{ ($documentData['name'] ?? 'document') . '.pdf' }}"
                                    class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                            <button onclick="printDocument()"
                                class="bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                                <i class="fas fa-print mr-2"></i>Print Document
                            </button>
                        </div>
                    @endif

                    <!-- Document Stats -->
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Digital
                        Verification</h3>

                    <div class="text-center mb-6">
                        <div class="bg-white rounded-lg border border-gray-300 inline-block mb-4">
                            <!-- QR Code -->
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

                        <p class="text-gray-600 text-sm mb-4">Scan QR code to verify document authenticity</p>

                        <div class="space-y-3">
                            {{-- <button onclick="scanWithMobile()"
                                class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                                <i class="fas fa-mobile-alt mr-2"></i>Scan with Mobile
                            </button> --}}
                            <button onclick="saveQRCode()"
                                class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-500 cursor-pointer w-full">
                                <i class="fas fa-save mr-2"></i>Save QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet Layout (sm dan md) -->
        <div class="lg:hidden space-y-6">
            <!-- 1. Preview Document -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Document Preview
                </h3>

                <!-- PDF Viewer -->
                @if ($url_document)
                    <div class="mb-4">
                        <div class="pdf-viewer-container">
                            <embed
                                src="{{ asset('storage/' . $url_document) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                                type="application/pdf" class="pdf-embed">
                        </div>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-12 text-center">
                        <i class="fas fa-file-pdf text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No document available</p>
                    </div>
                @endif

                <!-- Tombol aksi -->
                @if ($url_document)
                    <div class="space-y-3 mt-4">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ asset('storage/' . $url_document) }}" target="_blank"
                                class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                <i class="fas fa-external-link-alt mr-2"></i>Open Full
                            </a>
                            <a href="{{ asset('storage/' . $url_document) }}"
                                download="{{ ($documentData['name'] ?? 'document') . '.pdf' }}"
                                class="bg-white text-red-600 px-4 py-2.5 rounded-lg font-medium transition-all duration-200 border border-red-600 hover:bg-red-50 cursor-pointer w-full text-center">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                        <button onclick="printDocument()"
                            class="bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                            <i class="fas fa-print mr-2"></i>Print Document
                        </button>
                    </div>
                @endif

                <!-- Document Stats -->
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

            <!-- 2. Document Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fade-in">
                <div class="flex flex-row justify-between items-start gap-4 mb-4">
                    <div>
                        <div
                            class="bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-md font-mono font-semibold text-sm inline-block">
                            {{ $documentData['id'] ?? 'N/A' }}
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

                <!-- Description -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Document
                    Description</h3>
                <div class="mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <p class="text-gray-700">
                            {{ $documentData['description'] ?? 'No description available for this document.' }}
                        </p>
                    </div>

                    @if (!empty($documentData['notes']))
                        <div class="mt-6">
                            <h4 class="font-medium text-gray-900 mb-2">Additional Notes</h4>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <p class="text-gray-700">{!! $documentData['notes'] !!}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Metadata -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Signed By</p>
                        <p class="font-medium text-gray-900">{{ $documentData['signedBy'] ?? 'Unknown' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Received By</p>
                        <p class="font-medium text-gray-900">{{ $documentData['receivedBy'] ?? 'Unknown' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Document Type</p>
                        <p class="font-medium text-gray-900">{{ $documentData['action'] ?? 'General' }}</p>
                    </div>
                </div>

                <!-- Document Details -->
                <div class="bg-white rounded-lg border">
                    <div class="p-4 border-b">
                        <h4 class="font-medium text-gray-900">Document Details</h4>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">File Size</span>
                                <span class="font-medium text-gray-900" id="fileSize">
                                    @if (isset($size_document) && is_numeric($size_document))
                                        @php
                                            // Fungsi konversi langsung di blade
                                            function formatFileSize($bytes, $decimals = 2)
                                            {
                                                if ($bytes === 0 || $bytes === null) {
                                                    return '0 KB';
                                                }

                                                $k = 1024;
                                                $dm = $decimals < 0 ? 0 : $decimals;
                                                $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

                                                $i = floor(log($bytes) / log($k));

                                                return number_format($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
                                            }
                                        @endphp
                                        {{ formatFileSize($size_document) }}
                                    @elseif(isset($size_document) && is_string($size_document))
                                        {{ $size_document }}
                                    @else
                                        0 KB
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">File Type</span>
                                <span class="font-medium text-gray-900">{{ $size_mime_type ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">File Format</span>
                                <span
                                    class="font-medium text-gray-900">{{ $file_format ?? strtoupper(pathinfo($url_document ?? '', PATHINFO_EXTENSION)) ?: 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-gray-600">Expiry Status</span>
                                <span
                                    class="font-medium {{ $documentData['expired'] ?? false ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $documentData['expired'] ?? false ? 'Expired' : 'Active' }}
                                </span>
                            </div>
                            @if (isset($documentData['expiry_date']))
                                <div class="flex justify-between items-center py-3">
                                    <span class="text-gray-600">Expiry Date</span>
                                    <span
                                        class="font-medium text-gray-900">{{ date('M d, Y', strtotime($documentData['expiry_date'])) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. QR Code -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-slide-up">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">Digital Verification
                </h3>

                <div class="text-center mb-6">
                    <div class="bg-white rounded-lg p-5 border border-gray-300 inline-block mb-4">
                        <!-- QR Code -->
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

                    <p class="text-gray-600 text-sm mb-4">Scan QR code to verify document authenticity</p>

                    <div class="space-y-3">
                        {{-- <button onclick="scanWithMobile()"
                            class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 hover:bg-red-700 cursor-pointer w-full">
                            <i class="fas fa-mobile-alt mr-2"></i>Scan with Mobile
                        </button> --}}
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
        // Simple JavaScript functions
        function printDocument() {
            @if ($url_document)
                const pdfUrl = "{{ asset('storage/' . $url_document) }}";
                const printWindow = window.open(pdfUrl, '_blank');
                setTimeout(() => {
                    if (printWindow) {
                        printWindow.print();
                    } else {
                        showNotification('Could not open document for printing', 'error');
                    }
                }, 1000);
            @else
                showNotification('No document available for printing', 'error');
            @endif
        }

        function scanWithMobile() {
            showNotification('Open your camera app to scan the QR code', 'info');
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
                showNotification('QR code saved to your device', 'success');
            @else
                showNotification('QR code not available', 'error');
            @endif
        }

        function showNotification(message, type) {
            // Remove existing notifications
            document.querySelectorAll('.custom-notification').forEach(el => el.remove());

            // Create notification element
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-50' :
                type === 'error' ? 'bg-red-50' : 'bg-blue-50';
            const borderColor = type === 'success' ? 'border-green-200' :
                type === 'error' ? 'border-red-200' : 'border-blue-200';
            const textColor = type === 'success' ? 'text-green-700' :
                type === 'error' ? 'text-red-700' : 'text-blue-700';
            const icon = type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

            notification.className =
                `custom-notification fixed top-4 right-4 ${bgColor} ${borderColor} ${textColor} px-4 py-3 rounded-lg border shadow-lg z-50 animate-slide-up`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Add animation delay to cards
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>

</html>
