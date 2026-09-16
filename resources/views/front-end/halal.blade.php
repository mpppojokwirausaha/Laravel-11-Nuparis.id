<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title ?? 'Form Registrasi Sertifikat Halal' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta name="title" content="Form Registrasi Sertifikat Halal" />
    <meta name="description" content="Ajukan sertifikasi halal untuk produk usaha Anda melalui NUPARIS." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Form Registrasi Sertifikat Halal" />
    <meta property="og:description" content="Ajukan sertifikasi halal untuk produk usaha Anda melalui NUPARIS." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:site_name" content="NUPARIS" />

    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans bg-slate-50 text-gray-800 min-h-screen">

    @include('front-end.layouts.components.header')

    <main class="pt-8 lg:pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="w-full max-w-6xl mx-auto space-y-4">

                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 shadow-sm">
                    <p class="text-sm text-amber-800 mb-2">
                        <span class="font-semibold">Catatan Penting:</span> Jenis pengajuan sertifikasi halal
                        ditentukan oleh bahan pokok produk Anda:
                    </p>
                    <ul class="text-sm text-amber-800 list-disc list-inside space-y-1.5">
                        <li>
                            <span class="font-semibold">Self Declare (GRATIS)</span> — untuk produk dengan bahan
                            pokok <span class="font-semibold">bukan daging</span>.
                            Topping atau tambahan berbahan daging masih diperbolehkan.
                        </li>
                        <li>
                            <span class="font-semibold">Reguler (BERBAYAR)</span> — untuk produk dengan bahan pokok
                            <span class="font-semibold">berbahan daging</span>.
                        </li>
                    </ul>
                    <p class="text-xs text-amber-700 mt-2">
                        Pastikan Anda memilih jenis yang sesuai agar proses pengajuan berjalan lancar.
                    </p>
                </div>

                <div class="bg-white shadow-lg rounded-xl w-full p-6 sm:p-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">FORM REGISTRASI SERTIFIKAT HALAL</h1>
                    <p class="text-gray-500 text-sm mb-6">Lengkapi data usaha dan produk Anda untuk proses pengajuan
                        sertifikasi halal.</p>

                    <form id="produkForm" action="{{ route('halal.store') }}" method="POST" class="space-y-4"
                        novalidate>
                        @csrf

                        <div>
                            <label for="nama_pelaku" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Pelaku Usaha <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_pelaku" name="nama_pelaku" required minlength="3" autofocus
                                value="{{ old('nama_pelaku') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Masukkan nama lengkap pelaku usaha">
                            <p class="text-xs text-gray-400 mt-1">Nama lengkap sesuai KTP.</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Nama Pelaku Usaha wajib diisi
                                (minimal 3 karakter).</p>
                            @error('nama_pelaku')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nama_brand" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Usaha / Merk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_brand" name="nama_brand" required minlength="3"
                                value="{{ old('nama_brand') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Masukkan nama usaha / merk">
                            <p class="text-xs text-gray-400 mt-1">Masukkan nama usaha atau merk Anda.</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Nama Usaha / Merk wajib diisi
                                (minimal 3 karakter).</p>
                            @error('nama_brand')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="nik_ktp" class="block text-sm font-medium text-gray-700 mb-1">
                                    NIK KTP <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="nik_ktp" name="nik_ktp" required
                                    minlength="16" maxlength="16" pattern="[0-9]{16}" value="{{ old('nik_ktp') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="16 digit NIK KTP" title="NIK KTP harus berupa angka, 16 digit">
                                <p class="text-xs text-gray-400 mt-1">Sesuai KTP, 16 digit angka.</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">NIK KTP wajib diisi (16 digit
                                    angka).</p>
                                @error('nik_ktp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nib" class="block text-sm font-medium text-gray-700 mb-1">
                                    No. NIB <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="nib" name="nib" required
                                    minlength="9" maxlength="16" pattern="[0-9]{9,16}" value="{{ old('nib') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nomor Induk Berusaha" title="No. NIB harus berupa angka">
                                <p class="text-xs text-gray-400 mt-1">Nomor Induk Berusaha (OSS).</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">No. NIB wajib diisi.</p>
                                @error('nib')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="npwp" class="block text-sm font-medium text-gray-700 mb-1">
                                    NPWP <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="npwp" name="npwp" required
                                    maxlength="16" pattern="[0-9]{1,16}" value="{{ old('npwp') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Nomor NPWP">
                                <p class="text-xs text-gray-400 mt-1">Nomor Pokok Wajib Pajak.</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">NPWP wajib diisi.</p>
                                @error('npwp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="modal_awal" class="block text-sm font-medium text-gray-700 mb-1">
                                    Modal Awal <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="modal_awal" name="modal_awal" required
                                    value="{{ old('modal_awal') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: 5.000.000">
                                <p class="text-xs text-gray-400 mt-1">Perkiraan modal awal usaha (Rupiah). Titik
                                    pemisah ribuan otomatis muncul saat mengetik.</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">Modal Awal wajib diisi.</p>
                                @error('modal_awal')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="tahun_berdiri" class="block text-sm font-medium text-gray-700 mb-1">
                                    Bulan/Tahun Berdiri Usaha <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="tahun_berdiri" name="tahun_berdiri" required
                                    value="{{ old('tahun_berdiri') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: Agustus 2016">
                                <p class="text-xs text-gray-400 mt-1">Bulan dan tahun usaha mulai berjalan.</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">Bulan/Tahun Berdiri Usaha wajib
                                    diisi.</p>
                                @error('tahun_berdiri')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="luas_usaha" class="block text-sm font-medium text-gray-700 mb-1">
                                    Luas Usaha <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="luas_usaha" name="luas_usaha" required
                                    value="{{ old('luas_usaha') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: 20 m2 / Tidak menetap">
                                <p class="text-xs text-gray-400 mt-1">Boleh berupa ukuran (mis. 20 m2) atau
                                    keterangan (mis. tidak menetap, menumpang, dsb).</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">Luas Usaha wajib diisi.</p>
                                @error('luas_usaha')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="pendapatan_minggu" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pendapatan / Minggu <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="pendapatan_minggu"
                                    name="pendapatan_minggu" required value="{{ old('pendapatan_minggu') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Contoh: 1.500.000">
                                <p class="text-xs text-gray-400 mt-1">Perkiraan pendapatan per minggu (Rupiah).
                                    Titik pemisah ribuan otomatis muncul saat mengetik.</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">Pendapatan / Minggu wajib
                                    diisi.</p>
                                @error('pendapatan_minggu')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">
                                    No. Hp / Wa Aktif <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" inputmode="numeric" id="no_whatsapp" name="no_whatsapp"
                                    required minlength="9" maxlength="16" pattern="[0-9]{9,16}"
                                    value="{{ old('no_whatsapp') }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="081234567890" title="No. Hp/Wa harus berupa angka, 9-16 digit">
                                <p class="text-xs text-gray-400 mt-1">9–16 digit. Awalan "0" akan otomatis diubah
                                    jadi "62".</p>
                                <p class="error-msg text-xs text-red-500 mt-1 hidden">No. Hp / Wa Aktif wajib diisi
                                    (9-16 digit angka).</p>
                                @error('no_whatsapp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                value="{{ old('email') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="nama@email.com">
                            <p class="text-xs text-gray-400 mt-1">Contoh: nama@email.com</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Masukkan alamat email yang valid.
                            </p>
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Pelaku Usaha <span class="text-red-500">*</span>
                            </label>
                            <textarea id="alamat" name="alamat" rows="2" required minlength="5"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Alamat lengkap tempat tinggal / usaha">{{ old('alamat') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Alamat lengkap pelaku usaha.</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Alamat Pelaku Usaha wajib diisi.
                            </p>
                            @error('alamat')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="bahan" class="block text-sm font-medium text-gray-700">
                                    Bahan-bahan <span class="text-red-500">*</span>
                                </label>
                                <button type="button" data-expand-target="bahan" data-expand-title="Bahan-bahan"
                                    class="expand-btn flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded-md transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <polyline points="9 21 3 21 3 15"></polyline>
                                        <line x1="21" y1="3" x2="14" y2="10"></line>
                                        <line x1="3" y1="21" x2="10" y2="14"></line>
                                    </svg>
                                    Perbesar
                                </button>
                            </div>
                            <textarea id="bahan" name="bahan" rows="4" required minlength="5"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="- Tepung terigu&#10;- Gula pasir&#10;- Telur&#10;- Minyak goreng">{{ old('bahan') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Tulis satu bahan per baris.</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Bahan-bahan makanan wajib diisi.
                            </p>
                            @error('bahan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="cara_pembuatan" class="block text-sm font-medium text-gray-700">
                                    Cara Pembuatan Produk <span class="text-red-500">*</span>
                                </label>
                                <button type="button" data-expand-target="cara_pembuatan"
                                    data-expand-title="Cara Pembuatan Produk"
                                    class="expand-btn flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded-md transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <polyline points="9 21 3 21 3 15"></polyline>
                                        <line x1="21" y1="3" x2="14" y2="10"></line>
                                        <line x1="3" y1="21" x2="10" y2="14"></line>
                                    </svg>
                                    Perbesar
                                </button>
                            </div>
                            <textarea id="cara_pembuatan" name="cara_pembuatan" rows="4" required minlength="10"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Jelaskan dengan detail langkah langkah pembuatan produk">{{ old('cara_pembuatan') }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Jelaskan proses produksi secara ringkas dan
                                berurutan.</p>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Cara pembuatan wajib diisi
                                (minimal 10 karakter).</p>
                            @error('cara_pembuatan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-1">
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" id="konfirmasi" name="konfirmasi" required
                                    class="mt-1 accent-blue-600">
                                <span class="text-sm text-gray-700">
                                    Saya menyatakan bahwa data yang saya isi di atas sudah
                                    <span class="font-medium">benar</span> dan dapat dipertanggungjawabkan.
                                </span>
                            </label>
                            <p class="error-msg text-xs text-red-500 mt-1 hidden">Anda harus mencentang pernyataan
                                ini sebelum mengajukan.</p>
                            @error('konfirmasi')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" id="submitBtn" disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-600 flex items-center justify-center gap-2">
                            <svg id="submitSpinner" class="hidden animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span id="submitBtnText">Ajukan Sertifikat Halal</span>
                        </button>

                        <p id="statusMsg" class="text-sm text-center mt-2 hidden"></p>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <div id="expandModalOverlay"
        class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl h-full sm:h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-4 sm:px-5 py-3 border-b border-gray-200 shrink-0">
                <h2 id="expandModalTitle" class="text-base sm:text-lg font-semibold text-gray-800">Perbesar</h2>
                <button type="button" id="expandModalClose"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition"
                    aria-label="Tutup">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="flex-1 p-4 sm:p-5 overflow-hidden">
                <textarea id="expandModalTextarea"
                    class="w-full h-full resize-none border border-gray-300 rounded-lg px-3 py-2 text-sm sm:text-base leading-relaxed focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 px-4 sm:px-5 py-3 border-t border-gray-200 shrink-0">
                <button type="button" id="expandModalCancel"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    Batal
                </button>
                <button type="button" id="expandModalSave"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.footer')
    @include('front-end.layouts.components.bottom-bar')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const SUBMIT_THROTTLE_SECONDS = {{ (int) ($submitThrottleSeconds ?? 10) }};

            const form = document.getElementById('produkForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitSpinner = document.getElementById('submitSpinner');
            const submitBtnText = document.getElementById('submitBtnText');
            const nikInput = document.getElementById('nik_ktp');
            const nibInput = document.getElementById('nib');
            const npwpInput = document.getElementById('npwp');
            const modalInput = document.getElementById('modal_awal');
            const pendapatanInput = document.getElementById('pendapatan_minggu');
            const hpInput = document.getElementById('no_whatsapp');

            [nikInput, nibInput, npwpInput, hpInput].forEach(function(input) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            function formatRibuan(input) {
                input.addEventListener('input', function() {
                    let angka = this.value.replace(/[^0-9]/g, '');
                    if (angka === '') {
                        this.value = '';
                        return;
                    }
                    this.value = new Intl.NumberFormat('id-ID').format(Number(angka));
                });
            }
            formatRibuan(modalInput);
            formatRibuan(pendapatanInput);

            hpInput.addEventListener('blur', function() {
                let val = this.value;
                if (val.startsWith('0')) {
                    val = '62' + val.slice(1);
                }
                this.value = val;
            });

            function updateSubmitState() {
                submitBtn.disabled = !form.checkValidity();
            }
            form.addEventListener('input', updateSubmitState);
            form.addEventListener('change', updateSubmitState);
            updateSubmitState();

            function setFieldValidity(el, container) {
                const errorMsg = container.querySelector('.error-msg');
                if (!el.checkValidity()) {
                    el.classList.add('border-red-400', 'ring-1', 'ring-red-300');
                    if (errorMsg) errorMsg.classList.remove('hidden');
                    return false;
                } else {
                    el.classList.remove('border-red-400', 'ring-1', 'ring-red-300');
                    if (errorMsg) errorMsg.classList.add('hidden');
                    return true;
                }
            }

            document.querySelectorAll('#produkForm input[required], #produkForm textarea[required]').forEach(
                function(el) {
                    if (el.type === 'radio') return;
                    const container = el.closest('div');
                    el.addEventListener('blur', function() {
                        setFieldValidity(el, container);
                    });
                    el.addEventListener('input', function() {
                        setFieldValidity(el, container);
                    });
                });

            const expandOverlay = document.getElementById('expandModalOverlay');
            const expandTitle = document.getElementById('expandModalTitle');
            const expandTextarea = document.getElementById('expandModalTextarea');
            const expandClose = document.getElementById('expandModalClose');
            const expandCancel = document.getElementById('expandModalCancel');
            const expandSave = document.getElementById('expandModalSave');
            let expandCurrentField = null;

            function openExpandModal(fieldId, title) {
                const target = document.getElementById(fieldId);
                if (!target) return;
                expandCurrentField = target;
                expandTitle.textContent = title || 'Perbesar';
                expandTextarea.value = target.value;
                expandTextarea.placeholder = target.placeholder || '';
                expandOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                setTimeout(function() {
                    expandTextarea.focus();
                    expandTextarea.setSelectionRange(expandTextarea.value.length, expandTextarea.value
                        .length);
                }, 0);
            }

            function closeExpandModal() {
                expandOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                expandCurrentField = null;
            }

            function saveExpandModal() {
                if (!expandCurrentField) {
                    closeExpandModal();
                    return;
                }
                expandCurrentField.value = expandTextarea.value;
                expandCurrentField.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                expandCurrentField.dispatchEvent(new Event('blur', {
                    bubbles: true
                }));
                closeExpandModal();
            }

            document.querySelectorAll('.expand-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    openExpandModal(btn.getAttribute('data-expand-target'), btn.getAttribute(
                        'data-expand-title'));
                });
            });

            expandSave.addEventListener('click', saveExpandModal);
            expandCancel.addEventListener('click', closeExpandModal);
            expandClose.addEventListener('click', closeExpandModal);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !expandOverlay.classList.contains('hidden')) {
                    closeExpandModal();
                }
            });

            function startCountdown(seconds, label) {
                let countdown = seconds;
                submitSpinner.classList.add('hidden');
                submitBtnText.textContent = `${label} ${countdown} detik...`;

                const countdownInterval = setInterval(function() {
                    countdown--;
                    if (countdown > 0) {
                        submitBtnText.textContent = `${label} ${countdown} detik...`;
                    } else {
                        clearInterval(countdownInterval);
                        submitBtn.disabled = false;
                        submitBtnText.textContent = "Ajukan Sertifikat Halal";
                        updateSubmitState();
                    }
                }, 1000);
            }

            function resetButtonState() {
                submitBtn.disabled = false;
                submitSpinner.classList.add('hidden');
                submitBtnText.textContent = "Ajukan Sertifikat Halal";
                updateSubmitState();
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isValid = true;
                document.querySelectorAll('#produkForm input[required], #produkForm textarea[required]')
                    .forEach(function(el) {
                        if (el.type === 'radio') return;
                        const container = el.closest('div');
                        if (!setFieldValidity(el, container)) isValid = false;
                    });

                if (!isValid) {
                    Swal.fire({
                        icon: "warning",
                        title: "Data belum lengkap",
                        text: "Mohon lengkapi semua kolom yang wajib diisi (*) dengan benar.",
                        confirmButtonColor: "#2563eb",
                        timer: 5000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    const firstInvalid = document.querySelector('#produkForm .border-red-400');
                    if (firstInvalid) firstInvalid.focus();
                    return;
                }

                let hpValue = hpInput.value;
                if (hpValue.startsWith('0')) {
                    hpInput.value = '62' + hpValue.slice(1);
                }

                submitBtn.disabled = true;
                submitSpinner.classList.remove('hidden');
                submitBtnText.textContent = "Mengajukan...";

                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(async function(response) {
                        const data = await response.json().catch(() => null);

                        if (response.status === 429) {
                            Swal.fire({
                                icon: "error",
                                title: "Terlalu banyak percobaan",
                                text: "Mohon tunggu beberapa detik sebelum mencoba lagi.",
                                confirmButtonColor: "#2563eb",
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            startCountdown(SUBMIT_THROTTLE_SECONDS,
                                "Terlalu banyak percobaan, tunggu");
                            return;
                        }

                        if (response.status === 422) {
                            Swal.fire({
                                icon: "error",
                                title: "Terjadi kesalahan",
                                text: "Mohon periksa kembali data yang Anda masukkan.",
                                confirmButtonColor: "#2563eb",
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            resetButtonState();
                            return;
                        }

                        if (response.ok && data && data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: data.message,
                                confirmButtonColor: "#2563eb",
                                timer: 5000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            form.reset();
                            document.querySelectorAll('#produkForm .border-red-400').forEach(
                                function(el) {
                                    el.classList.remove('border-red-400', 'ring-1',
                                        'ring-red-300');
                                });
                            document.querySelectorAll('#produkForm .error-msg').forEach(function(
                                el) {
                                el.classList.add('hidden');
                            });
                            startCountdown(SUBMIT_THROTTLE_SECONDS,
                                "Anda dapat mengajukan lagi dalam");
                            return;
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Terjadi kesalahan",
                            text: "Silakan coba lagi.",
                            confirmButtonColor: "#2563eb",
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        resetButtonState();
                    })
                    .catch(function() {
                        Swal.fire({
                            icon: "error",
                            title: "Koneksi bermasalah",
                            text: "Periksa koneksi internet Anda dan coba lagi.",
                            confirmButtonColor: "#2563eb",
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                        resetButtonState();
                    });
            });
        });
    </script>

</body>

</html>
