<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $infos->meta_image) }}">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Meta Tags for News Page -->
    <meta name="title" content="Berita & Informasi Terbaru - NUPARIS.ID" />
    <meta name="description"
        content="Temukan berita terbaru, informasi terkini, dan update seputar perizinan bisnis, regulasi, dan perkembangan usaha di Indonesia." />
    <meta name="keywords"
        content="berita bisnis, informasi perizinan, berita terbaru, update regulasi, perkembangan usaha, kabar terkini" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="Berita & Informasi Terbaru - NUPARIS.ID" />
    <meta property="og:description"
        content="Kumpulan berita terbaru dan informasi terkini seputar perizinan bisnis, regulasi, dan perkembangan usaha di Indonesia." />
    <meta property="og:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="og:image:alt" content="Berita NUPARIS" />
    <meta property="og:site_name" content="NUPARIS.ID" />
    <meta property="og:locale" content="id_ID" />
    <meta property="article:publisher" content="NUPARIS.ID" />
    <meta property="article:section" content="Berita & Informasi" />

    <!-- Twitter Meta Tags -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ url()->current() }}" />
    <meta property="twitter:title" content="Berita & Informasi Terbaru - NUPARIS.ID" />
    <meta property="twitter:description"
        content="Temukan berita terbaru, informasi terkini, dan update seputar perizinan bisnis, regulasi, dan perkembangan usaha di Indonesia." />
    <meta property="twitter:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="twitter:image:alt" content="Berita NUPARIS" />
    <meta property="twitter:site" content="@nuparis_id" />
    <meta property="twitter:creator" content="@nuparis_id" />

    <!-- LinkedIn Meta Tags -->
    <meta property="linkedin:card" content="summary_large_image" />
    <meta property="linkedin:url" content="{{ url()->current() }}" />
    <meta property="linkedin:title" content="Berita & Informasi Terbaru - NUPARIS.ID" />
    <meta property="linkedin:description"
        content="Kumpulan berita terbaru dan informasi terkini seputar perizinan bisnis, regulasi, dan perkembangan usaha di Indonesia." />
    <meta property="linkedin:image" content="{{ asset('storage/' . $infos->meta_image) }}" />
    <meta property="linkedin:image:alt" content="Berita NUPARIS" />

    <!-- Tailwind Configuration -->
    <script src="{{ asset('assets/front-end/js/configtailwind.js') }}"></script>
</head>

<body class="font-sans text-gray-800 bg-gray-50 h-full overflow-x-hidden">

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
                            <span>Berita</span>
                        </nav>
                        <h1 class="text-3xl lg:text-4xl font-bold mb-3">Berita Terkini</h1>
                        <p class="text-red-100 max-w-2xl">Temukan berita terkini, informasi terbaru, dan update seputar
                            perizinan bisnis, regulasi, dan perkembangan usaha di Indonesia.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-xl p-4 mt-4 lg:mt-0">
                        <div class="text-center">
                            <div id="totalNewsCount" class="text-3xl font-bold">0</div>
                            <div class="text-sm text-red-100">Total Berita</div>
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
                                    placeholder="Cari berita atau informasi terkini...">
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
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel (Hidden by default) -->
                <div id="filterPanel" class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Hapus bagian kategori -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3 text-sm">Rentang Waktu</h4>
                            <div class="space-y-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="all" class="mr-2 h-4 w-4"
                                        checked>
                                    <span class="text-gray-700 text-sm">Semua Waktu</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="week" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Minggu Ini</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="month" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Bulan Ini</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="timeRange" value="year" class="mr-2 h-4 w-4">
                                    <span class="text-gray-700 text-sm">Tahun Ini</span>
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
            </section>

            <!-- News Section -->
            <section>
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 border-l-4 border-primary pl-3">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Semua Berita</h2>
                        <span id="newsCount" class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">
                            0 Berita
                        </span>
                    </div>
                </div>

                <!-- News Container -->
                <div id="newsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Berita akan dimuat via AJAX -->
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary">
                    </div>
                    <p class="mt-4 text-gray-600">Memuat berita...</p>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-12 hidden">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-newspaper text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada berita yang ditemukan</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Coba ubah kata kunci pencarian atau filter yang
                        Anda
                        gunakan.</p>
                    <button id="resetSearch"
                        class="bg-primary lg:hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
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
        @include('front-end.layouts.components.footer')
    </div>
    @include('front-end.layouts.components.chat')
    @include('front-end.layouts.components.bottom-bar')

    <!-- Main Custom Script (jQuery Version) -->
    <script>
        $(document).ready(function() {
            console.log('DOM loaded, initializing news page with jQuery...');

            // State variables untuk client-side pagination
            const newsManager = {
                currentPage: 1,
                itemsPerPage: 12,
                totalPages: 1,
                currentFilters: {
                    timeRange: 'all',
                    searchQuery: ''
                },
                allNews: [],
                filteredNews: [],
                searchTimeout: null
            };

            // Cache DOM elements
            const $elements = {
                newsContainer: $('#newsContainer'),
                loadingIndicator: $('#loadingIndicator'),
                noResults: $('#noResults'),
                newsCount: $('#newsCount'),
                totalNewsCount: $('#totalNewsCount'),
                searchInput: $('#searchInput'),
                clearSearch: $('#clearSearch'),
                resetSearch: $('#resetSearch'),
                filterButton: $('#filterButton'),
                filterPanel: $('#filterPanel'),
                applyFilter: $('#applyFilter'),
                cancelFilter: $('#cancelFilter'),
                paginationContainer: $('#paginationContainer')
            };

            // Initialize
            setupEventListeners();
            loadNewsFromController();

            async function loadNewsFromController() {
                console.log('Loading news from controller...');
                showLoading();

                try {
                    const response = await $.ajax({
                        url: '{{ route('news-data') }}',
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        dataType: 'json'
                    });

                    console.log('Response dari controller:', response);

                    // PERBAIKAN: Gunakan response.newsData bukan response.news
                    if (response.success && response.newsData && Array.isArray(response.newsData)) {
                        newsManager.allNews = response.newsData;
                        console.log(`Loaded ${newsManager.allNews.length} news from controller`);

                        // Debug: tampilkan struktur data pertama
                        if (newsManager.allNews.length > 0) {
                            console.log('First news item structure:', newsManager.allNews[0]);
                            console.log('Available keys:', Object.keys(newsManager.allNews[0]));
                        }

                        updateNewsCounts(newsManager.allNews);
                        filterAndPaginate();
                    } else {
                        console.error('Data tidak valid atau kosong:', response);
                        showNoResults();
                    }
                } catch (error) {
                    console.error('Error loading news:', error);
                    showNoResults();
                }
            }

            function updateNewsCounts(news) {
                const total = news.length;
                console.log(`Total news: ${total}`);

                if ($elements.totalNewsCount.length) {
                    $elements.totalNewsCount.text(total);
                }
                if ($elements.newsCount.length) {
                    $elements.newsCount.text(`${total} Berita`);
                }
            }

            function setupEventListeners() {
                console.log('Setting up event listeners...');

                // Search functionality
                if ($elements.searchInput.length) {
                    $elements.searchInput.on('input', function() {
                        if ($(this).val().trim() !== '') {
                            $elements.clearSearch.removeClass('hidden');
                        } else {
                            $elements.clearSearch.addClass('hidden');
                        }

                        clearTimeout(newsManager.searchTimeout);
                        newsManager.searchTimeout = setTimeout(() => {
                            newsManager.currentFilters.searchQuery = $(this).val().trim();
                            newsManager.currentPage = 1;
                            console.log('Search triggered:', newsManager.currentFilters
                                .searchQuery);
                            filterAndPaginate();
                        }, 500);
                    });

                    $elements.searchInput.on('keypress', function(e) {
                        if (e.key === 'Enter') {
                            newsManager.currentFilters.searchQuery = $(this).val().trim();
                            newsManager.currentPage = 1;
                            console.log('Search (Enter):', newsManager.currentFilters.searchQuery);
                            filterAndPaginate();
                        }
                    });
                }

                if ($elements.clearSearch.length) {
                    $elements.clearSearch.on('click', function() {
                        $elements.searchInput.val('');
                        newsManager.currentFilters.searchQuery = '';
                        newsManager.currentPage = 1;
                        $(this).addClass('hidden');
                        console.log('Clear search clicked');
                        filterAndPaginate();
                    });
                }

                // Filter panel toggle
                if ($elements.filterButton.length) {
                    $elements.filterButton.on('click', function() {
                        $elements.filterPanel.toggleClass('hidden');
                        console.log('Filter panel toggled');
                    });
                }

                // Apply filter button
                if ($elements.applyFilter.length) {
                    $elements.applyFilter.on('click', applyFilters);
                }

                // Cancel filter button
                if ($elements.cancelFilter.length) {
                    $elements.cancelFilter.on('click', function() {
                        $elements.filterPanel.addClass('hidden');
                        console.log('Filter panel closed');
                    });
                }

                // Reset search button
                if ($elements.resetSearch.length) {
                    $elements.resetSearch.on('click', function() {
                        clearAllFilters();
                        $elements.searchInput.val('');
                        newsManager.currentFilters.searchQuery = '';
                        $elements.clearSearch.addClass('hidden');
                        console.log('Reset all filters');
                        filterAndPaginate();
                    });
                }

                // Close panel when clicking outside
                $(document).on('click', function(e) {
                    if (!$elements.filterPanel.is(e.target) &&
                        $elements.filterPanel.has(e.target).length === 0 &&
                        !$elements.filterButton.is(e.target) &&
                        $elements.filterButton.has(e.target).length === 0) {
                        $elements.filterPanel.addClass('hidden');
                    }
                });
            }

            function applyFilters() {
                console.log('Applying filters...');

                const timeRangeRadio = $('input[name="timeRange"]:checked');
                if (timeRangeRadio.length) {
                    newsManager.currentFilters.timeRange = timeRangeRadio.val();
                    console.log('Time range:', newsManager.currentFilters.timeRange);
                }

                $elements.filterPanel.addClass('hidden');
                newsManager.currentPage = 1;
                filterAndPaginate();
            }

            function filterAndPaginate() {
                console.log('Filtering and paginating...');
                showLoading();

                setTimeout(() => {
                    newsManager.filteredNews = filterNews();
                    console.log(
                        `Filtered news: ${newsManager.filteredNews.length} of ${newsManager.allNews.length}`
                    );

                    updateFilteredCount(newsManager.filteredNews.length);

                    newsManager.totalPages = Math.ceil(newsManager.filteredNews.length / newsManager
                        .itemsPerPage);
                    console.log(`Total pages: ${newsManager.totalPages}`);

                    if (newsManager.currentPage > newsManager.totalPages && newsManager.totalPages > 0) {
                        newsManager.currentPage = newsManager.totalPages;
                        console.log(`Adjusted current page to: ${newsManager.currentPage}`);
                    }

                    const paginatedNews = getCurrentPageNews(newsManager.filteredNews);
                    console.log(`Current page news: ${paginatedNews.length}`);

                    renderNews(paginatedNews);
                    renderPagination();
                    hideLoading();

                    console.log('Filter and paginate completed');
                }, 300);
            }

            function filterNews() {
                let filtered = [...newsManager.allNews];

                if (newsManager.currentFilters.searchQuery) {
                    const query = newsManager.currentFilters.searchQuery.toLowerCase();
                    console.log(`Searching for: "${query}"`);

                    filtered = filtered.filter(news => {
                        // Sesuaikan dengan struktur database
                        const title = (news.news_title || '').toLowerCase();
                        const content = (news.news_content || '').toLowerCase();
                        const source = (news.news_source || '').toLowerCase();

                        return title.includes(query) ||
                            content.includes(query) ||
                            source.includes(query);
                    });
                    console.log(`After search: ${filtered.length} news`);
                }

                if (newsManager.currentFilters.timeRange !== 'all') {
                    const now = new Date();
                    filtered = filtered.filter(news => {
                        const dateString = news.created_at;
                        if (!dateString) return true;

                        const newsDate = new Date(dateString);
                        const diffTime = now - newsDate;
                        const diffDays = diffTime / (1000 * 60 * 60 * 24);

                        switch (newsManager.currentFilters.timeRange) {
                            case 'week':
                                return diffDays <= 7;
                            case 'month':
                                return diffDays <= 30;
                            case 'year':
                                return diffDays <= 365;
                            default:
                                return true;
                        }
                    });
                    console.log(`After time filter: ${filtered.length} news`);
                }

                // Urutkan berdasarkan created_at (terbaru pertama)
                filtered.sort((a, b) => {
                    const dateA = new Date(a.created_at || 0);
                    const dateB = new Date(b.created_at || 0);
                    return dateB - dateA; // Descending (terbaru dulu)
                });

                return filtered;
            }

            function getCurrentPageNews(filteredNews) {
                const startIndex = (newsManager.currentPage - 1) * newsManager.itemsPerPage;
                const endIndex = Math.min(startIndex + newsManager.itemsPerPage, filteredNews.length);
                return filteredNews.slice(startIndex, endIndex);
            }

            function updateFilteredCount(count) {
                console.log(`Filtered count: ${count}`);

                if ($elements.newsCount.length) {
                    $elements.newsCount.text(`${count} Berita`);
                }
            }

            function renderNews(news) {
                console.log(`Rendering ${news.length} news...`);

                // Debug: tampilkan struktur data
                if (news.length > 0) {
                    console.log('First item to render:', news[0]);
                }

                if (news.length === 0) {
                    showNoResults();
                    return;
                }

                hideNoResults();

                if ($elements.newsContainer.length) {
                    $elements.newsContainer.empty();
                }

                news.forEach((newsItem, index) => {
                    console.log(`Creating card ${index + 1}:`, {
                        title: newsItem.news_title,
                        hasImage: !!newsItem.news_image,
                        source: newsItem.news_source,
                        date: newsItem.created_at
                    });

                    const newsCard = createNewsCard(newsItem);
                    if ($elements.newsContainer.length) {
                        $elements.newsContainer.append(newsCard);
                    }
                });

                console.log('News rendered');
            }

            function renderPagination() {
                console.log(
                    `Rendering pagination for ${newsManager.totalPages} pages, current page: ${newsManager.currentPage}`
                );

                if (!$elements.paginationContainer.length) {
                    console.error('Pagination container not found!');
                    return;
                }

                if (newsManager.totalPages <= 1 || newsManager.filteredNews.length <= newsManager.itemsPerPage) {
                    $elements.paginationContainer.empty();
                    console.log('No pagination needed (only 1 page)');
                    return;
                }

                $elements.paginationContainer.empty();

                const paginationWrapper = $('<div></div>').addClass(
                    'flex flex-col md:flex-row justify-center items-center gap-4');
                const paginationNav = $('<nav></nav>').addClass('flex items-center space-x-2');

                const prevButton = $('<button></button>')
                    .addClass(
                        'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    )
                    .html('<i class="fas fa-chevron-left"></i>')
                    .prop('disabled', newsManager.currentPage === 1)
                    .on('click', function() {
                        if (newsManager.currentPage > 1) {
                            newsManager.currentPage--;
                            console.log(`Previous page: ${newsManager.currentPage}`);
                            filterAndPaginate();
                        }
                    });
                paginationNav.append(prevButton);

                const maxVisiblePages = 5;
                let startPage = Math.max(1, newsManager.currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(newsManager.totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                console.log(`Page range: ${startPage} to ${endPage}`);

                if (startPage > 1) {
                    const firstPageButton = createPageButton(1);
                    paginationNav.append(firstPageButton);

                    if (startPage > 2) {
                        const ellipsis = $('<span></span>')
                            .addClass('px-2 text-gray-400')
                            .text('...');
                        paginationNav.append(ellipsis);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageButton = createPageButton(i);
                    paginationNav.append(pageButton);
                }

                if (endPage < newsManager.totalPages) {
                    if (endPage < newsManager.totalPages - 1) {
                        const ellipsis = $('<span></span>')
                            .addClass('px-2 text-gray-400')
                            .text('...');
                        paginationNav.append(ellipsis);
                    }

                    const lastPageButton = createPageButton(newsManager.totalPages);
                    paginationNav.append(lastPageButton);
                }

                const nextButton = $('<button></button>')
                    .addClass(
                        'px-3 py-2 rounded-lg border border-gray-300 text-gray-600 lg:hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors'
                    )
                    .html('<i class="fas fa-chevron-right"></i>')
                    .prop('disabled', newsManager.currentPage === newsManager.totalPages)
                    .on('click', function() {
                        if (newsManager.currentPage < newsManager.totalPages) {
                            newsManager.currentPage++;
                            console.log(`Next page: ${newsManager.currentPage}`);
                            filterAndPaginate();
                        }
                    });
                paginationNav.append(nextButton);

                const infoDiv = $('<div></div>').addClass('text-sm text-gray-500');
                const start = (newsManager.currentPage - 1) * newsManager.itemsPerPage + 1;
                const end = Math.min(newsManager.currentPage * newsManager.itemsPerPage, newsManager.filteredNews
                    .length);
                infoDiv.html(
                    `Menampilkan <span class="font-semibold">${start}-${end}</span> dari <span class="font-semibold">${newsManager.filteredNews.length}</span> berita`
                );

                paginationWrapper.append(paginationNav);
                paginationWrapper.append(infoDiv);
                $elements.paginationContainer.append(paginationWrapper);

                console.log('Pagination rendered successfully');
            }

            function createPageButton(pageNumber) {
                const isActive = newsManager.currentPage === pageNumber;
                const button = $('<button></button>')
                    .addClass(
                        `px-3 py-2 rounded-lg transition-colors ${isActive ? 'bg-primary text-white border-primary' : 'border border-gray-300 text-gray-600 lg:hover:bg-gray-50'}`
                    )
                    .text(pageNumber)
                    .on('click', function() {
                        if (newsManager.currentPage !== pageNumber) {
                            newsManager.currentPage = pageNumber;
                            console.log(`Page ${pageNumber} clicked`);
                            filterAndPaginate();
                        }
                    });
                return button;
            }

            function createNewsCard(newsItem) {
                // Gunakan field yang sesuai dengan struktur database
                const title = newsItem.news_title || 'Judul Berita';
                const content = newsItem.news_content || '';
                const image = newsItem.news_image || newsItem.news_avatar || '';
                const slug = newsItem.news_slug || newsItem.uuid || '';
                const date = newsItem.created_at || new Date().toISOString();
                const source = newsItem.news_source || 'Sumber tidak diketahui';
                const newsUrl = newsItem.news_url || '';

                console.log('Creating card with data:', {
                    title: title.substring(0, 30) + '...',
                    hasImage: !!image,
                    imagePath: image,
                    source,
                    date
                });

                let formattedDate = 'Tanggal tidak tersedia';
                try {
                    const newsDate = new Date(date);
                    if (!isNaN(newsDate.getTime())) {
                        formattedDate = newsDate.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });
                    } else {
                        console.warn('Invalid date format:', date);
                    }
                } catch (e) {
                    console.error('Error parsing date:', e);
                }

                const stripHtml = (html) => {
                    if (!html) return '';
                    const div = document.createElement('div');
                    div.innerHTML = html;
                    return div.textContent || div.innerText || '';
                };

                // Ambil excerpt dari content, hapus HTML tags
                const plainContent = stripHtml(content);
                const excerpt = plainContent.substring(0, 120) + (plainContent.length > 120 ? '...' : '');

                const defaultImage =
                    'https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                let imageSrc = defaultImage;

                if (image && image.trim() !== '') {
                    // Perbaikan path image
                    if (image.startsWith('http') || image.startsWith('//')) {
                        imageSrc = image;
                    } else if (image.startsWith('storage/')) {
                        imageSrc = `/storage/${image.replace('storage/', '')}`;
                    } else if (image.startsWith('public/')) {
                        imageSrc = `/storage/${image.replace('public/', '')}`;
                    } else {
                        imageSrc = `/storage/${image}`;
                    }
                    console.log('Image source:', imageSrc);
                }

                // Tentukan URL yang benar
                let targetUrl = '';
                let targetAttr = '_self';

                if (newsUrl) {
                    targetUrl = newsUrl;
                    targetAttr = '_blank';
                } else if (slug) {
                    targetUrl = `/news/${slug}`;
                    targetAttr = '_self';
                } else {
                    targetUrl = '#';
                }

                const card = $('<a></a>')
                    .attr('href', targetUrl)
                    .addClass('block h-full')
                    .attr('target', targetAttr);

                card.html(`
                    <article class="group bg-white rounded-xl overflow-hidden shadow-sm border border-slate-100 flex flex-col h-full animate-fade-in-up lg:hover:-translate-y-1 transition-all duration-300 lg:hover:shadow-xl">
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                            <img src="${imageSrc}"
                                alt="${title}"
                                class="w-full h-full object-cover lg:group-hover:scale-105 transition-transform duration-500"
                                onerror="console.error('Failed to load image:', this.src); this.src='${defaultImage}'">
                            
                            <div class="absolute top-2 left-2 z-10 opacity-50">
                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                    <img src="{{ asset('storage/' . $infos->meta_image) }}"
                                        alt="NUPARIS Logo" class="w-6 h-6">
                                </div>
                            </div>

                            <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-bold text-slate-700">
                                <i class="far fa-calendar-alt mr-1"></i>
                                ${formattedDate}
                            </div>
                            
                            <!-- Overlay gradient untuk readability -->
                            <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>

                        <div class="p-4 flex flex-col flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-red-600 uppercase tracking-wider">
                                    ${source}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-800 mt-1 mb-2 text-sm line-clamp-2 lg:group-hover:text-primary transition">
                                ${title}
                            </h3>

                            <p class="text-slate-500 text-xs line-clamp-3">
                                ${excerpt || 'Tidak ada deskripsi'}
                            </p>
                        </div>
                    </article>
                `);

                return card;
            }

            function showLoading() {
                console.log('Showing loading indicator');
                $elements.loadingIndicator.removeClass('hidden');
                $elements.noResults.addClass('hidden');
                $elements.newsContainer.empty();
                $elements.paginationContainer.empty();
            }

            function hideLoading() {
                console.log('Hiding loading indicator');
                $elements.loadingIndicator.addClass('hidden');
            }

            function showNoResults() {
                console.log('Showing no results message');
                $elements.noResults.removeClass('hidden');
                $elements.newsContainer.empty();
                $elements.paginationContainer.empty();
                $elements.loadingIndicator.addClass('hidden');
            }

            function hideNoResults() {
                $elements.noResults.addClass('hidden');
            }

            function clearAllFilters() {
                console.log('Clearing all filters');

                $('input[name="timeRange"][value="all"]').prop('checked', true);

                newsManager.currentFilters = {
                    timeRange: 'all',
                    searchQuery: ''
                };

                $elements.filterPanel.addClass('hidden');
                newsManager.currentPage = 1;
                filterAndPaginate();
            }
        });
    </script>
</body>

</html>
