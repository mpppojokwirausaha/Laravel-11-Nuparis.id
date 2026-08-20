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

    <!-- Meta Tags for Events Page -->
    <meta name="title" content="Kumpulan Event & Kegiatan - NUPARIS.ID" />
    <meta name="description"
        content="Temukan berbagai event, kegiatan, dan acara menarik dari NUPARIS. Daftar sekarang untuk workshop, seminar, festival, dan kegiatan UMKM lainnya." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Kumpulan Event & Kegiatan - NUPARIS.ID" />
    <meta property="og:description"
        content="Temukan dan ikuti berbagai event menarik di NUPARIS. Workshop, seminar, festival, dan kegiatan UMKM untuk pengembangan bisnis Anda." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Kumpulan Event NUPARIS" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="NUPARIS.ID" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="Kumpulan Event & Kegiatan - NUPARIS.ID" />
    <meta property="twitter:description"
        content="Temukan berbagai event, kegiatan, dan acara menarik dari NUPARIS. Daftar sekarang untuk workshop, seminar, festival, dan kegiatan UMKM lainnya." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Kumpulan Event NUPARIS" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="Kumpulan Event & Kegiatan - NUPARIS.ID" />
    <meta property="linkedin:description"
        content="Temukan berbagai event, kegiatan, dan acara menarik dari NUPARIS. Workshop, seminar, festival, dan kegiatan UMKM untuk pengembangan bisnis Anda." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Kumpulan Event NUPARIS" />

    <!-- Tailwind Configuration -->
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
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
                    <div>
                        <nav class="flex items-center text-sm text-red-100 mb-2">
                            <a href="{{ route('landingpage') }}"
                                class="lg:hover:text-white transition-colors">Beranda</a>
                            <i class="fas fa-chevron-right mx-2 text-xs"></i>
                            <span>Event</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-3">Event</h1>
                        <p class="text-red-100 max-w-2xl">Temukan dan ikuti berbagai acara UMKM menarik dan pelatihan
                            di
                            NUPARIS. Daftar sekarang untuk mendapatkan pengalaman terbaik.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4 lg:mt-0">
                        <div class="text-center">
                            <div id="totalEventsCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Total Event</div>
                        </div>
                        <div class="h-12 w-px bg-white/30 mx-4"></div>
                        <div class="text-center">
                            <div id="ongoingEventsCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Ongoing</div>
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
                                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition text-sm"
                                    placeholder="Cari event...">
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

                    <!-- Active Filters (Hidden by default) -->
                    <div id="activeFilters" class="hidden px-4 pb-4">
                        <div class="flex flex-wrap gap-2" id="filterChips"></div>
                    </div>
                </div>

                <!-- Filter Panel (Hidden by default) -->
                <div id="filterPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Status Event</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="status" value="ongoing"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">Sedang Berlangsung</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="status" value="upcoming"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">Akan Datang</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="status" value="ended"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">Sudah Berakhir</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Kategori</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="category" value="seminar"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">Seminar & Workshop</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="category" value="pelatihan"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">Pelatihan</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="category" value="umkm"
                                        class="mr-2 rounded text-primary focus:ring-primary h-4 w-4">
                                    <span class="text-gray-700 text-sm">UMKM</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Harga</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="free" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Gratis</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="priceRange" value="paid" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Berbayar</span>
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
                            class="px-4 py-2 bg-primary lg:hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Sort Panel (Hidden by default) -->
                <div id="sortPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Urutkan Berdasarkan</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sortBy" value="date_asc" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Tanggal Terdekat</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sortBy" value="date_desc" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Tanggal Terjauh</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sortBy" value="title_asc" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Nama A-Z</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="sortBy" value="title_desc" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Nama Z-A</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Tampilkan</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="viewType" value="grid" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Tampilan Grid</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="viewType" value="list" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Tampilan List</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button id="cancelSort"
                            class="px-4 py-2 text-gray-600 lg:hover:text-gray-800 font-medium text-sm transition-colors">
                            Batal
                        </button>
                        <button id="applySort"
                            class="px-4 py-2 bg-primary lg:hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                            Terapkan Pengurutan
                        </button>
                    </div>
                </div>
            </section>

            <!-- Events Section -->
            <section>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-primary pl-3">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Event</h2>
                        <span id="eventsCount" class="bg-primary text-white text-sm font-bold px-3 py-1 rounded-full">
                            0 Event
                        </span>
                    </div>

                    <!-- View Toggle - Hanya tampil di desktop -->
                    <div class="hidden lg:flex items-center gap-2">
                        <span class="text-sm text-gray-600 mr-2">
                            Tampilan:
                        </span>

                        <button id="gridViewBtn" class="p-2 rounded-lg bg-primary text-white">
                            <i class="fas fa-th"></i>
                        </button>
                        <button id="listViewBtn"
                            class="p-2 rounded-lg bg-gray-200 text-gray-600 lg:hover:bg-gray-300 transition-colors">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Events Container -->
                <div id="eventsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Events will be loaded here via AJAX -->
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary">
                    </div>
                    <p class="mt-4 text-gray-600">Memuat event...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada event yang ditemukan</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Coba ubah kata kunci pencarian atau filter yang Anda
                        gunakan.</p>
                    <button id="resetSearch"
                        class="bg-primary lg:hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-redo mr-2"></i> Reset Pencarian
                    </button>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="flex flex-col md:flex-row justify-center items-center mt-12 gap-4 hidden">
                    <nav class="flex items-center space-x-2">
                        <button
                            class="px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            id="prevPage">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="flex items-center space-x-1" id="pageNumbers"></div>

                        <button
                            class="px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            id="nextPage">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>

                    <div class="text-sm text-gray-500">
                        Menampilkan <span id="currentRange">0-0</span> dari <span id="totalEvents">0</span> event
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        @include('front-end.layouts.components.footer')
    </div>
    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.bottom-bar')

    <!-- Main Custom Script -->
    <script>
        $(document).ready(function() {
            // State variables
            let currentPage = 1;
            const itemsPerPage = 12;
            let currentView = 'grid';
            let currentFilters = {
                status: [],
                category: [],
                priceRange: 'all',
                searchQuery: ''
            };
            let currentSort = 'date_desc'; // Default: tanggal terjauh/terbaru di atas (sesuai controller)
            let allEvents = [];

            // DOM Elements
            const eventsContainer = $('#eventsContainer');
            const loadingIndicator = $('#loadingIndicator');
            const noResults = $('#noResults');
            const pagination = $('#pagination');
            const eventsCount = $('#eventsCount');
            const totalEventsCount = $('#totalEventsCount');
            const ongoingEventsCount = $('#ongoingEventsCount');
            const gridViewBtn = $('#gridViewBtn');
            const listViewBtn = $('#listViewBtn');
            const searchInput = $('#searchInput');
            const clearSearch = $('#clearSearch');
            const resetSearch = $('#resetSearch');
            const filterButton = $('#filterButton');
            const filterPanel = $('#filterPanel');
            const sortButton = $('#sortButton');
            const sortPanel = $('#sortPanel');
            const activeFilters = $('#activeFilters');
            const filterChips = $('#filterChips');
            const applyFilter = $('#applyFilter');
            const cancelFilter = $('#cancelFilter');
            const applySort = $('#applySort');
            const cancelSort = $('#cancelSort');
            const prevPage = $('#prevPage');
            const nextPage = $('#nextPage');
            const currentRange = $('#currentRange');
            const totalEvents = $('#totalEvents');

            // Initialize
            setupEventListeners();
            loadEventsFromController();

            async function loadEventsFromController() {
                try {
                    showLoading();

                    const response = await $.ajax({
                        url: '{{ route('event-data') }}',
                        type: 'GET',
                        dataType: 'json',
                        cache: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Cache-Control': 'no-cache, no-store, must-revalidate',
                            'Pragma': 'no-cache',
                            'Expires': '0'
                        },
                        data: {
                            '_': new Date().getTime()
                        }
                    });

                    if (response.success && response.eventData) {
                        allEvents = response.eventData;
                        console.log('Events loaded:', allEvents.length);
                        updateEventCounts(allEvents);
                        renderEvents();
                    } else {
                        showNoResults();
                    }
                } catch (error) {
                    console.error('Error loading events:', error);
                    showNoResults();
                }
            }

            function updateEventCounts(events) {
                const total = events.length;
                const now = new Date();
                const ongoing = $.grep(events, function(event) {
                    const start = new Date(event.event_date_start);
                    const end = new Date(event.event_date_end);
                    return now >= start && now <= end;
                }).length;

                if (totalEventsCount.length) totalEventsCount.text(total);
                if (ongoingEventsCount.length) ongoingEventsCount.text(ongoing);
            }

            function setupEventListeners() {
                // View toggle buttons
                if (gridViewBtn.length && listViewBtn.length) {
                    gridViewBtn.on('click', () => {
                        currentView = 'grid';
                        updateViewButtons();
                        renderEvents();
                    });

                    listViewBtn.on('click', () => {
                        currentView = 'list';
                        updateViewButtons();
                        renderEvents();
                    });
                }

                // Search functionality
                if (searchInput.length) {
                    searchInput.on('input', function() {
                        if ($(this).val().trim() !== '') {
                            if (clearSearch.length) clearSearch.removeClass('hidden');
                        } else {
                            if (clearSearch.length) clearSearch.addClass('hidden');
                        }
                    });

                    searchInput.on('keypress', function(e) {
                        if (e.key === 'Enter') {
                            currentFilters.searchQuery = $(this).val().trim();
                            currentPage = 1;
                            updateActiveFiltersDisplay();
                            renderEvents();
                        }
                    });
                }

                if (clearSearch.length) {
                    clearSearch.on('click', function() {
                        if (searchInput.length) searchInput.val('');
                        currentFilters.searchQuery = '';
                        currentPage = 1;
                        $(this).addClass('hidden');
                        updateActiveFiltersDisplay();
                        renderEvents();
                    });
                }

                // Filter panel toggle
                if (filterButton.length) {
                    filterButton.on('click', function() {
                        filterPanel.toggleClass('hidden');
                        if (sortPanel.length) sortPanel.addClass('hidden');
                    });
                }

                // Sort panel toggle
                if (sortButton.length) {
                    sortButton.on('click', function() {
                        sortPanel.toggleClass('hidden');
                        if (filterPanel.length) filterPanel.addClass('hidden');
                    });
                }

                // Apply filter button
                if (applyFilter.length) {
                    applyFilter.on('click', applyFilters);
                }

                // Cancel filter button
                if (cancelFilter.length) {
                    cancelFilter.on('click', function() {
                        filterPanel.addClass('hidden');
                    });
                }

                // Apply sort button
                if (applySort.length) {
                    applySort.on('click', applySortHandler);
                }

                // Cancel sort button
                if (cancelSort.length) {
                    cancelSort.on('click', function() {
                        sortPanel.addClass('hidden');
                    });
                }

                // Reset search button
                if (resetSearch.length) {
                    resetSearch.on('click', function() {
                        clearAllFiltersHandler();
                        if (searchInput.length) {
                            searchInput.val('');
                            currentFilters.searchQuery = '';
                        }
                        if (clearSearch.length) clearSearch.addClass('hidden');
                        renderEvents();
                    });
                }

                // Pagination buttons
                if (prevPage.length) {
                    prevPage.on('click', function() {
                        if (currentPage > 1) {
                            currentPage--;
                            renderEvents();
                        }
                    });
                }

                if (nextPage.length) {
                    nextPage.on('click', function() {
                        const totalPages = Math.ceil(getFilteredEvents().length / itemsPerPage);
                        if (currentPage < totalPages) {
                            currentPage++;
                            renderEvents();
                        }
                    });
                }

                // Close panels when clicking outside
                $(document).on('click', function(e) {
                    if (filterPanel.length && !filterPanel.is(e.target) && filterPanel.has(e.target)
                        .length === 0 &&
                        filterButton.length && !filterButton.is(e.target) && filterButton.has(e.target)
                        .length === 0) {
                        filterPanel.addClass('hidden');
                    }
                    if (sortPanel.length && !sortPanel.is(e.target) && sortPanel.has(e.target).length ===
                        0 &&
                        sortButton.length && !sortButton.is(e.target) && sortButton.has(e.target).length ===
                        0) {
                        sortPanel.addClass('hidden');
                    }
                });
            }

            function updateViewButtons() {
                if (currentView === 'grid' && gridViewBtn.length && listViewBtn.length) {
                    gridViewBtn.removeClass('bg-gray-200 text-gray-600').addClass('bg-primary text-white');
                    listViewBtn.removeClass('bg-primary text-white').addClass(
                        'bg-gray-200 text-gray-600 lg:hover:bg-gray-300 transition-colors');
                } else if (listViewBtn.length && gridViewBtn.length) {
                    listViewBtn.removeClass('bg-gray-200 text-gray-600').addClass('bg-primary text-white');
                    gridViewBtn.removeClass('bg-primary text-white').addClass(
                        'bg-gray-200 text-gray-600 lg:hover:bg-gray-300 transition-colors');
                }
            }

            function applyFilters() {
                // Get status filters
                const statusCheckboxes = $('input[name="status"]:checked');
                currentFilters.status = $.map(statusCheckboxes, function(cb) {
                    return $(cb).val();
                });

                // Get category filters
                const categoryCheckboxes = $('input[name="category"]:checked');
                currentFilters.category = $.map(categoryCheckboxes, function(cb) {
                    return $(cb).val();
                });

                // Get price range
                const priceRangeRadio = $('input[name="priceRange"]:checked');
                if (priceRangeRadio.length) {
                    currentFilters.priceRange = priceRangeRadio.val();
                }

                // Close filter panel
                filterPanel.addClass('hidden');

                // Reset to page 1
                currentPage = 1;

                // Update active filters display
                updateActiveFiltersDisplay();

                // Render events with new filters
                renderEvents();
            }

            function applySortHandler() {
                // Get sort option
                const sortRadio = $('input[name="sortBy"]:checked');
                if (sortRadio.length) {
                    currentSort = sortRadio.val();
                    console.log('Sorting changed to:', currentSort);
                }

                // Get view type
                const viewRadio = $('input[name="viewType"]:checked');
                if (viewRadio.length) {
                    currentView = viewRadio.val();
                }

                // Update view buttons
                updateViewButtons();

                // Close sort panel
                sortPanel.addClass('hidden');

                // Reset to page 1
                currentPage = 1;

                // Render events with new sort
                renderEvents();
            }

            function getFilteredEvents() {
                if (!allEvents.length) return [];

                let filteredEvents = [...allEvents]; // Gunakan spread operator

                // Apply search filter
                if (currentFilters.searchQuery) {
                    const query = currentFilters.searchQuery.toLowerCase();
                    filteredEvents = filteredEvents.filter(function(event) {
                        return (event.event_title && event.event_title.toLowerCase().includes(query)) ||
                            (event.event_description && event.event_description.toLowerCase().includes(
                                query)) ||
                            (event.event_location && event.event_location.toLowerCase().includes(query));
                    });
                }

                // Apply status filter
                if (currentFilters.status.length > 0) {
                    filteredEvents = filteredEvents.filter(function(event) {
                        const now = new Date();
                        const start = new Date(event.event_date_start);
                        const end = new Date(event.event_date_end);
                        let status = 'upcoming';

                        if (now >= start && now <= end) {
                            status = 'ongoing';
                        } else if (now > end) {
                            status = 'ended';
                        }

                        return currentFilters.status.includes(status);
                    });
                }

                // Apply category filter
                if (currentFilters.category.length > 0) {
                    filteredEvents = filteredEvents.filter(function(event) {
                        const category = getEventCategory(event);
                        return currentFilters.category.includes(category);
                    });
                }

                // Apply price filter
                if (currentFilters.priceRange !== 'all') {
                    filteredEvents = filteredEvents.filter(function(event) {
                        if (currentFilters.priceRange === 'free') {
                            return event.event_price === '0' || event.event_price === 0;
                        } else if (currentFilters.priceRange === 'paid') {
                            return event.event_price !== '0' && event.event_price !== 0 && event
                                .event_price;
                        }
                        return true;
                    });
                }

                // Apply sorting
                console.log('Sorting with method:', currentSort);

                if (currentSort === 'date_asc') {
                    filteredEvents.sort(function(a, b) {
                        return new Date(a.event_date_start) - new Date(b.event_date_start);
                    });
                } else if (currentSort === 'date_desc') {
                    filteredEvents.sort(function(a, b) {
                        return new Date(b.event_date_start) - new Date(a.event_date_start);
                    });
                } else if (currentSort === 'title_asc') {
                    filteredEvents.sort(function(a, b) {
                        return (a.event_title || '').localeCompare(b.event_title || '');
                    });
                } else if (currentSort === 'title_desc') {
                    filteredEvents.sort(function(a, b) {
                        return (b.event_title || '').localeCompare(a.event_title || '');
                    });
                }

                return filteredEvents;
            }

            function getEventCategory(event) {
                const title = (event.event_title || '').toLowerCase();
                if (title.includes('seminar') || title.includes('workshop')) {
                    return 'seminar';
                } else if (title.includes('pelatihan') || title.includes('training')) {
                    return 'pelatihan';
                } else if (title.includes('umkm') || title.includes('usaha')) {
                    return 'umkm';
                }
                return 'seminar';
            }

            function renderEvents() {
                showLoading();

                setTimeout(function() {
                    const filteredEvents = getFilteredEvents();
                    const totalEventsCount = filteredEvents.length;

                    // Update events count
                    if (eventsCount.length) {
                        eventsCount.text(totalEventsCount + ' Event');
                    }

                    // Show/hide no results message
                    if (totalEventsCount === 0) {
                        showNoResults();
                        return;
                    } else {
                        hideNoResults();
                    }

                    // Calculate pagination
                    const totalPages = Math.ceil(totalEventsCount / itemsPerPage);
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = Math.min(startIndex + itemsPerPage, totalEventsCount);
                    const paginatedEvents = filteredEvents.slice(startIndex, endIndex);

                    // Clear container
                    if (eventsContainer.length) {
                        eventsContainer.empty();
                    }

                    // Render events
                    $.each(paginatedEvents, function(index, event) {
                        const eventCard = createEventCard(event);
                        if (eventsContainer.length) {
                            eventsContainer.append(eventCard);
                        }
                    });

                    // Update pagination
                    updatePagination(totalEventsCount, totalPages, startIndex, endIndex);

                    // Hide loading indicator
                    hideLoading();
                }, 300);
            }

            function createEventCard(event) {
                // Format date
                const eventDate = new Date(event.event_date_start);
                const formattedDate = eventDate.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });

                // Determine status
                const now = new Date();
                const start = new Date(event.event_date_start);
                const end = new Date(event.event_date_end);
                let statusClass = '';
                let statusText = '';
                let canRegister = false;

                if (now >= start && now <= end) {
                    statusClass = 'bg-green-500 text-white';
                    statusText = 'Ongoing';
                    canRegister = true;
                } else if (now < start) {
                    statusClass = 'bg-blue-500 text-white';
                    statusText = 'Upcoming';
                    canRegister = true;
                } else {
                    statusClass = 'bg-red-500 text-white';
                    statusText = 'End';
                    canRegister = false;
                }

                // Determine if event is free or paid
                const isFree = event.event_price === '0' || event.event_price === 0;
                const priceText = isFree ? 'GRATIS' : 'Rp ' + parseInt(event.event_price).toLocaleString('id-ID');

                // Get category
                const category = getEventCategory(event);
                const categoryLabel = getCategoryLabel(category);

                // Strip HTML tags from description
                const description = stripHtmlTags(event.event_description);
                const shortDescription = description.length > 100 ? description.substring(0, 100) + '...' :
                    description;

                // Quota and progress (dynamic from event data)
                const rawQuota = parseInt(event.event_quota || event.quota, 10);
                const rawRegistered = parseInt(event.participants_count || event.registeredCount || event
                    .event_registered || event.registered || 0, 10);
                const hasQuota = Number.isFinite(rawQuota) && rawQuota > 0;
                const safeRegistered = Number.isFinite(rawRegistered) && rawRegistered >= 0 ? rawRegistered : 0;
                const quota = hasQuota ? rawQuota : Math.max(safeRegistered, 20);
                const registered = Math.min(safeRegistered, quota);
                const quotaPercentage = Math.round((registered / quota) * 100);
                const remainingQuota = quota - registered;
                const quotaText = remainingQuota > 0 ? 'Tersisa ' + remainingQuota : 'Habis';
                const quotaClass = remainingQuota > 0 ? 'bg-blue-50 text-blue-700 border-blue-100' :
                    'bg-gray-100 text-gray-400 border-gray-200';

                // Progress bar color
                let progressBarColor = 'bg-green-500';
                if (quotaPercentage >= 90) {
                    progressBarColor = 'bg-red-500';
                } else if (quotaPercentage >= 70) {
                    progressBarColor = 'bg-yellow-500';
                }

                var card = $('\
                                            <article class="cursor-pointer bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 flex flex-row h-full lg:hover:-translate-y-1 transition-all duration-300 lg:hover:shadow-xl group">\
                                                <div class="relative w-1/3 min-w-[140px] h-auto overflow-hidden">\
                                                    <img src="{{ asset('storage/') }}/' + event.event_image + '" alt="' +
                    event
                    .event_title + '" \
                                                        class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500"\
                                                        onerror="this.src=\'https://via.placeholder.com/400x300?text=Event+Image\'">\
                                                    <div class="absolute top-2 left-2 z-10 opacity-50">\
                                                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">\
                                                            <img src="{{ asset('storage/' . $infos->meta_image) }}" alt="NUPARIS Logo" class="w-6 h-6">\
                                                        </div>\
                                                    </div>\
                                                    <div class="absolute bottom-2 left-2 z-10 ' + statusClass + ' text-white text-xs font-semibold px-2 py-1 rounded shadow-sm">\
                                                        ' + statusText + '\
                                                    </div>\
                                                </div>\
                                                <div class="p-5 w-2/3 flex flex-col h-full">\
                                                    <div class="flex-grow">\
                                                        <span class="text-xs font-bold text-primary uppercase tracking-wider mb-1">\
                                                            ' + categoryLabel + '\
                                                        </span>\
                                                        <h3 class="font-bold text-gray-800 text-lg mb-1 lg:group-hover:text-primary transition">\
                                                            ' + event.event_title + '\
                                                        </h3>\
                                                        <div class="flex items-center gap-2 text-gray-500 text-xs mb-2">\
                                                            <i class="far fa-calendar-check"></i>\
                                                            <span>' + formattedDate + '</span>\
                                                        </div>\
                                                        <div class="mb-2">\
                                                            <span class="inline-block ' + (isFree ?
                        'bg-green-100 text-green-800' :
                        'bg-blue-100 text-blue-800') + ' text-xs font-bold px-2 py-1 rounded">\
                                                                ' + priceText + '\
                                                            </span>\
                                                        </div>\
                                                        <div class="mb-3">\
                                                            <div class="flex justify-between items-center mb-1">\
                                                                <span class="text-xs font-medium text-gray-700">Kuota Terisi:</span>\
                                                                <span class="text-xs font-bold ' + (quotaPercentage >= 90 ?
                        'text-red-600' :
                        quotaPercentage >= 70 ? 'text-yellow-600' : 'text-green-600') + '">\
                                                                    ' + quotaPercentage + '%\
                                                                </span>\
                                                            </div>\
                                                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">\
                                                                <div class="' + progressBarColor +
                    ' h-2 rounded-full" style="width: ' +
                    quotaPercentage + '%"></div>\
                                                            </div>\
                                                            <div class="flex justify-between items-center">\
                                                                <div class="text-xs text-gray-600">\
                                                                    <i class="fas fa-users mr-1"></i>\
                                                                    ' + registered + '/' + quota + ' peserta\
                                                                </div>\
                                                                <div class="' + quotaClass + ' text-[10px] font-bold px-2 py-1 rounded border">\
                                                                    ' + quotaText + '\
                                                                </div>\
                                                            </div>\
                                                        </div>\
                                                        <p class="text-gray-500 text-sm line-clamp-2">\
                                                            ' + shortDescription + '\
                                                        </p>\
                                                    </div>\
                                                    <div class="flex gap-3 mt-4">\
                                                        <a href="/event/' + event.event_slug + '"\
                                                            class="flex-1 flex items-center justify-center bg-red-500 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-200 lg:hover:bg-red-700">\
                                                            Lihat Detail\
                                                        </a>\
                                                        ' + (canRegister ?
                        '<a href="#" class="flex-[0_0_25%] flex items-center justify-center bg-white border-2 border-primary text-primary font-semibold py-3 rounded-lg transition duration-200 lg:hover:bg-primary lg:hover:text-white">\
                                                                <i class="fas fa-shopping-cart text-sm"></i>\
                                                            </a>' :
                        '<button disabled\
                                                                class="flex-[0_0_25%] flex items-center justify-center bg-gray-300 text-gray-500 font-semibold py-3 rounded-lg cursor-not-allowed">\
                                                                <i class="fas fa-shopping-cart text-sm"></i>\
                                                            </button>'
                    ) + '\
                                                    </div>\
                                                </div>\
                                            </article>\
                                        ');

                // Add click event for whole card
                card.on('click', function(e) {
                    // Don't navigate if clicking on buttons or links
                    if (!$(e.target).closest('a').length && !$(e.target).closest('button').length) {
                        window.location.href = '/event/' + event.event_slug;
                    }
                });

                return card;
            }

            function stripHtmlTags(html) {
                if (!html) return '';
                var div = document.createElement('div');
                div.innerHTML = html;
                return div.textContent || div.innerText || '';
            }

            function getCategoryLabel(category) {
                const labels = {
                    'seminar': 'Seminar & Workshop',
                    'pelatihan': 'Pelatihan',
                    'umkm': 'UMKM'
                };
                return labels[category] || category;
            }

            function updatePagination(totalEventsCount, totalPages, startIndex, endIndex) {
                if (!pagination.length) return;

                if (totalPages <= 1) {
                    pagination.addClass('hidden');
                    return;
                }

                pagination.removeClass('hidden');

                // Update range display
                if (currentRange.length) currentRange.text((startIndex + 1) + '-' + endIndex);
                if (totalEvents.length) totalEvents.text(totalEventsCount);

                // Update page numbers
                const pageNumbersContainer = $('#pageNumbers');
                if (pageNumbersContainer.length) {
                    pageNumbersContainer.empty();

                    for (var i = 1; i <= totalPages; i++) {
                        var pageButton = $(
                            '<button class="px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 transition-colors ' +
                            (currentPage === i ? 'bg-primary text-white' : '') + '">' + i + '</button>');
                        pageButton.on('click', function(page) {
                            return function() {
                                currentPage = page;
                                renderEvents();
                            };
                        }(i));
                        pageNumbersContainer.append(pageButton);
                    }
                }

                // Update prev/next buttons
                if (prevPage.length) prevPage.prop('disabled', currentPage === 1);
                if (nextPage.length) nextPage.prop('disabled', currentPage === totalPages);
            }

            function showLoading() {
                if (loadingIndicator.length) loadingIndicator.removeClass('hidden');
                if (noResults.length) noResults.addClass('hidden');
                if (eventsContainer.length) eventsContainer.empty();
            }

            function hideLoading() {
                if (loadingIndicator.length) loadingIndicator.addClass('hidden');
            }

            function showNoResults() {
                if (noResults.length) noResults.removeClass('hidden');
                if (eventsContainer.length) eventsContainer.empty();
                if (pagination.length) pagination.addClass('hidden');
                if (loadingIndicator.length) loadingIndicator.addClass('hidden');
                if (eventsCount.length) eventsCount.text('0 Event');
            }

            function hideNoResults() {
                if (noResults.length) noResults.addClass('hidden');
            }

            function updateActiveFiltersDisplay() {
                // Implement if needed
            }

            function clearAllFiltersHandler() {
                // Reset all checkboxes and radio buttons
                $('input[name="status"]').prop('checked', false);
                $('input[name="category"]').prop('checked', false);
                $('input[name="priceRange"][value="all"]').prop('checked', true);
                $('input[name="sortBy"][value="date_desc"]').prop('checked', true);
                $('input[name="viewType"][value="grid"]').prop('checked', true);

                // Reset filter state
                currentFilters = {
                    status: [],
                    category: [],
                    priceRange: 'all',
                    searchQuery: ''
                };

                // Reset view
                currentView = 'grid';
                currentSort = 'date_desc';
                updateViewButtons();

                // Hide panels
                if (filterPanel.length) filterPanel.addClass('hidden');
                if (sortPanel.length) sortPanel.addClass('hidden');
                if (activeFilters.length) activeFilters.addClass('hidden');

                // Reset page
                currentPage = 1;

                // Render events
                renderEvents();
            }
        });
    </script>
</body>

</html>
