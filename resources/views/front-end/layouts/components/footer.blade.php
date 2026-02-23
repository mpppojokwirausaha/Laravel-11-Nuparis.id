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
                        <span>Jl. Jendral Sudirman No.Kel, Nagri Kaler, Kec. Purwakarta, Kabupaten Purwakarta,
                            Jawa Barat 41115</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <span>markom@nuparis.id</span>
                    </li>
                </ul>
            </div>

            <div class="animate-slide-in-right">
                <h4 class="text-white font-bold text-lg mb-6">Mitra & Institusi</h4>
                <div class="grid grid-cols-1 gap-4">
                    <a href="https://mpp.purwakartakab.go.id/instances/pojok-wirausaha/konsultasi-pra-perizinan-dan-non-perizinan"
                        class="bg-slate-800 p-2 rounded-lg flex items-center justify-center hover:bg-slate-700 transition cursor-pointer hover:-translate-y-0.5">
                        <span class="text-xs font-bold text-center text-slate-400">Madukara</span>
                    </a>
                </div>
            </div>
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

<!-- Privacy Policy Modal -->
<div id="privacyModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop - TERPISAH, klik di sini saja yang menutup modal -->
    <div id="privacyBackdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 md:p-6">
        <!-- Modal Content - KLIK DI SINI TIDAK MENUTUP MODAL -->
        <div
            class="relative w-full max-w-7xl min-h-[95vh] md:min-h-[90vh] transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all flex flex-col">

            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-4 sm:px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-primary to-red-500">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Kebijakan Privasi</h3>
                            <p class="text-sm text-gray-500">
                                NUPARIS.ID | Support Your Company Goals
                            </p>
                        </div>
                    </div>
                    <button onclick="closePrivacyModal()" type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content Container -->
            <div class="bg-white flex-1 flex flex-col min-h-0">
                <!-- Meta Info - Dengan null handling -->
                <div class="border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
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

                <!-- Body - Scrollable dengan null handling -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    @php
                        $hasPrivacyPolicy = isset($infos) && $infos && !empty($infos->privacy_policy);
                    @endphp

                    @if ($hasPrivacyPolicy)
                        <div class="text-gray-700">
                            {!! html_entity_decode($infos->privacy_policy) !!}
                        </div>
                    @else
                        <!-- Empty State dengan null handling -->
                        <div class="flex flex-col items-center justify-center py-12 h-full min-h-[50vh]">
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
                                                Variabel $infos tidak tersedia atau bernilai null.
                                                Pastikan data dikirim dari controller.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
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

<!-- Terms & Conditions Modal -->
<div id="termsModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop - TERPISAH, klik di sini saja yang menutup modal -->
    <div id="termsBackdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 md:p-6">
        <!-- Modal Content - KLIK DI SINI TIDAK MENUTUP MODAL -->
        <div
            class="relative w-full max-w-7xl min-h-[95vh] md:min-h-[90vh] transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all flex flex-col">

            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-4 sm:px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-primary to-red-500">
                            <i class="fas fa-file-contract text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Syarat & Ketentuan</h3>
                            <p class="text-sm text-gray-500">
                                NUPARIS.ID | Support Your Company Goals
                            </p>
                        </div>
                    </div>
                    <button onclick="closeTermsModal()" type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content Container -->
            <div class="bg-white flex-1 flex flex-col min-h-0">
                <!-- Meta Info - Dengan null handling -->
                <div class="border-b border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
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

                <!-- Body - Scrollable dengan null handling -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                    @php
                        $hasTermsConditions = isset($infos) && $infos && !empty($infos->terms_conditions);
                    @endphp

                    @if ($hasTermsConditions)
                        <div class="text-gray-700">
                            {!! html_entity_decode($infos->terms_conditions) !!}
                        </div>
                    @else
                        <!-- Empty State dengan null handling -->
                        <div class="flex flex-col items-center justify-center py-12 h-full min-h-[50vh]">
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
            </div>

            <!-- Footer -->
            <div
                class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-4 flex-shrink-0">
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

<script>
    // Modal Functions dengan validasi
    function openPrivacyModal() {
        const modal = document.getElementById('privacyModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            console.error('Privacy modal element not found');
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
        } else {
            console.error('Terms modal element not found');
        }
    }

    function closeTermsModal() {
        const modal = document.getElementById('termsModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Event Listeners dengan null checking - HANYA BACKDROP YANG MENUTUP MODAL
    document.addEventListener('DOMContentLoaded', function() {
        // Privacy Modal Backdrop
        const privacyBackdrop = document.getElementById('privacyBackdrop');
        if (privacyBackdrop) {
            privacyBackdrop.addEventListener('click', function() {
                closePrivacyModal();
            });
        }

        // Terms Modal Backdrop
        const termsBackdrop = document.getElementById('termsBackdrop');
        if (termsBackdrop) {
            termsBackdrop.addEventListener('click', function() {
                closeTermsModal();
            });
        }
    });

    // ESC key dengan validasi
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const privacyModal = document.getElementById('privacyModal');
            const termsModal = document.getElementById('termsModal');

            if (privacyModal && !privacyModal.classList.contains('hidden')) {
                closePrivacyModal();
            }
            if (termsModal && !termsModal.classList.contains('hidden')) {
                closeTermsModal();
            }
        }
    });
</script>
