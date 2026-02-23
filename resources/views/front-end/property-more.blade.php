<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Tags for Property Page -->
    <meta name="title" content="Daftar Properti Terbaik - NUPARIS.ID" />
    <meta name="description"
        content="Temukan properti terbaik dengan harga terjangkau. Hunian nyaman, fasilitas lengkap, dan lokasi strategis untuk keluarga Anda." />
    <meta name="keywords"
        content="properti, rumah, apartemen, tanah, investasi properti, hunian, perumahan, lokasi strategis" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Daftar Properti Terbaik - NUPARIS.ID" />
    <meta property="og:description"
        content="Kumpulan properti terbaik dengan fasilitas lengkap dan lokasi strategis untuk investasi atau hunian keluarga." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Properti NUPARIS" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="NUPARIS.ID" />
    <meta property="article:section" content="Properti" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="Daftar Properti Terbaik - NUPARIS.ID" />
    <meta property="twitter:description"
        content="Temukan properti terbaik dengan harga terjangkau. Hunian nyaman, fasilitas lengkap, dan lokasi strategis untuk keluarga Anda." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Properti NUPARIS" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="Daftar Properti Terbaik - NUPARIS.ID" />
    <meta property="linkedin:description"
        content="Kumpulan properti terbaik dengan fasilitas lengkap dan lokasi strategis untuk investasi atau hunian keluarga." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Properti NUPARIS" />
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans text-gray-800 bg-gray-50 h-full overflow-x-hidden">

    <!-- Desktop Navbar -->
    @include('front-end.layouts.components.header')

    <!-- Content Wrapper -->
    <div class="h-full overflow-y-auto pt-0 lg:pt-0">
        <!-- Header Hero Section -->
        <section class="bg-gradient-to-r from-red-600 to-red-700 text-white pt-6 pb-12 lg:pt-24 lg:pb-16">
            <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="flex-1">
                        <nav class="flex items-center text-sm text-red-100 mb-2">
                            <a href="{{ route('landingpage') }}"
                                class="lg:hover:text-white transition-colors">Beranda</a>
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span>Properti</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-3">Properti</h1>
                        <p class="text-red-100 max-w-2xl">Temukan properti dengan dual-purpose terbaik. Harga
                            terjangkau, fasilitas lengkap, lokasi strategis. Sempurna sebagai pondasi keluarga yang
                            nyaman atau fondasi bisnis yang menguntungkan. Pilih sesuai tujuan Anda.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4 lg:mt-0">
                        <div class="text-center">
                            <div id="totalPropertyCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Total Properti</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Search and Filter Section -->
            <section class="mb-10">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Search Bar -->
                    <div class="flex items-center justify-between p-4">
                        <div class="flex-1 flex items-center">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput"
                                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-red-600 outline-none transition text-sm"
                                    placeholder="Cari properti berdasarkan nama atau lokasi...">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button id="clearSearch"
                                        class="text-gray-400 lg:hover:text-gray-600 hidden transition-colors">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Filter and Sort Buttons -->
                            <div class="flex ml-3 gap-2">
                                <button id="filterButton"
                                    class="flex items-center justify-center gap-2 bg-gray-100 lg:hover:bg-gray-200 text-gray-700 font-medium px-3 py-2.5 rounded-lg transition-colors text-sm">
                                    <i class="fas fa-filter"></i>
                                    <span class="hidden sm:inline">Filter</span>
                                </button>
                                <button id="sortButton"
                                    class="flex items-center justify-center gap-2 bg-gray-100 lg:hover:bg-gray-200 text-gray-700 font-medium px-3 py-2.5 rounded-lg transition-colors text-sm">
                                    <i class="fas fa-sort-amount-down"></i>
                                    <span class="hidden sm:inline">Urutkan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel (Hidden by default) -->
                <div id="filterPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Filter Harga -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Rentang Harga</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua Harga</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="low" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">≤ Rp 500 juta</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="medium" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Rp 500 jt - 1 M</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="high" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">≥ Rp 1 M</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter Luas -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Luas Tanah</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="landArea" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua Luas</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="landArea" value="small" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">≤ 100 m²</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="landArea" value="medium" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">100 - 500 m²</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="landArea" value="large" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">≥ 500 m²</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button id="cancelFilter"
                            class="px-4 py-2 text-gray-600 lg:hover:text-gray-800 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button id="applyFilter"
                            class="px-4 py-2 bg-red-600 lg:hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Sort Panel (Hidden by default) -->
                <div id="sortPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sortBy" value="newest" class="mr-3 h-4 w-4" checked>
                            <div>
                                <span class="text-gray-800 text-sm font-medium">Terbaru</span>
                                <p class="text-gray-500 text-xs">Properti ditambahkan paling baru</p>
                            </div>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sortBy" value="price_low" class="mr-3 h-4 w-4">
                            <div>
                                <span class="text-gray-800 text-sm font-medium">Harga: Terendah ke Tertinggi</span>
                                <p class="text-gray-500 text-xs">Urutkan berdasarkan harga termurah</p>
                            </div>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sortBy" value="price_high" class="mr-3 h-4 w-4">
                            <div>
                                <span class="text-gray-800 text-sm font-medium">Harga: Tertinggi ke Terendah</span>
                                <p class="text-gray-500 text-xs">Urutkan berdasarkan harga termahal</p>
                            </div>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="sortBy" value="name" class="mr-3 h-4 w-4">
                            <div>
                                <span class="text-gray-800 text-sm font-medium">Nama (A-Z)</span>
                                <p class="text-gray-500 text-xs">Urutkan berdasarkan nama properti</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button id="cancelSort"
                            class="px-4 py-2 text-gray-600 lg:hover:text-gray-800 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button id="applySort"
                            class="px-4 py-2 bg-red-600 lg:hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Terapkan Urutan
                        </button>
                    </div>
                </div>
            </section>

            <!-- Property Section -->
            <section>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-red-600 pl-3">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Properti</h2>
                        <span id="propertyCount"
                            class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">
                            0 Properti
                        </span>
                    </div>
                </div>

                <!-- Property Container -->
                <div id="propertyContainer"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Properti akan dimuat via AJAX -->
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-red-600">
                    </div>
                    <p class="mt-4 text-gray-600">Memuat properti...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-home text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada properti yang ditemukan</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Coba ubah kata kunci pencarian atau filter yang Anda
                        gunakan.</p>
                    <button id="resetSearch"
                        class="bg-red-600 lg:hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                    </button>
                </div>

                <!-- PAGINATION SECTION -->
                <div id="paginationContainer" class="mt-12">
                    <!-- Pagination akan dimuat via JavaScript -->
                </div>
            </section>
        </main>

        <!-- Footer -->
        @include('front-end.layouts.components.bottom-bar')
        @include('front-end.layouts.components.footer')
    </div>
    @include('front-end.layouts.components.chat')

    <!-- Main jQuery Script -->
    <script>
        $(document).ready(function() {
            console.log('Document ready. Loading properties...');

            // State variables untuk client-side pagination
            let currentPage = 1;
            const itemsPerPage = 12;
            let totalPages = 1;
            let currentFilters = {
                priceRange: 'all',
                landArea: 'all',
                sortBy: 'newest',
                searchQuery: ''
            };
            let allProperties = [];
            let filteredProperties = [];

            // DOM Elements
            const $propertyContainer = $('#propertyContainer');
            const $loadingIndicator = $('#loadingIndicator');
            const $noResults = $('#noResults');
            const $propertyCount = $('#propertyCount');
            const $totalPropertyCount = $('#totalPropertyCount');
            const $searchInput = $('#searchInput');
            const $clearSearch = $('#clearSearch');
            const $resetSearch = $('#resetSearch');
            const $filterButton = $('#filterButton');
            const $filterPanel = $('#filterPanel');
            const $sortButton = $('#sortButton');
            const $sortPanel = $('#sortPanel');
            const $applyFilter = $('#applyFilter');
            const $cancelFilter = $('#cancelFilter');
            const $applySort = $('#applySort');
            const $cancelSort = $('#cancelSort');
            const $paginationContainer = $('#paginationContainer');

            // Initialize
            setupEventListeners();
            loadPropertiesFromController();

            // Fungsi untuk mendapatkan URL gambar yang aman
            function getSafeImageUrl(imageData) {
                const defaultImage =
                    'https://images.unsplash.com/photo-1568605114967-8130f3a36994?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';

                // Jika tidak ada data gambar
                if (!imageData) return defaultImage;

                // Cek jika imageData adalah string (mungkin path tunggal)
                if (typeof imageData === 'string') {
                    const imgStr = imageData.trim();

                    // Skip jika string kosong atau null-like
                    if (!imgStr || imgStr === 'null' || imgStr === 'undefined') {
                        return defaultImage;
                    }

                    // Cek jika ini gambar (bukan video)
                    const isImageFile = /\.(jpg|jpeg|png|gif|webp|bmp)(\?.*)?$/i.test(imgStr);

                    if (isImageFile) {
                        // Jika sudah URL lengkap
                        if (imgStr.startsWith('http://') || imgStr.startsWith('https://') || imgStr.startsWith(
                                '//')) {
                            return imgStr;
                        }

                        // Jika relative path, tambahkan storage path
                        const cleanPath = imgStr.replace(/^\//, '');
                        return `{{ asset('storage/') }}/${cleanPath}`;
                    }

                    return defaultImage;
                }

                // Jika imageData adalah array
                if (Array.isArray(imageData)) {
                    // Cari gambar pertama yang valid
                    for (let i = 0; i < imageData.length; i++) {
                        const media = imageData[i];

                        if (typeof media === 'string') {
                            const imgStr = media.trim();

                            // Skip jika string kosong
                            if (!imgStr || imgStr === 'null' || imgStr === 'undefined') continue;

                            // Cek jika ini gambar
                            const isImageFile = /\.(jpg|jpeg|png|gif|webp|bmp)(\?.*)?$/i.test(imgStr);

                            if (isImageFile) {
                                // Jika sudah URL lengkap
                                if (imgStr.startsWith('http://') || imgStr.startsWith('https://') || imgStr
                                    .startsWith('//')) {
                                    return imgStr;
                                }

                                // Jika relative path
                                const cleanPath = imgStr.replace(/^\//, '');
                                return `{{ asset('storage/') }}/${cleanPath}`;
                            }
                        }
                    }
                }

                return defaultImage;
            }

            function loadPropertiesFromController() {
                showLoading();
                console.log('Loading properties from controller...');

                $.ajax({
                    url: '{{ route('property-data') }}',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(result) {
                        console.log('AJAX success:', result);

                        if (result.success && result.propertyData && $.isArray(result.propertyData)) {
                            console.log('Properties loaded:', result.propertyData.length);
                            console.log('Sample property:', result.propertyData[0]);

                            allProperties = result.propertyData;
                            updatePropertyCounts(allProperties);
                            filterAndPaginate();
                        } else {
                            console.log('No properties found or invalid data structure');
                            console.log('Result keys:', Object.keys(result));
                            showNoResults();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading properties:', error);
                        console.log('Status:', status);
                        console.log('XHR:', xhr);
                        showNoResults();
                    }
                });
            }

            function updatePropertyCounts(properties) {
                const total = properties.length;
                $totalPropertyCount.text(total);
                $propertyCount.text(total + ' Properti');
                console.log('Total properties:', total);
            }

            function setupEventListeners() {
                console.log('Setting up event listeners...');

                // Search functionality
                $searchInput.on('input', function() {
                    if ($(this).val().trim() !== '') {
                        $clearSearch.removeClass('hidden');
                    } else {
                        $clearSearch.addClass('hidden');
                    }

                    clearTimeout($(this).data('searchTimeout'));
                    $(this).data('searchTimeout', setTimeout(function() {
                        currentFilters.searchQuery = $searchInput.val().trim();
                        currentPage = 1;
                        filterAndPaginate();
                    }, 500));
                });

                $searchInput.on('keypress', function(e) {
                    if (e.key === 'Enter') {
                        currentFilters.searchQuery = $(this).val().trim();
                        currentPage = 1;
                        filterAndPaginate();
                    }
                });

                // Clear search
                $clearSearch.on('click', function() {
                    $searchInput.val('');
                    currentFilters.searchQuery = '';
                    currentPage = 1;
                    $(this).addClass('hidden');
                    filterAndPaginate();
                });

                // Filter panel toggle
                $filterButton.on('click', function() {
                    $filterPanel.toggleClass('hidden');
                    $sortPanel.addClass('hidden');
                });

                // Sort panel toggle
                $sortButton.on('click', function() {
                    $sortPanel.toggleClass('hidden');
                    $filterPanel.addClass('hidden');
                });

                // Apply filter button
                $applyFilter.on('click', applyFilters);

                // Cancel filter button
                $cancelFilter.on('click', function() {
                    $filterPanel.addClass('hidden');
                });

                // Apply sort button
                $applySort.on('click', applySorting);

                // Cancel sort button
                $cancelSort.on('click', function() {
                    $sortPanel.addClass('hidden');
                });

                // Reset search button
                $resetSearch.on('click', function() {
                    clearAllFilters();
                    $searchInput.val('');
                    currentFilters.searchQuery = '';
                    $clearSearch.addClass('hidden');
                    filterAndPaginate();
                });

                // Close panels when clicking outside
                $(document).on('click', function(e) {
                    // Close filter panel
                    if (!$filterPanel.is(e.target) && $filterPanel.has(e.target).length === 0 && !
                        $filterButton.is(e.target) && $filterButton.has(e.target).length === 0) {
                        $filterPanel.addClass('hidden');
                    }

                    // Close sort panel
                    if (!$sortPanel.is(e.target) && $sortPanel.has(e.target).length === 0 && !$sortButton
                        .is(e.target) && $sortButton.has(e.target).length === 0) {
                        $sortPanel.addClass('hidden');
                    }
                });
            }

            function applyFilters() {
                currentFilters.priceRange = $('input[name="priceRange"]:checked').val();
                currentFilters.landArea = $('input[name="landArea"]:checked').val();
                $filterPanel.addClass('hidden');
                currentPage = 1;
                filterAndPaginate();
            }

            function applySorting() {
                currentFilters.sortBy = $('input[name="sortBy"]:checked').val();
                $sortPanel.addClass('hidden');
                currentPage = 1;
                filterAndPaginate();
            }

            function filterAndPaginate() {
                console.log('Filtering and paginating...');
                console.log('Current filters:', currentFilters);
                console.log('All properties:', allProperties.length);

                showLoading();

                setTimeout(function() {
                    filteredProperties = filterProperties();
                    console.log('Filtered properties:', filteredProperties.length);

                    updateFilteredCount(filteredProperties.length);

                    totalPages = Math.ceil(filteredProperties.length / itemsPerPage);

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    const paginatedProperties = getCurrentPageProperties(filteredProperties);
                    console.log('Paginated properties:', paginatedProperties.length);

                    renderProperties(paginatedProperties);
                    renderPagination();
                    hideLoading();

                }, 300);
            }

            function filterProperties() {
                let filtered = $.extend(true, [], allProperties);
                console.log('Starting with:', filtered.length, 'properties');

                // Filter berdasarkan pencarian
                if (currentFilters.searchQuery) {
                    const query = currentFilters.searchQuery.toLowerCase();
                    console.log('Searching for:', query);

                    filtered = $.grep(filtered, function(property) {
                        const name = (property.property_name || '').toLowerCase();
                        const address = (property.property_address || '').toLowerCase();
                        const description = (property.property_description || '').toLowerCase();
                        const type = (property.property_type || '').toLowerCase();

                        return name.indexOf(query) !== -1 ||
                            address.indexOf(query) !== -1 ||
                            description.indexOf(query) !== -1 ||
                            type.indexOf(query) !== -1;
                    });
                    console.log('After search filter:', filtered.length);
                }

                // Filter berdasarkan harga
                if (currentFilters.priceRange !== 'all') {
                    filtered = $.grep(filtered, function(property) {
                        const price = parseFloat(property.property_price) || 0;

                        switch (currentFilters.priceRange) {
                            case 'low':
                                return price <= 500000000;
                            case 'medium':
                                return price > 500000000 && price <= 1000000000;
                            case 'high':
                                return price > 1000000000;
                            default:
                                return true;
                        }
                    });
                    console.log('After price filter:', filtered.length);
                }

                // Filter berdasarkan luas tanah
                if (currentFilters.landArea !== 'all') {
                    filtered = $.grep(filtered, function(property) {
                        const landArea = parseFloat(property.property_land_area) || 0;

                        switch (currentFilters.landArea) {
                            case 'small':
                                return landArea <= 100;
                            case 'medium':
                                return landArea > 100 && landArea <= 500;
                            case 'large':
                                return landArea > 500;
                            default:
                                return true;
                        }
                    });
                    console.log('After land area filter:', filtered.length);
                }

                // Sorting
                filtered.sort(function(a, b) {
                    switch (currentFilters.sortBy) {
                        case 'newest':
                            const dateA = new Date(a.created_at || 0);
                            const dateB = new Date(b.created_at || 0);
                            return dateB - dateA;

                        case 'price_low':
                            const priceA = parseFloat(a.property_price) || 0;
                            const priceB = parseFloat(b.property_price) || 0;
                            return priceA - priceB;

                        case 'price_high':
                            const priceC = parseFloat(a.property_price) || 0;
                            const priceD = parseFloat(b.property_price) || 0;
                            return priceD - priceC;

                        case 'name':
                            const nameA = (a.property_name || '').toLowerCase();
                            const nameB = (b.property_name || '').toLowerCase();
                            return nameA.localeCompare(nameB);

                        default:
                            return 0;
                    }
                });

                return filtered;
            }

            function getCurrentPageProperties(filteredProperties) {
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = Math.min(startIndex + itemsPerPage, filteredProperties.length);
                return filteredProperties.slice(startIndex, endIndex);
            }

            function updateFilteredCount(count) {
                $propertyCount.text(count + ' Properti');
            }

            function renderProperties(properties) {
                console.log('Rendering properties:', properties.length);

                if (properties.length === 0) {
                    console.log('No properties to render, showing no results');
                    showNoResults();
                    return;
                }

                hideNoResults();
                $propertyContainer.empty();

                $.each(properties, function(index, property) {
                    const propertyCard = createPropertyCard(property);
                    $propertyContainer.append(propertyCard);
                });
            }

            function renderPagination() {
                if (totalPages <= 1 || filteredProperties.length <= itemsPerPage) {
                    $paginationContainer.empty();
                    return;
                }

                $paginationContainer.empty();

                const paginationWrapper = $('<div>').addClass(
                    'flex flex-col md:flex-row justify-center items-center gap-4');

                const paginationNav = $('<nav>').addClass('flex items-center space-x-2');

                // Previous button
                const prevButton = $('<button>').addClass(
                    'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                ).html('<i class="fas fa-chevron-left"></i>').prop('disabled', currentPage === 1);

                prevButton.on('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        filterAndPaginate();
                    }
                });

                paginationNav.append(prevButton);

                // Page numbers
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                if (startPage > 1) {
                    paginationNav.append(createPageButton(1));

                    if (startPage > 2) {
                        paginationNav.append($('<span>').addClass('px-2 text-gray-400').text('...'));
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationNav.append(createPageButton(i));
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationNav.append($('<span>').addClass('px-2 text-gray-400').text('...'));
                    }
                    paginationNav.append(createPageButton(totalPages));
                }

                // Next button
                const nextButton = $('<button>').addClass(
                    'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                ).html('<i class="fas fa-chevron-right"></i>').prop('disabled', currentPage === totalPages);

                nextButton.on('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        filterAndPaginate();
                    }
                });

                paginationNav.append(nextButton);

                // Info text
                const infoDiv = $('<div>').addClass('text-sm text-gray-500');
                const start = (currentPage - 1) * itemsPerPage + 1;
                const end = Math.min(currentPage * itemsPerPage, filteredProperties.length);
                infoDiv.html(
                    `Menampilkan <span class="font-semibold">${start}-${end}</span> dari <span class="font-semibold">${filteredProperties.length}</span> properti`
                );

                paginationWrapper.append(paginationNav);
                paginationWrapper.append(infoDiv);
                $paginationContainer.append(paginationWrapper);
            }

            function createPageButton(pageNumber) {
                const isActive = currentPage === pageNumber;
                const button = $('<button>').addClass(
                    `px-3 py-2 rounded-lg transition-colors ${isActive ? 'bg-red-600 text-white border-red-600' : 'border border-gray-300 text-gray-600 lg:hover:bg-gray-50'}`
                ).text(pageNumber);

                button.on('click', function() {
                    if (currentPage !== pageNumber) {
                        currentPage = pageNumber;
                        filterAndPaginate();
                    }
                });

                return button;
            }

            function createPropertyCard(property) {
                console.log('Creating card for property:', property);

                const name = property.property_name || 'Nama Properti';
                const description = property.property_description || 'Deskripsi properti tidak tersedia';

                // MODIFIKASI: Ambil gambar menggunakan fungsi helper
                const imageSrc = getSafeImageUrl(property.property_image);

                const slug = property.property_slug || property.uuid || '';
                const price = parseFloat(property.property_price) || 0;
                const address = property.property_address || 'Alamat tidak tersedia';

                // Ambil angka dari building_area dan land_area
                const buildingAreaStr = property.property_building_area || '0';
                const landAreaStr = property.property_land_area || '0';

                const buildingArea = buildingAreaStr.replace(/[^0-9.]/g, '');
                const landArea = landAreaStr.replace(/[^0-9.]/g, '');

                const whatsapp = property.property_no_whatsapp === null ? "{{ env('NO_WHATSAPP') }}" : property
                    .property_no_whatsapp;
                const facilities = property.property_fasilities || '';

                // Format harga
                const formatPrice = function(price) {
                    if (price <= 0) return null;

                    if (price >= 1000000000) {
                        return `Rp ${(price / 1000000000).toFixed(1)} M`;
                    } else if (price >= 1000000) {
                        return `Rp ${(price / 1000000).toFixed(1)} jt`;
                    } else if (price >= 1000) {
                        return `Rp ${(price / 1000).toFixed(1)} rb`;
                    }
                    return `Rp ${price}`;
                };

                const formattedPrice = formatPrice(price);
                const showPriceTag = formattedPrice !== null;

                // Parse fasilitas
                let facilitiesList = [];
                try {
                    if (facilities && typeof facilities === 'string') {
                        const parsed = JSON.parse(facilities);
                        if ($.isArray(parsed)) {
                            facilitiesList = parsed;
                        }
                    }
                } catch (e) {
                    if (facilities && typeof facilities === 'string') {
                        facilitiesList = [facilities];
                    }
                }

                // Potong deskripsi
                const stripHtml = function(html) {
                    if (!html) return '';
                    const div = $('<div>').html(html);
                    return div.text() || div.html() || '';
                };

                const excerpt = stripHtml(description).substring(0, 100) + (stripHtml(description).length > 100 ?
                    '...' : '');

                // Buat URL detail properti
                const propertyDetailUrl = `${window.location.origin}/property/${slug}`;

                // Buat pesan WhatsApp
                const whatsappMessage = `Halo, saya tertarik dengan properti "${name}" di NUPARIS.ID.

Link detail: ${propertyDetailUrl}

Mohon info lebih lanjut. Terima kasih.`;

                const whatsappUrl =
                    `https://wa.me/${whatsapp.replace(/\D/g, '')}?text=${encodeURIComponent(whatsappMessage)}`;

                // Generate HTML untuk Price Tag
                const priceTagHTML = showPriceTag ? `
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg shadow-sm">
                        <div class="text-sm font-bold text-red-600">${formattedPrice}</div>
                    </div>
                ` : '';

                // Buat card HTML dengan lg: untuk hover
                const cardHTML = `
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg cursor-pointer animate-fade-in-up lg:hover:-translate-y-1 transition-all duration-300 lg:hover:shadow-xl group">
                        <!-- Image with overlay -->
                        <div class="relative h-56 overflow-hidden">
                            <img src="${imageSrc}" 
                                 alt="${name}" 
                                 class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1568605114967-8130f3a36994?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">

                            <!-- Logo Overlay -->
                            <div class="absolute top-3 left-3 z-10 opacity-50">
                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $infos->meta_image) }}" 
                                         alt="NUPARIS Logo" 
                                         class="w-6 h-6">
                                </div>
                            </div>

                            <!-- Price Tag -->
                            ${priceTagHTML}
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <!-- Title & Subtitle -->
                            <h3 class="text-lg font-bold text-slate-800 mb-2 lg:group-hover:text-red-600 transition-colors">
                                ${name}
                            </h3>
                            <p class="text-slate-600 text-sm mb-4 line-clamp-2">
                                ${excerpt || 'Deskripsi tidak tersedia'}
                            </p>

                            <!-- Property Details -->
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <!-- Luas Tanah -->
                                <div class="text-center bg-slate-50 py-2 rounded-lg border border-slate-100">
                                    <div class="text-red-600 text-sm font-bold">${landArea}</div>
                                    <div class="text-slate-500 text-[10px]">Luas Tanah</div>
                                </div>

                                <!-- Luas Bangunan -->
                                <div class="text-center bg-slate-50 py-2 rounded-lg border border-slate-100">
                                    <div class="text-red-600 text-sm font-bold">${buildingArea}</div>
                                    <div class="text-slate-500 text-[10px]">Luas Bangunan</div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start gap-3 mb-5 p-3 bg-slate-50 rounded-lg">
                                <i class="fas fa-map-marker-alt text-red-600 mt-1"></i>
                                <div>
                                    <p class="text-slate-700 text-sm font-medium mb-1">Lokasi</p>
                                    <p class="text-slate-500 text-xs line-clamp-2">${address}</p>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="flex gap-3">
                                <!-- Tombol Lihat Detail -->
                                <button onclick="window.location.href='/property/${slug}'"
                                    class="w-[85%] bg-red-600 lg:hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 text-center lg:hover:-translate-y-0.5 lg:hover:shadow-md">
                                    Lihat Detail
                                </button>

                                <!-- Tombol WhatsApp -->
                                ${whatsapp ? `
                                                                        <a href="${whatsappUrl}"
                                                                           target="_blank"
                                                                           class="w-[15%] bg-white border-2 border-red-600 text-red-600 lg:hover:bg-red-600 lg:hover:text-white font-semibold py-3 rounded-lg transition-colors duration-200 flex items-center justify-center lg:hover:-translate-y-0.5 lg:hover:shadow-md">
                                                                            <i class="fab fa-whatsapp text-sm"></i>
                                                                        </a>
                                                                    ` : `
                                                                        <button onclick="alert('Nomor WhatsApp tidak tersedia')"
                                                                            class="w-[15%] bg-white border-2 border-gray-300 text-gray-400 font-semibold py-3 rounded-lg transition-colors duration-200 flex items-center justify-center cursor-not-allowed">
                                                                            <i class="fab fa-whatsapp text-sm"></i>
                                                                        </button>
                                                                    `}
                            </div>
                        </div>
                    </div>
                `;

                const card = $(cardHTML);
                return card;
            }

            function showLoading() {
                console.log('Showing loading indicator');
                $loadingIndicator.removeClass('hidden');
                $noResults.addClass('hidden');
                $propertyContainer.empty();
                $paginationContainer.empty();
            }

            function hideLoading() {
                console.log('Hiding loading indicator');
                $loadingIndicator.addClass('hidden');
            }

            function showNoResults() {
                console.log('Showing no results message');
                $noResults.removeClass('hidden');
                $propertyContainer.empty();
                $paginationContainer.empty();
                $loadingIndicator.addClass('hidden');
            }

            function hideNoResults() {
                console.log('Hiding no results message');
                $noResults.addClass('hidden');
            }

            function clearAllFilters() {
                console.log('Clearing all filters');
                // Reset radio buttons
                $('input[name="priceRange"][value="all"]').prop('checked', true);
                $('input[name="landArea"][value="all"]').prop('checked', true);
                $('input[name="sortBy"][value="newest"]').prop('checked', true);

                currentFilters = {
                    priceRange: 'all',
                    landArea: 'all',
                    sortBy: 'newest',
                    searchQuery: ''
                };

                $filterPanel.addClass('hidden');
                $sortPanel.addClass('hidden');
                currentPage = 1;
                filterAndPaginate();
            }
        });
    </script>
</body>

</html>
