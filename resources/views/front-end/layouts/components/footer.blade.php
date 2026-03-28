<footer class="bg-slate-900 text-slate-300 pt-16 pb-8">
    <div class="max-w-[1920px] mx-auto px-4 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center">
                        <img src="{{ asset('storage/' . ($infos->meta_image ?? '')) }}" alt="NUPARIS Logo" class="w-6 h-6">
                    </div>
                    <span class="font-bold text-2xl text-white tracking-tight">NUPARIS.ID</span>
                </div>
                <p class="text-sm leading-relaxed">NUPARIS.ID | Support Your Company Goals</p>
                <div class="flex space-x-4 pt-2">
                    <a href="https://www.linkedin.com/company/nuparis/" target="_blank" rel="noopener noreferrer"
                        class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition hover:-translate-y-0.5">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://www.facebook.com/nuparis.id/"
                        class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition hover:-translate-y-0.5">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/nuparis.id/"
                        class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition hover:-translate-y-0.5">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com/@nuparis"
                        class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition hover:-translate-y-0.5">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <div class="animate-fade-in-up">
                <h4 class="text-white font-bold text-lg mb-6">Tautan Cepat</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('landingpage') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Beranda
                        </a></li>
                    <li><a href="{{ route('news-more') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Berita
                        </a></li>
                    <li><a href="{{ route('article-more') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Perizinan & Non Perizinan
                        </a></li>
                    <li><a href="{{ route('activity-more') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Activitas
                        </a></li>
                    <li><a href="{{ route('event-more') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Event
                        </a></li>
                    <li><a href="{{ route('property-more') }}"
                            class="hover:text-primary transition flex items-center gap-2 hover:translate-x-1">
                            <i class="fas fa-chevron-right text-xs"></i> Properti
                        </a></li>
                </ul>
            </div>

            <div class="animate-fade-in-up">
                <h4 class="text-white font-bold text-lg mb-6">Hubungi Kami</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                        <span>{{ $infos->address }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <a href="mailto:{{ $infos->email }}">
                            <span>{{ $infos->email }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            @if ($agencies_footer->count() > 0)
                <div class="animate-slide-in-right">
                    <h4 class="text-white font-bold text-lg mb-6">Mitra & Institusi</h4>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($agencies_footer as $index => $item)
                            <a href="{{ $item->partner_url }}"
                                class="bg-slate-800 p-2 rounded-lg flex items-center justify-center hover:bg-slate-700 transition cursor-pointer hover:-translate-y-0.5 {{ $loop->last && $loop->count % 2 != 0 ? 'col-span-2' : '' }}">
                                <span class="text-xs font-bold text-center text-slate-400">
                                    {{ \Illuminate\Support\Str::limit($item->partner_name, 18) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-slate-500">&copy; 2026 NUPARIS Purwakarta. Hak Cipta Dilindungi.</p>
            <div class="flex gap-6 text-sm text-slate-500">
                <button onclick="openPrivacyModal()"
                    class="hover:text-white transition-colors hover:-translate-y-0.5 transform">
                    Kebijakan Privasi
                </button>
                <button onclick="openTermsModal()"
                    class="hover:text-white transition-colors hover:-translate-y-0.5 transform">
                    Syarat & Ketentuan
                </button>
            </div>
        </div>
    </div>
</footer>

<!-- STYLE BODY KONTEN - TANPA BIRU -->
<style>
    /* Style body konten - tanpa border biru */
    .modal-body-content {
        color: #374151;
        line-height: 1.7;
        font-size: 0.95rem;
        padding: 1.5rem 2rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    /* Judul utama - Kebijakan Privasi */
    .modal-body-content h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 0.75rem;
    }

    /* Subtitle - NUPARIS.ID | Support Your Company Goals */
    .modal-body-content .company-subtitle {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 0 0 1.5rem 0;
        font-weight: 400;
    }

    /* Meta info - mengikuti style meta di modal */
    .modal-body-content .meta-info {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        background-color: #f9fafb;
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        margin: 1rem 0 2rem 0;
        border: 1px solid #e5e7eb;
        font-size: 0.85rem;
    }

    .modal-body-content .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4b5563;
    }

    .modal-body-content .meta-item i {
        color: #3b82f6;
        width: 1rem;
    }

    .modal-body-content .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background-color: #dcfce7;
        color: #166534;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Heading 2 - Pendahuluan, Informasi yang Dikumpulkan - TANPA BIRU */
    .modal-body-content h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin: 2rem 0 1rem 0;
        /* BIRU DIHAPUS */
        padding-left: 0;
    }

    /* Heading 3 */
    .modal-body-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin: 1.5rem 0 0.75rem 0;
    }

    /* Heading 4 */
    .modal-body-content h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin: 1.25rem 0 0.5rem 0;
    }

    /* Paragraph */
    .modal-body-content p {
        margin: 1rem 0;
        color: #374151;
    }

    /* List - TANPA BIRU */
    .modal-body-content ul {
        margin: 1rem 0;
        padding-left: 1.5rem;
        list-style-type: disc;
    }

    .modal-body-content ol {
        margin: 1rem 0;
        padding-left: 1.5rem;
        list-style-type: decimal;
    }

    .modal-body-content li {
        margin: 0.5rem 0;
        color: #374151;
    }

    .modal-body-content li strong {
        color: #111827;
        font-weight: 600;
    }

    /* Nested lists */
    .modal-body-content ul ul {
        list-style-type: circle;
        margin: 0.5rem 0 0.5rem 1rem;
    }

    .modal-body-content ul ul ul {
        list-style-type: square;
    }

    /* Link */
    .modal-body-content a {
        color: #dc2626;
        text-decoration: none;
        border-bottom: 1px dotted #dc2626;
    }

    .modal-body-content a:hover {
        color: #dc2626;
        border-bottom: 1px solid #dc2626;
    }

    /* Blockquote */
    .modal-body-content blockquote {
        margin: 1.5rem 0;
        padding: 1rem 1.5rem;
        background-color: #f9fafb;
        border-left: 4px solid #9ca3af;
        /* Diubah jadi abu-abu */
        border-radius: 0 0.5rem 0.5rem 0;
        font-style: italic;
        color: #4b5563;
    }

    /* Table */
    .modal-body-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .modal-body-content th {
        background-color: #f9fafb;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #1f2937;
        border: 1px solid #e5e7eb;
    }

    .modal-body-content td {
        padding: 0.75rem 1rem;
        border: 1px solid #e5e7eb;
        color: #374151;
    }

    .modal-body-content tr:nth-child(even) {
        background-color: #f9fafb;
    }

    /* Image */
    .modal-body-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }

    /* Code */
    .modal-body-content code {
        background-color: #f3f4f6;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.9em;
        color: #dc2626;
    }

    .modal-body-content pre {
        background-color: #1f2937;
        color: #e5e7eb;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        font-family: monospace;
        margin: 1.5rem 0;
    }

    .modal-body-content pre code {
        background-color: transparent;
        color: #e5e7eb;
        padding: 0;
    }

    /* Horizontal rule */
    .modal-body-content hr {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 2rem 0;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .modal-body-content {
            padding: 1rem 1.25rem;
        }

        .modal-body-content h1 {
            font-size: 1.5rem;
        }

        .modal-body-content h2 {
            font-size: 1.3rem;
        }

        .modal-body-content .meta-info {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>

<!-- Privacy Policy Modal - TETAP SEPERTI ASLINYA -->
<div id="privacyModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div id="privacyBackdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container dengan scroll -->
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-2 sm:p-4 md:p-6">
            <!-- Modal Content -->
            <div
                class="relative w-full max-w-7xl max-h-[90vh] transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all flex flex-col">

                <!-- Header -->
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-primary to-red-500">
                                <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Kebijakan Privasi</h3>
                                <p class="text-sm text-gray-500">NUPARIS.ID | Support Your Company Goals</p>
                            </div>
                        </div>
                        <button onclick="closePrivacyModal()" type="button"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="sticky top-[73px] z-10 border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <span class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-alt text-primary mr-2"></i>
                            Terakhir diperbarui:
                            @if (isset($infos) && $infos && $infos->updated_at)
                                {{ $infos->updated_at->translatedFormat('d F Y') }}
                            @else
                                <span class="text-gray-400 italic">Belum diperbarui</span>
                            @endif
                        </span>
                        <span class="flex items-center text-gray-600">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            Versi:
                            @if (isset($infos) && $infos && $infos->privacy_policy_version)
                                {{ $infos->privacy_policy_version }}
                            @else
                                <span class="text-gray-400 italic">1.0</span>
                            @endif
                        </span>
                        @if (isset($infos) && $infos && $infos->privacy_policy)
                            <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>
                                Tersedia
                            </span>
                        @else
                            <span class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Draft
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Body - Scrollable dengan style yang TANPA BIRU -->
                <div class="flex-1 overflow-y-auto">
                    @php
                        $hasPrivacyPolicy = isset($infos) && $infos && !empty($infos->privacy_policy);
                    @endphp

                    @if ($hasPrivacyPolicy)
                        <!-- BODY KONTEN TANPA BIRU -->
                        <div class="modal-body-content">
                            {!! html_entity_decode($infos->privacy_policy) !!}
                        </div>
                    @else
                        <!-- Empty State - TETAP SEPERTI ASLINYA -->
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="relative">
                                <div
                                    class="flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-gray-50 to-gray-100 shadow-lg">
                                    <i class="fas fa-shield-alt text-5xl text-gray-300"></i>
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-2 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-white">
                                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                </div>
                            </div>

                            <h4 class="mt-8 text-2xl font-semibold text-gray-800">Kebijakan Privasi Belum Tersedia</h4>

                            <p class="mt-4 max-w-lg text-center text-gray-500 leading-relaxed">
                                @if (isset($infos) && $infos)
                                    @if ($infos->privacy_policy === null)
                                        Dokumen kebijakan privasi belum diupload oleh administrator.
                                    @elseif($infos->privacy_policy === '')
                                        Konten kebijakan privasi masih kosong.
                                    @else
                                        Saat ini halaman kebijakan privasi masih dalam proses penyusunan.
                                    @endif
                                @else
                                    Data informasi perusahaan belum tersedia. Silakan hubungi administrator.
                                @endif
                            </p>

                            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                                <div
                                    class="flex items-center gap-2 text-xs bg-gray-100 px-4 py-2.5 rounded-full text-gray-600">
                                    <i class="fas fa-database text-gray-400"></i>
                                    Status Data:
                                    <span class="font-semibold">
                                        @if (!isset($infos))
                                            <span class="text-red-600">Tidak Ada Data</span>
                                        @elseif(!$infos)
                                            <span class="text-red-600">Data Kosong</span>
                                        @elseif(empty($infos->privacy_policy))
                                            <span class="text-yellow-600">Konten Kosong</span>
                                        @else
                                            <span class="text-green-600">Tersedia</span>
                                        @endif
                                    </span>
                                </div>

                                <button onclick="closePrivacyModal()"
                                    class="flex items-center gap-2 bg-primary/10 text-primary px-5 py-2.5 rounded-full text-sm font-medium hover:bg-primary hover:text-white transition-all">
                                    <i class="fas fa-times-circle"></i>
                                    Tutup Modal
                                </button>
                            </div>

                            @if (!isset($infos) || !$infos)
                                <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-lg max-w-lg">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                                        <div class="text-left">
                                            <h5 class="text-sm font-semibold text-red-800">Error: Data Tidak Ditemukan
                                            </h5>
                                            <p class="text-xs text-red-600 mt-1">
                                                Variabel $infos tidak tersedia atau bernilai null. Pastikan data dikirim
                                                dari controller.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 z-10 border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2 text-primary"></i>
                            <span>
                                @if ($hasPrivacyPolicy)
                                    Kebijakan privasi NUPARIS
                                @else
                                    Kebijakan privasi sedang dalam pengembangan
                                @endif
                            </span>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="closePrivacyModal()"
                                class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition shadow-sm hover:shadow-md">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms & Conditions Modal - TETAP SEPERTI ASLINYA -->
<div id="termsModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div id="termsBackdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container dengan scroll -->
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-2 sm:p-4 md:p-6">
            <!-- Modal Content -->
            <div
                class="relative w-full max-w-7xl max-h-[90vh] transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all flex flex-col">

                <!-- Header -->
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-primary to-red-500">
                                <i class="fas fa-file-contract text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Syarat & Ketentuan</h3>
                                <p class="text-sm text-gray-500">NUPARIS.ID | Support Your Company Goals</p>
                            </div>
                        </div>
                        <button onclick="closeTermsModal()" type="button"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Meta Info -->
                <div
                    class="sticky top-[73px] z-10 border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <span class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-alt text-primary mr-2"></i>
                            Terakhir diperbarui:
                            @if (isset($infos) && $infos && $infos->updated_at)
                                {{ $infos->updated_at->translatedFormat('d F Y') }}
                            @else
                                <span class="text-gray-400 italic">Belum diperbarui</span>
                            @endif
                        </span>
                        <span class="flex items-center text-gray-600">
                            <i class="fas fa-file-signature text-primary mr-2"></i>
                            Berlaku sejak:
                            @if (isset($infos) && $infos && $infos->terms_valid_from)
                                {{ \Carbon\Carbon::parse($infos->terms_valid_from)->translatedFormat('d F Y') }}
                            @else
                                <span class="text-gray-400 italic">Belum ditetapkan</span>
                            @endif
                        </span>
                        <span class="flex items-center text-gray-600">
                            <i class="fas fa-tag text-primary mr-2"></i>
                            Versi:
                            @if (isset($infos) && $infos && $infos->terms_version)
                                {{ $infos->terms_version }}
                            @else
                                <span class="text-gray-400 italic">1.0</span>
                            @endif
                        </span>
                        @if (isset($infos) && $infos && $infos->terms_conditions)
                            <span class="flex items-center text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="flex items-center text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Draft
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Body - Scrollable dengan style yang TANPA BIRU -->
                <div class="flex-1 overflow-y-auto">
                    @php
                        $hasTermsConditions = isset($infos) && $infos && !empty($infos->terms_conditions);
                    @endphp

                    @if ($hasTermsConditions)
                        <!-- BODY KONTEN TANPA BIRU -->
                        <div class="modal-body-content">
                            {!! $infos->terms_conditions !!}
                        </div>
                    @else
                        <!-- Empty State - TETAP SEPERTI ASLINYA -->
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="relative">
                                <div
                                    class="flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-gray-50 to-gray-100 shadow-lg">
                                    <i class="fas fa-file-contract text-5xl text-gray-300"></i>
                                </div>
                                <div
                                    class="absolute -bottom-2 -right-2 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-white">
                                    <i class="fas fa-pen text-yellow-600 text-lg"></i>
                                </div>
                            </div>

                            <h4 class="mt-8 text-2xl font-semibold text-gray-800">Syarat & Ketentuan Belum Tersedia
                            </h4>

                            <p class="mt-4 max-w-lg text-center text-gray-500 leading-relaxed">
                                @if (isset($infos) && $infos)
                                    @if ($infos->terms_conditions === null)
                                        Dokumen syarat dan ketentuan belum diupload oleh administrator.
                                    @elseif($infos->terms_conditions === '')
                                        Konten syarat dan ketentuan masih kosong.
                                    @else
                                        Saat ini halaman syarat dan ketentuan masih dalam proses penyusunan oleh tim
                                        legal.
                                    @endif
                                @else
                                    Data informasi perusahaan belum tersedia. Silakan hubungi administrator untuk
                                    mengatur data.
                                @endif
                            </p>

                            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                                <div
                                    class="flex items-center gap-2 text-xs bg-gray-100 px-4 py-2.5 rounded-full text-gray-600">
                                    <i class="fas fa-legal text-gray-400"></i>
                                    Status Legal:
                                    <span class="font-semibold">
                                        @if (!isset($infos))
                                            <span class="text-red-600">No Data</span>
                                        @elseif(!$infos)
                                            <span class="text-red-600">Invalid</span>
                                        @elseif(empty($infos->terms_conditions))
                                            <span class="text-yellow-600">Draft</span>
                                        @else
                                            <span class="text-green-600">Published</span>
                                        @endif
                                    </span>
                                </div>

                                @if (isset($infos) && $infos && $infos->legal_contact)
                                    <a href="mailto:{{ $infos->legal_contact }}"
                                        class="flex items-center gap-2 bg-primary/10 text-primary px-5 py-2.5 rounded-full text-sm font-medium hover:bg-primary hover:text-white transition-all">
                                        <i class="fas fa-envelope"></i>
                                        Hubungi Legal
                                    </a>
                                @endif

                                <button onclick="closeTermsModal()"
                                    class="flex items-center gap-2 bg-gray-200 text-gray-700 px-5 py-2.5 rounded-full text-sm font-medium hover:bg-gray-300 transition-all">
                                    <i class="fas fa-times-circle"></i>
                                    Tutup
                                </button>
                            </div>

                            @if (!isset($infos) || !$infos)
                                <div class="mt-6 p-5 bg-orange-50 border border-orange-200 rounded-xl max-w-lg">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <i class="fas fa-tools text-orange-600"></i>
                                            </div>
                                        </div>
                                        <div class="text-left">
                                            <h5 class="text-sm font-bold text-orange-800">Konfigurasi Data</h5>
                                            <p class="text-xs text-orange-700 mt-1.5 leading-relaxed">
                                                Variabel $infos tidak terdefinisi. Pastikan controller mengirimkan data
                                                dengan <span
                                                    class="font-mono bg-orange-200 px-1.5 py-0.5 rounded">compact('infos')</span>
                                                atau <span
                                                    class="font-mono bg-orange-200 px-1.5 py-0.5 rounded">with(['infos'
                                                    => $infos])</span>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($infos) && $infos && !$infos->terms_conditions && $infos->legal_status === 'pending')
                                <div class="mt-4 text-xs text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-hourglass-half"></i>
                                    Estimasi selesai: Q2 2026
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 z-10 border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-gavel mr-2 text-primary"></i>
                            <span>
                                @if ($hasTermsConditions)
                                    Syarat & ketentuan berlaku untuk semua layanan NUPARIS
                                @else
                                    Dokumen legal sedang dalam proses review
                                @endif
                            </span>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="closeTermsModal()"
                                class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition shadow-sm hover:shadow-md">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script>
    function openPrivacyModal() {
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closePrivacyModal() {
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openTermsModal() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeTermsModal() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const privacyBackdrop = document.getElementById('privacyBackdrop');
        if (privacyBackdrop) {
            privacyBackdrop.addEventListener('click', closePrivacyModal);
        }

        const termsBackdrop = document.getElementById('termsBackdrop');
        if (termsBackdrop) {
            termsBackdrop.addEventListener('click', closeTermsModal);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePrivacyModal();
            closeTermsModal();
        }
    });
</script>
