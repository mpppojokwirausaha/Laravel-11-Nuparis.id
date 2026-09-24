<!DOCTYPE html>
<html lang="id">

<head>
    @include('front-end.layouts.components.seo-meta')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>

    <style>
        /* Custom CSS untuk sticky behavior yang lebih baik */
        .sticky-sidebar {
            position: -webkit-sticky;
            position: sticky;
            top: 6rem;
            align-self: flex-start;
        }

        /* Hide scrollbar but allow scrolling */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Touch-friendly elements */
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }

        /* Prevent content jump on mobile */
        @media (max-width: 1023px) {
            .sticky-sidebar {
                position: static !important;
            }
        }

        /* Better text truncation */
        .text-ellipsis-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Prevent zoom on iOS */
        input,
        textarea,
        select {
            font-size: 16px !important;
        }

        /* Smooth image transition */
        .gallery-main-image {
            transition: opacity 0.3s ease;
        }

        /* Swipe indicator animation */
        @keyframes swipePulse {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }
        }

        .swipe-indicator {
            animation: swipePulse 2s infinite;
        }

        /* Thumbnail hover effect */
        .thumbnail {
            transition: all 0.2s ease;
        }

        .thumbnail:hover {
            transform: translateY(-2px);
        }

        /* WhatsApp button animation */
        .whatsapp-btn {
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .whatsapp-btn:active {
            transform: translateY(0);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease forwards;
        }

        /* Video badge */
        .video-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: rgba(220, 38, 38, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            z-index: 10;
        }
    </style>
</head>

<body class="font-inter text-gray-700 bg-gray-50">
    <!-- Navigation untuk desktop -->
    @include('front-end.layouts.components.header')

    <!-- Main Content -->
    <main class="w-full max-w-screen-2xl mx-auto px-4 lg:pt-24 sm:px-4 md:px-6 lg:px-8 py-4 md:py-6 lg:py-8">
        <!-- Layout Container -->
        <div class="flex flex-col lg:flex-row lg:gap-8">
            <!-- Left Column (2/3 width on desktop) -->
            <div class="lg:w-2/3 space-y-6 md:space-y-8">
                <!-- Header Section -->
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight break-words">
                            {{ $property->property_name }}
                        </h1>
                        <div class="flex items-center text-gray-600 mt-2 text-sm md:text-base">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2 flex-shrink-0"></i>
                            <span class="line-clamp-2">{{ $property->property_address }}</span>
                        </div>
                    </div>

                    <!-- Combined Media Gallery (Images + Videos) -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        @php
                            // Gabungkan semua media (images + videos)
                            $allMedia = [];
                            $mediaTypes = []; // 'image' atau 'video'

                            // Tambahkan gambar dari property_image
                            if (isset($propertyImages) && is_array($propertyImages)) {
                                foreach ($propertyImages as $image) {
                                    if (!empty($image)) {
                                        // Deteksi apakah ini gambar atau video berdasarkan ekstensi
                                        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                                        $isVideo = in_array($extension, [
                                            'mp4',
                                            'mov',
                                            'avi',
                                            'wmv',
                                            'flv',
                                            'mkv',
                                            'webm',
                                        ]);

                                        $allMedia[] = [
                                            'type' => $isVideo ? 'video' : 'image',
                                            'url' => asset('storage/' . $image),
                                            'storage_path' => $image,
                                            'filename' => basename($image),
                                        ];
                                    }
                                }
                            }

                            // Tambahkan video dari property_video jika ada
                            $videoData = [];
                            if (!empty($property->property_video)) {
                                if (is_string($property->property_video)) {
                                    $decoded = json_decode($property->property_video, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $videoData = $decoded;
                                    } else {
                                        $videoData = [$property->property_video];
                                    }
                                } elseif (is_array($property->property_video)) {
                                    $videoData = $property->property_video;
                                }
                            }

                            foreach ($videoData as $video) {
                                if (!empty($video)) {
                                    // Cek apakah video adalah URL YouTube atau file lokal
                                    $isYouTube = preg_match(
                                        '/youtu\.be\/([a-zA-Z0-9_-]+)|youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
                                        $video,
                                    );

                                    $allMedia[] = [
                                        'type' => 'video',
                                        'url' => $video,
                                        'is_youtube' => $isYouTube,
                                        'filename' => $isYouTube ? 'YouTube Video' : basename($video),
                                    ];
                                }
                            }

                            $hasMedia = count($allMedia) > 0;
                            $firstMedia = $hasMedia ? $allMedia[0] : null;
                        @endphp

                        @if ($hasMedia)
                            <!-- Main Media Display -->
                            <div class="relative h-56 sm:h-64 md:h-72 lg:h-96 overflow-hidden bg-black"
                                id="galleryContainer">
                                <!-- Media akan ditampilkan oleh JavaScript -->
                            </div>

                            <!-- Thumbnails -->
                            @if (count($allMedia) > 1)
                                <div class="px-3 py-2 md:px-4 md:py-3 border-t border-gray-100">
                                    <div class="flex space-x-2 md:space-x-3 overflow-x-auto scrollbar-hide pb-1"
                                        id="thumbnailsContainer">
                                        @foreach ($allMedia as $index => $media)
                                            <button
                                                class="thumbnail flex-shrink-0 w-16 h-12 sm:w-20 sm:h-16 md:w-24 md:h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 touch-target relative {{ $index == 0 ? 'border-red-500 scale-105 shadow-sm' : 'border-gray-200 hover:border-red-400' }}"
                                                onclick="changeMedia({{ $index }})">
                                                @if ($media['type'] === 'image')
                                                    <img src="{{ $media['url'] }}" class="w-full h-full object-cover"
                                                        alt="Thumbnail {{ $index + 1 }}" loading="lazy">
                                                @else
                                                    <!-- Thumbnail untuk video -->
                                                    @if (isset($media['is_youtube']) && $media['is_youtube'])
                                                        @php
                                                            preg_match(
                                                                '/youtu\.be\/([a-zA-Z0-9_-]+)|youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
                                                                $media['url'],
                                                                $matches,
                                                            );
                                                            $videoId = $matches[1] ?? ($matches[2] ?? '');
                                                        @endphp
                                                        <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg"
                                                            class="w-full h-full object-cover"
                                                            alt="Video {{ $index + 1 }}">
                                                    @else
                                                        <!-- Thumbnail untuk video lokal -->
                                                        <div
                                                            class="w-full h-full bg-gray-800 flex items-center justify-center">
                                                            <i class="fas fa-video text-gray-400 text-lg"></i>
                                                        </div>
                                                    @endif
                                                    <!-- Video badge -->
                                                    <div class="video-badge">
                                                        <i class="fas fa-play text-xs"></i>
                                                        <span>VIDEO</span>
                                                    </div>
                                                @endif
                                                <div
                                                    class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] px-1 rounded">
                                                    {{ $index + 1 }}
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Placeholder if no media -->
                            <div
                                class="relative h-56 sm:h-64 md:h-72 lg:h-96 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price Card - Optimized for Mobile -->
                <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-gray-200 relative">
                    @if ($property->property_transaction_type == 'Jual')
                        <!-- PPN badge dengan absolute position -->
                        <div class="absolute top-2 right-2 md:top-3 md:right-3 z-10">
                            <div
                                class="bg-red-600 text-white font-bold px-2 py-1 md:px-3 md:py-1.5 rounded-lg text-xs md:text-sm flex items-center gap-1 md:gap-2 shadow-md">
                                <svg class="w-2.5 h-2.5 md:w-3.5 md:h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>+ PPN 11%</span>
                            </div>
                        </div>
                    @endif

                    <!-- Konten harga tanpa terpengaruh PPN -->
                    <div>
                        <div class="text-xs md:text-sm font-semibold text-gray-500 uppercase tracking-wide">Harga</div>
                        <div class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 format-rupiah">
                            Rp {{ number_format($property->property_price, 0, ',', '.') }}
                        </div>

                        @if ($property->property_transaction_type == 'Jual')
                            <div class="text-xs md:text-sm text-red-600 mt-1.5 flex items-center gap-1 md:gap-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Harga belum termasuk PPN 11%</span>
                            </div>
                        @endif
                    </div>

                    <!-- Border dan grid property info -->
                    <div class="border-t border-gray-100 pt-3 md:pt-4 mt-3 md:mt-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <!-- Luas Tanah -->
                            <div class="bg-white rounded-lg border border-gray-200 p-3 md:p-4">
                                <div
                                    class="text-xs md:text-sm font-medium text-gray-600 mb-1 md:mb-2 flex items-center gap-1 md:gap-2">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-red-500 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">Luas Tanah</span>
                                </div>
                                <div
                                    class="text-base md:text-lg lg:text-xl font-bold text-gray-900 break-words overflow-visible">
                                    {{ $property->property_land_area }}
                                </div>
                            </div>

                            <!-- Luas Bangunan -->
                            <div class="bg-white rounded-lg border border-gray-200 p-3 md:p-4">
                                <div
                                    class="text-xs md:text-sm font-medium text-gray-600 mb-1 md:mb-2 flex items-center gap-1 md:gap-2">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-red-500 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">Luas Bangunan</span>
                                </div>
                                <div
                                    class="text-base md:text-lg lg:text-xl font-bold text-gray-900 break-words overflow-visible">
                                    {{ $property->property_building_area }}
                                </div>
                            </div>

                            <!-- Status Properti -->
                            <div
                                class="bg-white rounded-lg border border-gray-200 p-3 md:p-4 sm:col-span-2 md:col-span-1">
                                <div
                                    class="text-xs md:text-sm font-medium text-gray-600 mb-1 md:mb-2 flex items-center gap-1 md:gap-2">
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-red-500 flex-shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">Status</span>
                                </div>
                                <div class="text-base md:text-lg lg:text-xl font-bold text-gray-900">
                                    {{ $property->property_transaction_type }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-3 md:mb-4">Deskripsi Properti</h2>
                    <div class="text-gray-600 text-sm md:text-base leading-relaxed whitespace-pre-line">
                        {!! $property->property_description ?? 'Tidak ada deskripsi tersedia.' !!}
                    </div>
                </div>

                <!-- Sertifikat & Fasilitas Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                    <!-- Sertifikat -->
                    @if (is_array($certificate) && count($certificate) > 0)
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Sertifikat &
                                Legalitas</h2>
                            <div class="space-y-3 md:space-y-4">
                                @foreach ($certificate as $item)
                                    @if (!empty(trim($item)))
                                        <div
                                            class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div
                                                class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fas fa-file-contract text-red-600 text-sm md:text-base"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm md:text-base">
                                                    {{ trim($item) }}</h4>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Fasilitas -->
                    @if (is_array($fasilities) && count($fasilities) > 0)
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Fasilitas</h2>

                            @php
                                $showLimit = 5;
                                $totalFasilities = count($fasilities);
                                $hasMore = $totalFasilities > $showLimit;
                            @endphp

                            <div
                                class="space-y-3 md:space-y-4 {{ $hasMore ? 'max-h-[400px] overflow-y-auto pr-2' : '' }}">
                                @foreach ($fasilities as $index => $fasility)
                                    @if (!empty(trim($fasility)))
                                        <div
                                            class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 {{ $hasMore && $index >= $showLimit ? 'opacity-90' : '' }}">
                                            <div
                                                class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fas fa-check-circle text-red-600 text-sm md:text-base"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm md:text-base">
                                                    {{ trim($fasility) }}
                                                </h4>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if ($hasMore)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-between text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-chevron-down text-red-500"></i>
                                            <span>Scroll untuk melihat {{ $totalFasilities - $showLimit }} fasilitas
                                                lainnya</span>
                                        </div>
                                        <span
                                            class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                                            Total: {{ $totalFasilities }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Location Section -->
                <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-gray-200">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 md:w-6 md:h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>
                        Lokasi Properti
                    </h2>

                    <!-- Address & Buttons -->
                    <div class="space-y-4 mb-4 md:mb-6">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <span class="text-gray-700 text-sm md:text-base leading-relaxed whitespace-pre-line">
                                {{ $property->property_address }}
                            </span>
                        </div>

                        <div class="flex flex-col lg:flex-row gap-3">
                            <a href="https://www.google.com/maps/search/{{ urlencode($property->property_address) }}"
                                target="_blank"
                                class="lg:flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold text-sm touch-target">
                                <i class="fas fa-directions"></i>
                                <span>Petunjuk Arah</span>
                            </a>

                            <button onclick="copyAddress()"
                                class="lg:flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors font-medium text-sm border border-gray-300 touch-target">
                                <i class="far fa-copy"></i>
                                <span>Salin Alamat</span>
                            </button>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div
                        class="rounded-xl overflow-hidden mb-4 h-64 sm:h-72 md:h-80 lg:h-96 shadow-lg border border-gray-300 relative">
                        @php
                            $lat = $property->property_latitude ?? -6.5569;
                            $lng = $property->property_longitude ?? 107.4433;
                            $hasValidCoords =
                                isset($property->property_latitude) &&
                                isset($property->property_longitude) &&
                                is_numeric($property->property_latitude) &&
                                is_numeric($property->property_longitude);

                            if (!$hasValidCoords) {
                                $fallbackAddress = urlencode($property->property_address);
                            }
                        @endphp

                        <div id="map-loader"
                            class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center z-10">
                            <div class="text-center">
                                <div
                                    class="inline-block animate-spin rounded-full h-8 w-8 md:h-10 md:w-10 border-t-2 border-b-2 border-red-500 mb-2 md:mb-3">
                                </div>
                                <p class="text-gray-700 font-medium text-sm md:text-base">Memuat peta lokasi...</p>
                            </div>
                        </div>

                        <iframe
                            src="{{ $hasValidCoords
                                ? "https://maps.google.com/maps?q={$lat},{$lng}&hl=id&z=16&output=embed&markers=color:red%7C{$lat},{$lng}"
                                : "https://maps.google.com/maps?q={$fallbackAddress}&hl=id&z=15&output=embed" }}"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full relative z-20 opacity-0 transition-opacity duration-500"
                            title="Lokasi {{ $property->property_name }}"
                            onload="document.getElementById('map-loader').style.display = 'none'; this.classList.remove('opacity-0');">
                        </iframe>

                        @if ($hasValidCoords)
                            <div
                                class="absolute bottom-2 left-2 md:bottom-3 md:left-3 bg-white/90 backdrop-blur-sm px-2 py-1 md:px-3 md:py-2 rounded shadow-sm border border-gray-200 z-30">
                                <div class="text-xs text-gray-600 flex items-center gap-1">
                                    <i class="fas fa-crosshairs text-red-500 text-xs"></i>
                                    Koordinat:
                                </div>
                                <div class="text-xs font-mono text-gray-800 font-medium">
                                    {{ number_format($lat, 6) }}, {{ number_format($lng, 6) }}
                                </div>
                            </div>
                        @endif
                    </div>

                    @if (!$hasValidCoords)
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start gap-2 md:gap-3">
                                <div class="text-yellow-600 mt-0.5">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-yellow-800 font-medium">Peta menampilkan lokasi berdasarkan
                                        alamat teks</p>
                                    <p class="text-xs text-yellow-700 mt-1">Untuk akurasi lebih baik, tambahkan
                                        koordinat di data properti.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column (Sidebar) -->
            <div class="lg:w-1/3 mt-6 lg:mt-0">
                <!-- Sticky Container untuk desktop saja -->
                <div class="sticky-sidebar space-y-6">
                    <!-- WhatsApp Contact Card -->
                    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                        <div class="text-center mb-4 md:mb-6">
                            <div
                                class="w-12 h-12 md:w-16 md:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fab fa-whatsapp text-red-600 text-xl md:text-2xl"></i>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-1 md:mb-2">Hubungi Kami</h3>
                        </div>

                        <div class="space-y-3 md:space-y-4">
                            <!-- WhatsApp button selalu tampil -->
                            <a href="https://wa.me/{{ str_replace(' ', '', $property->property_no_whatsapp ?: env('NO_WHATSAPP')) }}?text=Halo, saya tertarik dengan properti &quot;{{ urlencode($property->property_name) }}&quot; di NUPARIS.ID.%0A%0ALink detail: {{ url()->current() }}%0A%0AMohon info lebih lanjut. Terima kasih."
                                target="_blank"
                                class="whatsapp-btn block w-full py-3 md:py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-center transition-all duration-300 touch-target text-sm md:text-base">
                                <i class="fab fa-whatsapp mr-2"></i>
                                WhatsApp Sekarang
                            </a>

                            <div class="text-center text-gray-500 text-xs md:text-sm">
                                <p><i class="fas fa-bolt text-yellow-500 mr-1"></i> Respon cepat dalam 15 menit</p>
                                <p><i class="far fa-clock text-gray-400 mr-1"></i> Jam operasional: 08:00 - 20:00 WIB
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Related Properties -->
                    @if (isset($relatedProperties) && $relatedProperties->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <div class="flex items-center justify-between mb-3 md:mb-4">
                                <h3 class="text-base md:text-lg font-semibold text-gray-900">Properti
                                    Serupa</h3>
                            </div>

                            <div class="space-y-3 md:space-y-4">
                                @foreach ($relatedProperties as $related)
                                    @php
                                        $relatedImages = [];
                                        if (!empty($related->property_image)) {
                                            $relatedImages = is_string($related->property_image)
                                                ? json_decode($related->property_image, true)
                                                : $related->property_image;
                                        }
                                        $relatedImages = is_array($relatedImages) ? $relatedImages : [];
                                        $firstImage = !empty($relatedImages) ? $relatedImages[0] : null;
                                    @endphp

                                    @if ($related->property_slug)
                                        <a href="{{ route('property-detail', ['property_slug' => $related->property_slug]) }}"
                                            class="flex items-center gap-3 p-2 md:p-3 hover:bg-gray-50 rounded-lg transition-colors group border border-transparent hover:border-red-200">
                                            <div class="relative flex-shrink-0">
                                                @if ($firstImage)
                                                    <img src="{{ asset('storage/' . $firstImage) }}"
                                                        class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover"
                                                        alt="{{ $related->property_name }}">
                                                @else
                                                    <div
                                                        class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-home text-gray-400 text-sm md:text-base"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 text-sm md:text-sm truncate">
                                                    {{ $related->property_name }}
                                                </h4>
                                                <p class="text-gray-600 text-xs mb-1 truncate">
                                                    {{ Str::limit($related->property_address, 25) }}
                                                </p>
                                                <div class="flex items-center justify-between">
                                                    <span
                                                        class="font-bold text-gray-900 text-xs md:text-sm format-rupiah">
                                                        Rp
                                                        {{ number_format($related->property_price, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                                <a href="{{ route('property-more') }}"
                                    class="block w-full text-center py-2 border border-red-600 text-red-600 hover:bg-red-50 font-medium rounded-lg transition-colors text-xs md:text-sm touch-target">
                                    Lihat Lebih Banyak Properti
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @include('front-end.layouts.components.footer')
    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.bottom-bar')

    <script>
        // Global variables
        let currentMediaIndex = 0;
        let autoSlideInterval;
        let isUserInteracting = false;
        let userInteractionTimer;
        let isVideoPlaying = false;

        // Data dari PHP
        const allMedia = @json($allMedia ?? []);
        const hasMedia = allMedia && allMedia.length > 0;

        // Fungsi untuk mengganti media
        function changeMedia(index) {
            if (index < 0) index = allMedia.length - 1;
            if (index >= allMedia.length) index = 0;

            currentMediaIndex = index;
            const media = allMedia[currentMediaIndex];

            // Stop video if playing
            stopVideoPlayback();

            // Update counter
            const currentMediaElement = document.getElementById('currentMedia');
            if (currentMediaElement) {
                currentMediaElement.textContent = currentMediaIndex + 1;
            }

            // Update thumbnail selection
            updateThumbnails();

            // Tampilkan media
            displayMedia(media);

            markUserInteraction();
        }

        // Fungsi untuk menampilkan media
        function displayMedia(media) {
            const galleryContainer = document.getElementById('galleryContainer');
            if (!galleryContainer) return;

            // Clear existing content
            galleryContainer.innerHTML = '';

            if (media.type === 'image') {
                // Tampilkan gambar
                const img = document.createElement('img');
                img.id = 'mainMedia';
                img.src = media.url;
                img.alt = "{{ $property->property_name }}";
                img.className = 'w-full h-full object-cover transition-opacity duration-300';

                // Fade in effect
                img.style.opacity = '0';
                galleryContainer.appendChild(img);

                setTimeout(() => {
                    img.style.opacity = '1';
                }, 100);

            } else if (media.type === 'video') {
                // Tampilkan video
                displayVideo(media);
            }

            // Tambahkan counter media
            if (allMedia.length > 1) {
                addMediaCounter();
            }
        }

        // Fungsi untuk menampilkan video
        function displayVideo(media) {
            const galleryContainer = document.getElementById('galleryContainer');
            if (!galleryContainer) return;

            const videoUrl = media.url;
            const isYouTube = media.is_youtube || isYouTubeUrl(videoUrl);

            if (isYouTube) {
                // YouTube video
                const videoId = extractYouTubeId(videoUrl);
                displayYouTubeVideo(videoId, galleryContainer);
            } else {
                // Direct video file
                displayDirectVideo(videoUrl, galleryContainer);
            }
        }

        // Fungsi untuk menampilkan video YouTube
        function displayYouTubeVideo(videoId, container) {
            const videoDiv = document.createElement('div');
            videoDiv.id = 'videoPlayer';
            videoDiv.className = 'w-full h-full relative';
            videoDiv.innerHTML = `
                <div id="videoThumbnail" class="absolute inset-0 cursor-pointer">
                    <img src="https://img.youtube.com/vi/${videoId}/maxresdefault.jpg"
                        class="w-full h-full object-cover"
                        alt="Video {{ $property->property_name }}"
                        onerror="this.src='https://img.youtube.com/vi/${videoId}/hqdefault.jpg'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition-colors shadow-lg cursor-pointer" onclick="playYouTubeVideo('${videoId}')">
                            <i class="fas fa-play text-white text-2xl ml-1"></i>
                        </div>
                    </div>
                </div>
                <iframe id="youtubeIframe" class="w-full h-full absolute inset-0 hidden" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            `;
            container.appendChild(videoDiv);
        }

        // Fungsi untuk menampilkan video langsung
        function displayDirectVideo(videoUrl, container) {
            const videoWrapper = document.createElement('div');
            videoWrapper.className = 'w-full h-full relative';

            const video = document.createElement('video');
            video.id = 'directVideo';
            video.className = 'w-full h-full';
            video.controls = true;
            video.autoplay = false;
            video.playsInline = true;
            video.preload = 'metadata';

            const source = document.createElement('source');
            source.src = videoUrl;
            source.type = 'video/mp4';

            video.appendChild(source);
            video.innerHTML += 'Browser Anda tidak mendukung pemutaran video.';

            videoWrapper.appendChild(video);
            container.appendChild(videoWrapper);

            // Tambahkan event listener untuk mendeteksi saat video diputar
            video.addEventListener('play', function() {
                isVideoPlaying = true;
                stopAutoSlide();
            });

            video.addEventListener('pause', function() {
                isVideoPlaying = false;
                if (!isUserInteracting) {
                    setTimeout(() => startAutoSlide(), 2000);
                }
            });

            video.addEventListener('ended', function() {
                isVideoPlaying = false;
                if (!isUserInteracting && allMedia.length > 1) {
                    setTimeout(() => startAutoSlide(), 2000);
                }
            });
        }

        // Fungsi untuk mengekstrak ID dari URL YouTube
        function extractYouTubeId(url) {
            const match = url.match(/youtu\.be\/([a-zA-Z0-9_-]+)|youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/);
            return match ? (match[1] || match[2]) : '';
        }

        // Fungsi untuk memutar video YouTube
        function playYouTubeVideo(videoId) {
            const iframe = document.getElementById('youtubeIframe');
            const thumbnail = document.getElementById('videoThumbnail');

            if (iframe && thumbnail) {
                iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&showinfo=0&modestbranding=1`;
                iframe.classList.remove('hidden');
                thumbnail.style.display = 'none';

                // Set video sedang diputar
                isVideoPlaying = true;
                stopAutoSlide();

                // Monitor ketika video selesai
                iframe.onload = function() {
                    // YouTube iframe tidak memiliki event yang bisa kita akses langsung
                    // Kita akan menggunakan interval untuk check
                    const checkVideoEnded = setInterval(() => {
                        try {
                            // Coba akses YouTube player state (ini hanya bekerja di environment yang sama)
                            if (iframe.contentWindow && iframe.contentWindow.postMessage) {
                                iframe.contentWindow.postMessage('{"event":"command","func":"getPlayerState"}',
                                    '*');
                            }
                        } catch (e) {
                            // Jika ada error, clear interval
                            clearInterval(checkVideoEnded);
                            isVideoPlaying = false;
                            if (!isUserInteracting) {
                                setTimeout(() => startAutoSlide(), 2000);
                            }
                        }
                    }, 1000);

                    // Clear interval setelah 5 menit untuk menghindari memory leak
                    setTimeout(() => clearInterval(checkVideoEnded), 300000);
                };
            }
        }

        // Fungsi untuk menghentikan pemutaran video
        function stopVideoPlayback() {
            // Hentikan video YouTube
            const youtubeIframe = document.getElementById('youtubeIframe');
            if (youtubeIframe) {
                youtubeIframe.src = '';
            }

            // Hentikan video langsung
            const directVideo = document.getElementById('directVideo');
            if (directVideo) {
                directVideo.pause();
                directVideo.currentTime = 0;
            }

            isVideoPlaying = false;
        }

        // Fungsi navigasi
        function nextMedia() {
            changeMedia(currentMediaIndex + 1);
        }

        function prevMedia() {
            changeMedia(currentMediaIndex - 1);
        }

        // Fungsi untuk menambahkan counter media
        function addMediaCounter() {
            const galleryContainer = document.getElementById('galleryContainer');
            if (!galleryContainer) return;

            const counter = document.createElement('div');
            counter.className = 'absolute top-3 right-3 bg-black/70 text-white text-xs px-2 py-1 rounded-full z-20';
            counter.id = 'mediaCounter';
            counter.innerHTML = `<span id="currentMedia">${currentMediaIndex + 1}</span> / <span>${allMedia.length}</span>`;
            galleryContainer.appendChild(counter);
        }

        // Fungsi untuk update thumbnail selection
        function updateThumbnails() {
            document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                thumb.classList.remove('border-red-500', 'scale-105', 'shadow-sm');
                thumb.classList.add('border-gray-200');

                if (i === currentMediaIndex) {
                    thumb.classList.add('border-red-500', 'scale-105', 'shadow-sm');
                    thumb.classList.remove('border-gray-200');

                    // Scroll to active thumbnail
                    if (window.innerWidth < 768) {
                        const container = thumb.parentElement.parentElement;
                        const thumbLeft = thumb.offsetLeft;
                        const containerWidth = container.offsetWidth;
                        const thumbWidth = thumb.offsetWidth;

                        container.scrollTo({
                            left: thumbLeft - (containerWidth / 2) + (thumbWidth / 2),
                            behavior: 'smooth'
                        });
                    }
                }
            });
        }

        // ===== AUTO-SLIDE FUNCTIONS =====
        function startAutoSlide() {
            if (hasMedia && allMedia.length > 1 && !isUserInteracting && !isVideoPlaying) {
                stopAutoSlide();

                autoSlideInterval = setInterval(() => {
                    // Cek apakah media saat ini adalah video yang sedang diputar
                    const currentMedia = allMedia[currentMediaIndex];
                    if (currentMedia.type !== 'video' || !isVideoPlaying) {
                        nextMedia();
                    }
                }, 5000); // 5 detik
            }
        }

        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                autoSlideInterval = null;
            }
        }

        function markUserInteraction() {
            isUserInteracting = true;
            stopAutoSlide();

            if (userInteractionTimer) {
                clearTimeout(userInteractionTimer);
            }

            userInteractionTimer = setTimeout(() => {
                isUserInteracting = false;
                if (hasMedia && allMedia.length > 1 && !isVideoPlaying) {
                    startAutoSlide();
                }
            }, 10000); // 10 detik setelah interaksi
        }

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            // Format Rupiah
            document.querySelectorAll('.format-rupiah').forEach(element => {
                let text = element.textContent.trim();
                if (!text.startsWith('Rp ') && !text.includes('Rp')) {
                    element.textContent = 'Rp ' + text;
                }
            });

            if (hasMedia) {
                // Set total media counter
                const totalMediaElement = document.getElementById('totalMedia');
                if (totalMediaElement) {
                    totalMediaElement.textContent = allMedia.length;
                }

                // Initialize first media
                updateThumbnails();
                displayMedia(allMedia[0]);

                // Start auto-slide if multiple media and first media is not video
                if (allMedia.length > 1 && allMedia[0].type !== 'video') {
                    setTimeout(() => startAutoSlide(), 3000);
                }

                // Touch/swipe support
                const galleryContainer = document.getElementById('galleryContainer');
                if (galleryContainer && allMedia.length > 1) {
                    let touchStartX = 0;

                    galleryContainer.addEventListener('touchstart', function(e) {
                        touchStartX = e.touches[0].clientX;
                        markUserInteraction();
                    });

                    galleryContainer.addEventListener('touchend', function(e) {
                        const touchEndX = e.changedTouches[0].clientX;
                        const swipeDistance = touchStartX - touchEndX;

                        if (Math.abs(swipeDistance) > 50) {
                            if (swipeDistance > 0) {
                                nextMedia();
                            } else {
                                prevMedia();
                            }
                        }
                        markUserInteraction();
                    });

                    // Click untuk next media (kecuali jika sedang video)
                    galleryContainer.addEventListener('click', function(e) {
                        // Cek apakah klik bukan pada tombol play video
                        const isPlayButton = e.target.closest('.fa-play') ||
                            e.target.closest('[onclick*="playYouTubeVideo"]');

                        if (!isPlayButton && !isVideoPlaying) {
                            const currentMedia = allMedia[currentMediaIndex];
                            if (currentMedia.type !== 'video') {
                                nextMedia();
                            }
                        }
                    });
                }
            }

            // Copy address function
            window.copyAddress = function() {
                const address = `{{ addslashes($property->property_address) }}`;
                navigator.clipboard.writeText(address).then(() => {
                    showToast('Alamat berhasil disalin!', 'success');
                }).catch(err => {
                    console.error('Gagal menyalin alamat:', err);
                    showToast('Gagal menyalin alamat. Silakan salin manual.', 'error');
                });
            };

            // Visibility change handling
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoSlide();
                } else if (hasMedia && allMedia.length > 1 && !isUserInteracting && !isVideoPlaying) {
                    // Check if current media is not video before starting auto-slide
                    const currentMedia = allMedia[currentMediaIndex];
                    if (currentMedia.type !== 'video') {
                        startAutoSlide();
                    }
                }
            });

            // Pause auto-slide ketika tab/window tidak aktif
            window.addEventListener('blur', function() {
                stopAutoSlide();
            });

            window.addEventListener('focus', function() {
                if (hasMedia && allMedia.length > 1 && !isUserInteracting && !isVideoPlaying) {
                    const currentMedia = allMedia[currentMediaIndex];
                    if (currentMedia.type !== 'video') {
                        startAutoSlide();
                    }
                }
            });
        });

        // Helper functions
        function isYouTubeUrl(url) {
            if (!url) return false;
            return url.match(/youtu\.be\/([a-zA-Z0-9_-]+)|youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/);
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white font-medium transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
    </script>
</body>

</html>
