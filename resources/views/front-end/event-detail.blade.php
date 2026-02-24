<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }} - NUPARIS.ID</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Tags -->
    <meta name="title" content="{{ $event->event_title }} - NUPARIS.ID" />
    <meta name="description"
        content="{{ \Illuminate\Support\Str::limit(strip_tags($event->event_description), 160) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="{{ $event->event_title }} - NUPARIS.ID" />
    <meta property="og:description"
        content="{{ \Illuminate\Support\Str::limit(strip_tags($event->event_description), 160) }}" />
    <meta property="og:image" content="{{ asset('storage/' . $event->event_image) }}" />
    <meta property="og:image:alt" content="{{ $event->event_title }}" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="{{ $event->event_title }} - NUPARIS.ID" />
    <meta property="twitter:description"
        content="{{ \Illuminate\Support\Str::limit(strip_tags($event->event_description), 160) }}" />
    <meta property="twitter:image" content="{{ asset('storage/' . $event->event_image) }}" />
    <meta property="twitter:image:alt" content="{{ $event->event_title }}" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans text-gray-800 bg-gray-50 min-h-screen">

    <!-- Desktop Navbar -->
    @include('front-end.layouts.components.header')

    <!-- Main Content -->
    <main class="pt-0 lg:pt-16">
        <!-- Event Hero Section -->
        <section class="bg-gradient-to-br from-red-50 to-white pt-6 pb-10 sm:pt-8 sm:pb-12">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb - Mobile lebih compact -->
                <div class="mb-4 sm:mb-6">
                    <nav class="flex items-center text-xs sm:text-sm text-gray-600">
                        <a href="{{ route('landingpage') }}"
                            class="hover:text-red-600 transition-colors duration-200 truncate max-w-[80px] sm:max-w-none">Beranda</a>
                        <i class="fas fa-chevron-right mx-1 sm:mx-2 text-xs"></i>
                        <a href="{{ route('event-more') }}"
                            class="hover:text-red-600 transition-colors duration-200 truncate max-w-[60px] sm:max-w-none">Event</a>
                        <i class="fas fa-chevron-right mx-1 sm:mx-2 text-xs"></i>
                        <span
                            class="text-gray-900 font-medium truncate max-w-[150px] sm:max-w-md md:max-w-lg">{{ $event->event_title }}</span>
                    </nav>
                </div>

                <!-- Hero Content - Layout berbeda untuk mobile/tablet -->
                <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6 sm:gap-8">
                    <!-- Left Column - Full width di mobile -->
                    <div class="lg:col-span-2">
                        <!-- Category & Status Badges -->
                        <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4 flex-wrap">
                            <span
                                class="px-2 py-1 sm:px-3 sm:py-1 bg-red-100 text-red-700 text-xs sm:text-sm font-medium rounded-full">
                                Event
                            </span>
                            @php
                                $now = now();
                                $start = \Carbon\Carbon::parse($event->event_date_start);
                                $end = \Carbon\Carbon::parse($event->event_date_end);

                                if ($now->lt($start)) {
                                    $label = 'Akan Datang';
                                    $class = 'bg-green-100 text-green-700';
                                } elseif ($now->between($start, $end)) {
                                    $label = 'Sedang Berlangsung';
                                    $class = 'bg-blue-100 text-blue-700';
                                } else {
                                    $label = 'Sudah Berakhir';
                                    $class = 'bg-red-100 text-red-700';
                                }
                            @endphp

                            <span
                                class="px-2 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-medium rounded-full {{ $class }}">
                                {{ $label }}
                            </span>
                        </div>

                        <!-- Title - Ukuran berbeda per device -->
                        <h1
                            class="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold text-gray-900 mb-3 sm:mb-4 leading-snug sm:leading-tight">
                            {{ $event->event_title }}
                        </h1>

                        <!-- Short Description -->
                        <p class="text-sm sm:text-base lg:text-lg text-gray-600 mb-4 sm:mb-6">
                            {{ \Illuminate\Support\Str::limit(strip_tags($event->event_description), 100) }}
                        </p>

                        <!-- Event Details Grid - Mobile: 2 kolom, Tablet: 4 kolom -->
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                            <!-- Date -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-red-600 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm text-gray-500 truncate">Tanggal</div>
                                    <div class="font-medium text-sm sm:text-base truncate">
                                        {{ \Carbon\Carbon::parse($event->event_date_start)->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Time -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-blue-600 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm text-gray-500 truncate">Waktu</div>
                                    <div class="font-medium text-sm sm:text-base truncate">
                                        {{ \Carbon\Carbon::parse($event->event_date_start)->translatedFormat('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($event->event_date_end)->translatedFormat('H:i') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Platform -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-video text-red-600 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm text-gray-500 truncate">Platform</div>
                                    <div class="font-medium text-sm sm:text-base truncate">
                                        {{ $event->event_location }}
                                    </div>
                                </div>
                            </div>

                            <!-- Quota -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <div
                                    class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-users text-purple-600 text-sm sm:text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs sm:text-sm text-gray-500 truncate">Kuota</div>
                                    <div class="font-medium text-sm sm:text-base truncate">Penuh</div>
                                </div>
                            </div>
                        </div>

                        <!-- Price Section - Mobile lebih compact -->
                        <div class="hidden lg:block mb-4 sm:mb-6">
                            @if ($event->event_price == 0)
                                <!-- Free Event -->
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                                    <div
                                        class="bg-red-100 text-red-600 px-4 py-1.5 sm:px-6 sm:py-2 text-sm sm:text-md font-bold rounded-lg flex items-center justify-center sm:justify-start w-full sm:w-auto">
                                        <i class="fas fa-gift mr-2"></i>
                                        GRATIS
                                    </div>
                                    <div class="text-red-600 font-medium text-sm sm:text-base text-center sm:text-left">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Tidak ada biaya apapun
                                    </div>
                                </div>
                            @else
                                <!-- Paid Event -->
                                <div class="flex items-end gap-2 sm:gap-3">
                                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                                        Rp {{ number_format($event->event_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- CTA Button - Mobile full width -->
                        <button onclick="showRegistrationModal()"
                            class="hidden lg:block w-full lg:w-1/4 bg-primary hover:bg-primary-hover text-white font-bold px-6 py-3 sm:px-8 sm:py-3.5 rounded-lg shadow-lg transition-colors duration-300 text-sm sm:text-base">
                            Gabung Sekarang
                        </button>
                    </div>

                    <!-- Right Column - Event Image Card - Di bawah di mobile -->
                    <div class="lg:col-span-1 order-first lg:order-last">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-red-200">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $event->event_image) }}"
                                    alt="{{ $event->event_title }}" class="w-full h-48 sm:h-56 md:h-64 object-cover">
                                @if ($event->event_price == 0)
                                    <div class="absolute top-3 right-3 sm:top-4 sm:right-4">
                                        <span
                                            class="bg-gradient-to-r from-red-600 to-red-700 text-white px-2 py-1 sm:px-3 sm:py-1 text-xs sm:text-sm font-bold rounded-full">
                                            GRATIS
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 sm:p-4">
                                <!-- Progress Bar -->
                                <div class="text-center mb-3 sm:mb-4">
                                    <div class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2">Kuota Terisi</div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2 mb-1 sm:mb-2">
                                        <div class="bg-red-600 h-1.5 sm:h-2 rounded-full" style="width: 100%"></div>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-600">
                                        20 peserta terdaftar • Sisa 0 kuota
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-1.5 sm:space-y-2">
                                    <button onclick="addToCalendar()"
                                        class="w-full border border-gray-300 hover:border-gray-400 text-gray-700 font-medium py-2 sm:py-2.5 rounded-lg transition-colors duration-300 text-xs sm:text-sm">
                                        <i class="far fa-calendar-plus mr-2"></i>
                                        Tambah ke Kalender
                                    </button>
                                    <button onclick="shareEvent()"
                                        class="w-full border border-gray-300 hover:border-gray-400 text-gray-700 font-medium py-2 sm:py-2.5 rounded-lg transition-colors duration-300 text-xs sm:text-sm flex items-center justify-center">
                                        <i class="fas fa-share-alt mr-2"></i>
                                        Bagikan Event
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Area -->
        <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Left Content -->
                <div class="lg:col-span-2">
                    <!-- Content Navigation - Mobile lebih compact -->
                    <div class="mb-6 sm:mb-8">
                        <div class="flex space-x-1 bg-gray-100 p-0.5 sm:p-1 rounded-lg overflow-x-auto">
                            <button onclick="switchTab('overview')" id="tab-overview"
                                class="tab-button flex-1 min-w-[100px] px-3 py-2 sm:px-4 sm:py-2 text-center font-medium rounded-md bg-white shadow-sm text-gray-900 text-xs sm:text-sm whitespace-nowrap">
                                Overview
                            </button>
                            <button onclick="switchTab('rundown')" id="tab-rundown"
                                class="tab-button flex-1 min-w-[100px] px-3 py-2 sm:px-4 sm:py-2 text-center font-medium rounded-md text-gray-600 hover:text-gray-900 transition-colors duration-300 text-xs sm:text-sm whitespace-nowrap">
                                Rundown
                            </button>
                            <button onclick="switchTab('mentor')" id="tab-mentor"
                                class="tab-button flex-1 min-w-[100px] px-3 py-2 sm:px-4 sm:py-2 text-center font-medium rounded-md text-gray-600 hover:text-gray-900 transition-colors duration-300 text-xs sm:text-sm whitespace-nowrap">
                                Mentor
                            </button>
                            <button onclick="switchTab('faq')" id="tab-faq"
                                class="tab-button flex-1 min-w-[100px] px-3 py-2 sm:px-4 sm:py-2 text-center font-medium rounded-md text-gray-600 hover:text-gray-900 transition-colors duration-300 text-xs sm:text-sm whitespace-nowrap">
                                FAQ
                            </button>
                        </div>
                    </div>

                    <!-- Overview Tab -->
                    <div id="overview-content" class="space-y-4 sm:space-y-6 lg:space-y-8">
                        <!-- Description -->
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Deskripsi</h2>
                            <div class="text-gray-600 space-y-3 sm:space-y-4 prose max-w-none text-sm sm:text-base">
                                {!! $event->event_description !!}
                            </div>
                        </div>

                        <!-- What You'll Learn -->
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Apa yang akan anda
                                dapatkan?</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                @foreach (['Strategi Branding Digital', 'Social Media Marketing', 'Content Creation', 'Performance Tracking'] as $benefit)
                                    <div class="flex items-start gap-2 sm:gap-3">
                                        <i
                                            class="fas fa-check-circle text-green-600 mt-0.5 sm:mt-1 flex-shrink-0 text-sm sm:text-base"></i>
                                        <div class="min-w-0">
                                            <h3 class="font-medium text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">
                                                {{ $benefit }}</h3>
                                            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">Membangun brand
                                                yang kuat di era digital</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Manfaat yang didapat
                            </h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                                @foreach ([['icon' => 'fas fa-certificate', 'title' => 'Sertifikat', 'desc' => 'Sertifikat resmi NUPARIS'], ['icon' => 'fas fa-book', 'title' => 'Materi Lengkap', 'desc' => 'Slide, template, dan tools'], ['icon' => 'fas fa-headset', 'title' => 'Konsultasi', 'desc' => 'Sesi konsultasi dengan mentor']] as $benefit)
                                    <div class="text-center">
                                        <div
                                            class="w-12 h-12 sm:w-16 sm:h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                            <i class="{{ $benefit['icon'] }} text-red-600 text-base sm:text-xl"></i>
                                        </div>
                                        <h3 class="font-medium text-gray-900 mb-0.5 sm:mb-1 text-sm sm:text-base">
                                            {{ $benefit['title'] }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-600">{{ $benefit['desc'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Rundown Tab (Hidden by default) -->
                    <div id="rundown-content" class="hidden space-y-4 sm:space-y-6">
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Rundown Kegiatan</h2>
                            <div class="space-y-3 sm:space-y-4">
                                @foreach ([['session' => 'Session 1: Digital Marketing Fundamentals', 'time' => '09:00 - 10:30 WIB', 'items' => ['Pengenalan digital marketing untuk UMKM', 'Analisis target market dan kompetitor', 'Membangun value proposition yang kuat']], ['session' => 'Session 2: Social Media Strategy', 'time' => '10:45 - 12:15 WIB', 'items' => ['Strategi content untuk Instagram & TikTok', 'Teknik engagement dan community building', 'Optimasi profil sosial media untuk konversi']], ['session' => 'Session 3: Content Creation Workshop', 'time' => '13:00 - 14:30 WIB', 'items' => ['Membuat konten video yang engaging', 'Copywriting untuk media sosial', 'Tools gratis untuk content creation']]] as $index => $session)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button onclick="toggleAccordion('session-{{ $index }}')"
                                            class="w-full text-left p-3 sm:p-4 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors duration-300">
                                            <div class="min-w-0 mr-2">
                                                <h3 class="font-medium text-gray-900 text-sm sm:text-base truncate">
                                                    {{ $session['session'] }}</h3>
                                                <p class="text-xs sm:text-sm text-gray-600 mt-0.5">
                                                    {{ $session['time'] }}</p>
                                            </div>
                                            <i id="icon-session-{{ $index }}"
                                                class="fas fa-chevron-down text-gray-400 transition-transform duration-300 text-sm sm:text-base flex-shrink-0"></i>
                                        </button>
                                        <div id="session-{{ $index }}"
                                            class="hidden p-3 sm:p-4 border-t border-gray-200">
                                            <ul class="space-y-1.5 sm:space-y-2">
                                                @foreach ($session['items'] as $item)
                                                    <li class="flex items-center gap-1.5 sm:gap-2">
                                                        <i
                                                            class="fas fa-circle text-[8px] sm:text-xs text-red-600 flex-shrink-0"></i>
                                                        <span class="text-xs sm:text-sm">{{ $item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Mentor Tab (Hidden by default) -->
                    <div id="mentor-content" class="hidden">
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Mentor Workshop</h2>
                            <div class="flex flex-col sm:grid sm:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                                @foreach ([['name' => 'Budi Santoso', 'role' => 'Digital Marketing Director', 'desc' => '15+ tahun pengalaman di digital marketing, konsultan Kemenkop UKM untuk program digitalisasi UMKM.', 'skills' => ['Facebook Ads', 'SEO', 'Analytics']], ['name' => 'Sari Wijaya', 'role' => 'Social Media Expert', 'desc' => 'Spesialis TikTok & Instagram Marketing, telah membantu 100+ UMKM meningkatkan penjualan melalui program pemerintah.', 'skills' => ['Instagram', 'TikTok', 'Content']]] as $mentor)
                                    <div class="flex gap-3 sm:gap-4">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0">
                                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80"
                                                alt="{{ $mentor['name'] }}"
                                                class="w-full h-full object-cover rounded-lg">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-gray-900 text-sm sm:text-base">
                                                {{ $mentor['name'] }}</h3>
                                            <p class="text-red-600 font-medium text-xs sm:text-sm mb-1 sm:mb-2">
                                                {{ $mentor['role'] }}</p>
                                            <p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-3 leading-relaxed">
                                                {{ $mentor['desc'] }}</p>
                                            <div class="flex gap-1.5 sm:gap-2 flex-wrap">
                                                @foreach ($mentor['skills'] as $skill)
                                                    <span
                                                        class="px-1.5 py-0.5 sm:px-2 sm:py-1 bg-red-50 text-red-700 text-xs rounded">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Tab (Hidden by default) -->
                    <div id="faq-content" class="hidden">
                        <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Pertanyaan yang Sering
                                Ditanyakan (FAQ)</h2>
                            <div class="space-y-3 sm:space-y-4">
                                @foreach ([['question' => 'Apakah workshop ini benar-benar gratis?', 'answer' => 'Ya, workshop ini 100% gratis tanpa biaya pendaftaran maupun biaya tersembunyi. Didukung oleh program CSR NUPARIS UKM RI.'], ['question' => 'Bagaimana cara mendapatkan sertifikat?', 'answer' => 'Sertifikat digital akan dikirim via email 1-2 hari kerja setelah workshop selesai kepada peserta yang mengikuti minimal 80% sesi.']] as $index => $faq)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button onclick="toggleFAQ('faq-{{ $index }}')"
                                            class="w-full text-left p-3 sm:p-4 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors duration-300">
                                            <h3 class="font-medium text-gray-900 text-sm sm:text-base mr-2">
                                                {{ $faq['question'] }}</h3>
                                            <i id="icon-faq-{{ $index }}"
                                                class="fas fa-chevron-down text-gray-400 transition-transform duration-300 text-sm sm:text-base flex-shrink-0"></i>
                                        </button>
                                        <div id="faq-{{ $index }}"
                                            class="hidden p-3 sm:p-4 border-t border-gray-200">
                                            <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                                                {{ $faq['answer'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar - Registration Card -->
                <div class="lg:col-span-1 order-first lg:order-last">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 sticky top-20 lg:top-24">
                        <div class="p-4 sm:p-6 border-b border-gray-200">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Daftar Event</h3>

                            <!-- Price Display -->
                            <div class="mb-4 sm:mb-6">
                                @if ($event->event_price > 0)
                                    <div class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                                        Rp. {{ number_format($event->event_price, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div
                                        class="bg-red-100 text-red-600 text-lg sm:text-xl font-bold py-2 sm:py-3 px-3 sm:px-4 rounded-lg text-center mb-1 sm:mb-2">
                                        <i class="fas fa-gift mr-2"></i>
                                        GRATIS
                                    </div>
                                    <div class="text-center text-red-600 font-medium text-sm sm:text-base">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Tidak ada biaya apapun
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Registration Form -->
                        <div class="p-4 sm:p-6">
                            <div class="space-y-3 sm:space-y-4">
                                <!-- Order Summary -->
                                <div class="border-t border-gray-200 pt-3 sm:pt-4">
                                    <div class="flex justify-between items-center mb-1 sm:mb-2">
                                        <span class="text-gray-600 truncate text-xs sm:text-sm">1x ticket</span>
                                        <span class="font-medium text-xs sm:text-sm">
                                            @if ($event->event_price > 0)
                                                Rp. {{ number_format($event->event_price, 0, ',', '.') }}
                                            @else
                                                GRATIS
                                            @endif
                                        </span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center text-base sm:text-lg font-bold pt-2 border-t border-gray-100">
                                        <span class="text-sm sm:text-base">Total Pembayaran</span>
                                        <span class="text-red-600 text-sm sm:text-base">
                                            @if ($event->event_price > 0)
                                                Rp. {{ number_format($event->event_price, 0, ',', '.') }}
                                            @else
                                                GRATIS
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Register Button -->
                                <button onclick="showRegistrationModal()"
                                    class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3 sm:py-3.5 rounded-lg shadow-lg transition-colors duration-300 mt-4 sm:mt-6 flex items-center justify-center text-sm sm:text-base">
                                    <i class="fas fa-lock mr-2"></i>
                                    @if ($event->event_price > 0)
                                        Lanjutkan ke Pembayaran
                                    @else
                                        Daftar Sekarang
                                    @endif
                                </button>

                                <!-- Terms -->
                                <p class="text-xs text-gray-500 text-center mt-3 sm:mt-4 leading-relaxed">
                                    Dengan mendaftar, Anda menyetujui
                                    <a href="#" class="text-red-600 hover:underline">Syarat & Ketentuan</a>
                                    dan
                                    <a href="#" class="text-red-600 hover:underline">Kebijakan Privasi</a>
                                </p>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="bg-gray-50 p-3 sm:p-4 rounded-b-xl">
                            <div class="text-xs sm:text-sm text-gray-600">
                                <div class="flex items-center gap-1.5 sm:gap-2 mb-1.5 sm:mb-2">
                                    <i class="fas fa-headset text-gray-400 text-xs sm:text-sm"></i>
                                    <span class="font-medium">Butuh bantuan? Hubungi kami:</span>
                                </div>
                                <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                                    <i class="fas fa-phone text-gray-400 text-xs"></i>
                                    <span class="font-medium text-xs sm:text-sm">{{ env('NO_WHATSAPP', '-') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 sm:gap-2">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                    <span class="text-xs sm:text-sm">info@nuparis.id</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.bottom-bar')
    @include('front-end.layouts.components.footer')

    <!-- Toast Notification (Hidden by default) -->
    <div id="toast" class="fixed bottom-20 lg:bottom-4 right-4 z-50 hidden animate-slide-up">
        <div class="bg-green-500 text-white px-3 py-2 sm:px-4 sm:py-3 rounded-lg shadow-lg flex items-center max-w-xs">
            <i class="fas fa-check-circle mr-2 text-sm sm:text-base"></i>
            <span id="toast-message" class="text-xs sm:text-sm"></span>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('[id$="-content"]').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
                button.classList.add('text-gray-600');
            });

            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Activate selected tab button
            document.getElementById('tab-' + tabName).classList.add('bg-white', 'shadow-sm', 'text-gray-900');
            document.getElementById('tab-' + tabName).classList.remove('text-gray-600');
        }

        // Accordion toggle for session details
        function toggleAccordion(sessionId) {
            const content = document.getElementById(sessionId);
            const icon = document.getElementById('icon-' + sessionId);

            content.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        // FAQ toggle
        function toggleFAQ(faqId) {
            const content = document.getElementById(faqId);
            const icon = document.getElementById('icon-' + faqId);

            content.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        // Add to calendar function
        function addToCalendar() {
            @php
                $safeTitle = addslashes($event->event_title);
                $safeDesc = addslashes(strip_tags($event->event_description));
                $safeDesc = substr($safeDesc, 0, 100);
            @endphp

            const eventData = {
                title: '{{ $safeTitle }} - NUPARIS.ID',
                description: '{{ $safeDesc }}',
                startDate: '{{ \Carbon\Carbon::parse($event->event_date_start)->format('Ymd') }}T{{ \Carbon\Carbon::parse($event->event_date_start)->format('His') }}',
                endDate: '{{ \Carbon\Carbon::parse($event->event_date_end)->format('Ymd') }}T{{ \Carbon\Carbon::parse($event->event_date_end)->format('His') }}'
            };

            // Buat URL tanpa location dulu
            let calendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE';
            calendarUrl += '&text=' + encodeURIComponent(eventData.title);
            calendarUrl += '&details=' + encodeURIComponent(eventData.description);
            calendarUrl += '&dates=' + eventData.startDate + '/' + eventData.endDate;

            window.open(calendarUrl, '_blank');

            if (typeof showToast === 'function') {
                showToast('Event ditambahkan ke kalender');
            } else {
                alert('Event ditambahkan ke kalender (showToast tidak ditemukan)');
            }
        }

        // Share event function
        async function shareEvent() {
            const shareData = {
                title: '{{ $event->event_title }}',
                text: '{{ \Illuminate\Support\Str::limit(strip_tags($event->event_description), 100) }}',
                url: window.location.href
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                    showToast('Event berhasil dibagikan!');
                } else {
                    // Fallback for browsers that don't support Web Share API
                    await navigator.clipboard.writeText(window.location.href);
                    showToast('Link event telah disalin ke clipboard!');
                }
            } catch (err) {
                console.log('Error sharing:', err);
                // Don't show error for user cancellation
                if (err.name !== 'AbortError') {
                    await navigator.clipboard.writeText(window.location.href);
                    showToast('Link event telah disalin ke clipboard!');
                }
            }
        }

        // Show toast notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');

            toastMessage.textContent = message;
            toast.classList.remove('hidden');

            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Registration modal - Responsive untuk mobile
        function showRegistrationModal() {
            const isFreeEvent = {{ $event->event_price }} <= 0;

            const modalHTML = `
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4 animate-fade-in" id="registrationModal">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[95vh] sm:max-h-[90vh] flex flex-col">
            <!-- Fixed Header -->
            <div class="flex-shrink-0 border-b border-gray-200">
                <div class="p-4 sm:p-6 md:p-8 pb-2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">Formulir Pendaftaran</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">
                            <i class="fas fa-times text-xl sm:text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6 md:p-8">
                    <!-- Grid Container -->
                    <div class="flex flex-col lg:grid lg:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
                        <!-- Left Column - Data Diri -->
                        <div class="space-y-4 sm:space-y-6">
                            <h4 class="text-base sm:text-lg font-bold text-gray-900">Data Diri Peserta</h4>
                            
                            <!-- Name -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Nama Lengkap *
                                </label>
                                <input type="text" 
                                    id="participantName"
                                    placeholder="Masukkan nama lengkap"
                                    class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-300"
                                    required>
                                <p class="text-xs text-gray-500 mt-1 sm:mt-2">
                                    Nama yang akan tercantum di sertifikat
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Email *
                                </label>
                                <input type="email" 
                                    id="participantEmail"
                                    placeholder="nama@email.com"
                                    class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-300"
                                    required>
                                <p class="text-xs text-gray-500 mt-1 sm:mt-2">
                                    E-sertifikat dan materi akan dikirim ke email ini
                                </p>
                            </div>

                            <!-- WhatsApp -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Nomor WhatsApp *
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 sm:px-4 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 rounded-l-lg text-xs sm:text-sm">
                                        +62
                                    </span>
                                    <input type="tel" 
                                        id="participantWhatsApp"
                                        placeholder="812-3456-7890"
                                        class="flex-1 border border-gray-300 rounded-r-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-300"
                                        required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 sm:mt-2">
                                    Konfirmasi dan link Zoom akan dikirim ke nomor ini
                                </p>
                            </div>

                            <!-- Company/Business (Optional) -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Nama Perusahaan/Bisnis
                                    <span class="text-gray-400 text-xs font-normal">(Opsional)</span>
                                </label>
                                <input type="text" 
                                    id="companyName"
                                    placeholder="Nama perusahaan atau bisnis Anda"
                                    class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-300">
                            </div>

                            <!-- Additional Questions -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Dari mana Anda mengetahui event ini?
                                </label>
                                <select id="sourceInfo"
                                    class="w-full border border-gray-300 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors duration-300">
                                    <option value="">Pilih salah satu</option>
                                    <option>Instagram</option>
                                    <option>Facebook</option>
                                    <option>Email Newsletter</option>
                                    <option>WhatsApp Group</option>
                                    <option>Rekomendasi Teman</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column - Order Summary -->
                        <div class="space-y-4 sm:space-y-6">
                            <h4 class="text-base sm:text-lg font-bold text-gray-900">Ringkasan Pendaftaran</h4>
                            
                            <div class="bg-gray-50 rounded-xl p-4 sm:p-6">
                                <!-- Event Details -->
                                <div class="mb-4 sm:mb-6">
                                    <h5 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">{{ $event->event_title }}</h5>
                                    <div class="space-y-1 text-xs sm:text-sm text-gray-600">
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <i class="fas fa-calendar-alt text-gray-400 text-xs sm:text-sm"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->event_date_start)->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <i class="fas fa-clock text-gray-400 text-xs sm:text-sm"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->event_date_start)->translatedFormat('H:i') }} - {{ \Carbon\Carbon::parse($event->event_date_end)->translatedFormat('H:i') }} WIB</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <i class="fas fa-video text-gray-400 text-xs sm:text-sm"></i>
                                            <span>{{ $event->event_location }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 sm:space-y-4">
                            ${isFreeEvent ? `
                                <!-- Free Event Display -->
                                <div class="flex justify-between items-center pb-3 sm:pb-4 border-b border-gray-200">
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm sm:text-base">Tiket Gratis</div>
                                        <div class="text-xs sm:text-sm text-gray-600">1 x Tiket</div>
                                    </div>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">Rp 0</div>
                                </div>
                            ` : `
                                <!-- Paid Event Display -->
                                <div class="flex justify-between items-center pb-3 sm:pb-4 border-b border-gray-200">
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm sm:text-base">Tiket</div>
                                        <div class="text-xs sm:text-sm text-gray-600">1 x Tiket</div>
                                    </div>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">Rp {{ number_format($event->event_price, 0, ',', '.') }}</div>
                                </div>
                                `}

                            <!-- Benefits Included -->
                            <div class="mb-4 sm:mb-6">
                                <h5 class="font-bold text-gray-900 mb-1.5 sm:mb-2 text-sm sm:text-base">Yang Anda dapatkan:</h5>
                                <div class="space-y-1.5 sm:space-y-2">
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <i class="fas fa-check text-green-600 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm">Sertifikat digital</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <i class="fas fa-check text-green-600 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm">Materi workshop lengkap (PDF)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <i class="fas fa-check text-green-600 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm">Akses recording sesi</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <i class="fas fa-check text-green-600 text-xs sm:text-sm"></i>
                                        <span class="text-xs sm:text-sm">Template digital marketing</span>
                                    </div>
                                </div>
                            </div>

                            ${isFreeEvent ? `
                                <!-- Free Price Display -->
                                <div class="pt-3 sm:pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-bold text-gray-900 text-base sm:text-lg">Total Pembayaran</div>
                                            <div class="text-xs sm:text-sm text-gray-600">Workshop ini gratis sepenuhnya</div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-primary text-2xl sm:text-3xl text-right">GRATIS</div>
                                            <div class="text-xs sm:text-sm text-gray-500 text-right">Didukung oleh NUPARIS</div>
                                        </div>
                                    </div>
                                </div>
                            ` : `
                                <!-- Total for Paid Event -->
                                <div class="pt-3 sm:pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-bold text-gray-900 text-base sm:text-lg">Total Pembayaran</div>
                                            <div class="text-xs sm:text-sm text-gray-600">Sudah termasuk sertifikat</div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-primary text-xl sm:text-2xl">Rp {{ number_format($event->event_price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            `}
                                </div>
                            </div>

                            <!-- Important Notice -->
                            <div class="p-3 sm:p-4 bg-red-50 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-primary rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas fa-exclamation text-white text-[10px] sm:text-xs"></i>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-700">
                                        <p class="font-medium mb-0.5 sm:mb-1">Penting!</p>
                                        <p>Link Zoom Meeting akan dikirim ke email dan WhatsApp Anda 1 hari sebelum workshop berlangsung. Pastikan data yang Anda isi benar.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3 sm:gap-4 pt-3 sm:pt-4">
                                <button onclick="${isFreeEvent ? 'claimFreeTicket()' : 'processPayment()'}"
                                    class="flex-1 bg-primary text-white font-bold py-3 sm:py-4 rounded-xl shadow-lg transition-colors duration-300 text-sm sm:text-base lg:text-lg flex items-center justify-center">
                                    <i class="${isFreeEvent ? 'fas fa-gift' : 'fas fa-shopping-cart'} mr-2"></i>
                                    ${isFreeEvent ? 'Claim Tiket' : 'Bayar Sekarang'}
                                </button>
                            </div>

                            <!-- Security Info -->
                            <div class="text-center pt-3 sm:pt-4">
                                <div class="flex items-center justify-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-500">
                                    <i class="fas fa-lock text-green-500 text-xs sm:text-sm"></i>
                                    <span>Data Anda aman dan terenkripsi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;

            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Close modal when clicking outside
            document.getElementById('registrationModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        // Close modal function
        function closeModal() {
            const modal = document.getElementById('registrationModal');
            if (modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => modal.remove(), 300);
            }
        }

        // Submit free registration
        function claimFreeTicket() {
            const name = document.getElementById('participantName').value;
            const email = document.getElementById('participantEmail').value;
            const whatsapp = document.getElementById('participantWhatsApp').value;
            const company = document.getElementById('companyName')?.value || '';
            const source = document.getElementById('sourceInfo')?.value || '';

            if (!name || !email || !whatsapp) {
                showToast('Harap lengkapi semua data yang diperlukan (bertanda *)');
                return;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Format email tidak valid');
                return;
            }

            // Validate phone number
            const phoneRegex = /^[0-9]{9,13}$/;
            const phoneNumber = whatsapp.replace(/\D/g, '');
            if (!phoneRegex.test(phoneNumber)) {
                showToast('Format nomor WhatsApp tidak valid. Harap masukkan 9-13 digit angka.');
                return;
            }

            // Show success message
            closeModal();

            const successHTML = `
                <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4 animate-fade-in" id="successModal">
                    <div class="bg-white rounded-xl max-w-sm sm:max-w-md w-full p-4 sm:p-6 text-center">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <i class="fas fa-check-circle text-green-600 text-xl sm:text-2xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1.5 sm:mb-2">Tiket Berhasil Diklaim!</h3>
                        <div class="mb-4 sm:mb-6 text-gray-600 text-sm sm:text-base">
                            <p class="mb-1 sm:mb-2">Selamat <span class="font-bold">${name}</span>!</p>
                            <p>Anda telah berhasil mendaftar workshop gratis ini.</p>
                        </div>
                        <div class="bg-red-50 p-3 sm:p-4 rounded-lg mb-4 sm:mb-6">
                            <h4 class="font-bold text-gray-900 mb-1 sm:mb-2 text-sm sm:text-base">Info Penting:</h4>
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">📧 Konfirmasi dikirim ke: ${email}</p>
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">📱 Link Zoom dikirim via WhatsApp ke: +62${phoneNumber}</p>
                            <p class="text-xs sm:text-sm text-gray-600">⏰ Link akan dikirim 24 jam sebelum workshop</p>
                        </div>
                        <button onclick="closeSuccessModal()"
                            class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 sm:py-3 rounded-lg transition-colors duration-300 text-sm sm:text-base">
                            Tutup
                        </button>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', successHTML);
        }

        // Close success modal
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            if (modal) {
                modal.classList.add('opacity-0');
                setTimeout(() => modal.remove(), 300);
            }
        }

        // Process payment (for paid events)
        function processPayment() {
            const name = document.getElementById('participantName').value;
            const email = document.getElementById('participantEmail').value;
            const whatsapp = document.getElementById('participantWhatsApp').value;

            if (!name || !email || !whatsapp) {
                showToast('Harap lengkapi semua data yang diperlukan (bertanda *)');
                return;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Format email tidak valid');
                return;
            }

            // Validate phone number
            const phoneRegex = /^[0-9]{9,13}$/;
            const phoneNumber = whatsapp.replace(/\D/g, '');
            if (!phoneRegex.test(phoneNumber)) {
                showToast('Format nomor WhatsApp tidak valid. Harap masukkan 9-13 digit angka.');
                return;
            }

            // This would redirect to payment gateway
            showToast('Mengarahkan ke halaman pembayaran...');
            closeModal();

            // In a real app, you would redirect to payment page
            // window.location.href = '/payment/process';
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set first tab as active
            switchTab('overview');

            // Add padding to bottom for mobile bottom bar
            if (window.innerWidth < 1024) {
                document.body.style.paddingBottom = '64px';
            }
        });
    </script>
</body>

</html>
